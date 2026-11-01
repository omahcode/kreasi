<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\WargaRplService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSyncController extends Controller
{
    public function __construct(
        protected WargaRplService $service
    ) {}

    /**
     * Display the WargaRPL API Sync page.
     */
    public function index(Request $request)
    {
        $token = $request->query('token') ?: config('services.wargarpl.token');
        $maskedToken = $this->service->getMaskedToken($token);

        $localStudentCount = Student::count();
        $localTeacherCount = Teacher::count();
        $localClassCount = SchoolClass::count();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $activeTab = $request->query('tab', 'students');

        $recentSyncLogs = AuditLog::whereIn('action', ['sync_wargarpl', 'sync_wargarpl_teachers'])
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.students.sync', [
            'baseUrl' => $this->service->getBaseUrl(),
            'token' => $token,
            'maskedToken' => $maskedToken,
            'localStudentCount' => $localStudentCount,
            'localTeacherCount' => $localTeacherCount,
            'localClassCount' => $localClassCount,
            'activeAcademicYear' => $activeAcademicYear,
            'recentSyncLogs' => $recentSyncLogs,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Test connection to WargaRPL API via AJAX.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $token = $request->input('token') ?: config('services.wargarpl.token');
        $result = $this->service->testConnection($token);

        return response()->json($result);
    }

    /**
     * Preview student data from WargaRPL API before running sync.
     */
    public function preview(Request $request)
    {
        $token = $request->input('token') ?: config('services.wargarpl.token');
        $classFilter = $request->input('class_filter');

        try {
            $queryParams = [];
            if ($classFilter) {
                $queryParams['search'] = $classFilter;
            }

            // Fetch first page (up to 50 items) for preview
            $response = $this->service->fetchStudentsPage(1, 50, $token, $queryParams);
            $items = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            // Existing local NIS array
            $existingNisList = Student::pluck('nis')->flip()->toArray();

            $previewItems = [];
            $newCount = 0;
            $updateCount = 0;

            foreach ($items as $item) {
                $nis = (string) ($item['nis'] ?? '');
                $exists = isset($existingNisList[$nis]);

                if ($exists) {
                    $updateCount++;
                } else {
                    $newCount++;
                }

                $birthDate = ! empty($item['birth_date']) ? Carbon::parse($item['birth_date']) : null;
                $autoPassword = $birthDate ? $birthDate->format('dmY') : $nis;

                $previewItems[] = [
                    'nis' => $nis,
                    'nisn' => $item['nisn'] ?? '-',
                    'full_name' => $item['full_name'] ?? '-',
                    'gender' => $item['gender'] ?? '-',
                    'class_name' => $item['class']['name'] ?? '-',
                    'birth_date' => $birthDate ? $birthDate->format('d-m-Y') : null,
                    'auto_password' => $autoPassword,
                    'is_existing' => $exists,
                    'status' => $item['status'] ?? 'Aktif',
                ];
            }

            return view('admin.students.sync', [
                'baseUrl' => $this->service->getBaseUrl(),
                'token' => $token,
                'maskedToken' => $this->service->getMaskedToken($token),
                'localStudentCount' => Student::count(),
                'localTeacherCount' => Teacher::count(),
                'localClassCount' => SchoolClass::count(),
                'activeAcademicYear' => AcademicYear::where('is_active', true)->first(),
                'recentSyncLogs' => AuditLog::whereIn('action', ['sync_wargarpl', 'sync_wargarpl_teachers'])->latest()->take(8)->get(),
                'previewItems' => $previewItems,
                'previewMeta' => $meta,
                'previewNewCount' => $newCount,
                'previewUpdateCount' => $updateCount,
                'classFilter' => $classFilter,
                'activeTab' => 'students',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.students.sync', ['tab' => 'students'])
                ->with('error', 'Gagal memuat pratinjau data siswa API: '.$e->getMessage());
        }
    }

    /**
     * Execute full synchronization of students from WargaRPL API.
     */
    public function sync(Request $request)
    {
        $token = $request->input('token') ?: config('services.wargarpl.token');

        if (empty($token)) {
            return redirect()->route('admin.students.sync', ['tab' => 'students'])
                ->with('error', 'Token API WargaRPL tidak boleh kosong.');
        }

        $result = $this->service->syncStudents($token);

        if (! $result['success']) {
            return redirect()->route('admin.students.sync')
                ->with('error', 'Sinkronisasi gagal: '.($result['message'] ?? 'Terjadi kesalahan sistem.'));
        }

        $msg = sprintf(
            'Sinkronisasi WargaRPL Berhasil! Total %d siswa diproses (%d siswa baru ditambahkan, %d siswa diperbarui, %d kelas disinkronkan). Kata sandi akun siswa otomatis diatur ke Tanggal Lahir (DDMMYYYY) atau NIS.',
            $result['total_fetched'] ?? 0,
            $result['created_count'] ?? 0,
            $result['updated_count'] ?? 0,
            $result['classes_created'] ?? 0
        );

        return redirect()->route('admin.students.sync')->with('success', $msg);
    }

    /**
     * Preview teacher / employee data from WargaRPL API before running sync.
     */
    public function previewTeachers(Request $request)
    {
        $token = $request->input('token') ?: config('services.wargarpl.token');
        $searchFilter = $request->input('search_filter');

        try {
            $queryParams = [];
            if ($searchFilter) {
                $queryParams['search'] = $searchFilter;
            }

            // Fetch first page of employees for preview
            $response = $this->service->fetchEmployeesPage(1, 50, $token, $queryParams);
            $items = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];

            // Existing local teacher numbers
            $existingTeacherNumbers = Teacher::pluck('teacher_number')->flip()->toArray();

            $previewTeacherItems = [];
            $newCount = 0;
            $updateCount = 0;

            foreach ($items as $item) {
                $nipRaw = trim((string) ($item['nip'] ?? ''));
                $nipClean = preg_replace('/\D/', '', $nipRaw);
                $phone = trim($item['phone'] ?? $item['whatsapp'] ?? '');
                $teacherNumber = $nipClean ?: ($phone ? preg_replace('/\D/', '', $phone) : ('GURU-'.substr($item['uuid'] ?? uniqid(), 0, 8)));

                $exists = isset($existingTeacherNumbers[$teacherNumber]);
                if ($exists) {
                    $updateCount++;
                } else {
                    $newCount++;
                }

                $passInfo = $this->service->extractTeacherBirthAndPass($nipClean, $phone);

                $previewTeacherItems[] = [
                    'nip' => $nipRaw ?: '-',
                    'teacher_number' => $teacherNumber,
                    'full_name' => $item['full_name'] ?? '-',
                    'ptk_type' => $item['ptk_type'] ?? 'Guru',
                    'employment_type' => $item['employment_type'] ?? '-',
                    'phone' => $phone ?: '-',
                    'email' => $item['email'] ?? ($teacherNumber.'@guru.kreasi.test'),
                    'birth_date' => $passInfo['dob'],
                    'auto_password' => $passInfo['password'],
                    'is_nip_standard' => $passInfo['is_nip_standard'],
                    'is_existing' => $exists,
                    'status' => $item['status'] ?? 'Aktif',
                ];
            }

            return view('admin.students.sync', [
                'baseUrl' => $this->service->getBaseUrl(),
                'token' => $token,
                'maskedToken' => $this->service->getMaskedToken($token),
                'localStudentCount' => Student::count(),
                'localTeacherCount' => Teacher::count(),
                'localClassCount' => SchoolClass::count(),
                'activeAcademicYear' => AcademicYear::where('is_active', true)->first(),
                'recentSyncLogs' => AuditLog::whereIn('action', ['sync_wargarpl', 'sync_wargarpl_teachers'])->latest()->take(8)->get(),
                'previewTeacherItems' => $previewTeacherItems,
                'previewTeacherMeta' => $meta,
                'previewTeacherNewCount' => $newCount,
                'previewTeacherUpdateCount' => $updateCount,
                'searchFilter' => $searchFilter,
                'activeTab' => 'teachers',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.students.sync', ['tab' => 'teachers'])
                ->with('error', 'Gagal memuat pratinjau data guru API: '.$e->getMessage());
        }
    }

    /**
     * Execute full synchronization of teachers from WargaRPL API.
     */
    public function syncTeachers(Request $request)
    {
        $token = $request->input('token') ?: config('services.wargarpl.token');

        if (empty($token)) {
            return redirect()->route('admin.students.sync', ['tab' => 'teachers'])
                ->with('error', 'Token API WargaRPL tidak boleh kosong.');
        }

        $resetPasswords = $request->boolean('reset_passwords');

        $result = $this->service->syncTeachers($token, [
            'reset_passwords' => $resetPasswords,
        ]);

        if (! $result['success']) {
            return redirect()->route('admin.students.sync', ['tab' => 'teachers'])
                ->with('error', 'Sinkronisasi guru gagal: '.($result['message'] ?? 'Terjadi kesalahan sistem.'));
        }

        $msg = sprintf(
            'Sinkronisasi Guru WargaRPL Berhasil! Total %d guru diproses (%d guru baru ditambahkan, %d guru diperbarui). Akun guru login menggunakan NIP dengan kata sandi Tanggal Lahir (DDMMYYYY).',
            $result['total_fetched'] ?? 0,
            $result['created_count'] ?? 0,
            $result['updated_count'] ?? 0
        );

        return redirect()->route('admin.students.sync', ['tab' => 'teachers'])->with('success', $msg);
    }
}
