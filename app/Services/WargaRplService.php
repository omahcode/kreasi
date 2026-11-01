<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WargaRplService
{
    protected string $baseUrl;

    protected ?string $token;

    protected string $emailDomain;

    public function __construct(?string $baseUrl = null, ?string $token = null)
    {
        $this->baseUrl = rtrim($baseUrl ?: config('services.wargarpl.base_url', 'https://wargarpl.neperone.id'), '/');
        $this->token = $token ?: config('services.wargarpl.token');
        $this->emailDomain = config('services.wargarpl.student_email_domain', 'siswa.kreasi.test');
    }

    /**
     * Get Base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get API Token.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set API Token dynamically.
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Return masked token for display (e.g. wgr_live_ElQ...DpcQ).
     */
    public function getMaskedToken(?string $token = null): string
    {
        $tok = $token ?: $this->token;
        if (empty($tok)) {
            return '-';
        }

        if (strlen($tok) <= 16) {
            return substr($tok, 0, 4).'...'.substr($tok, -4);
        }

        return substr($tok, 0, 12).'...'.substr($tok, -6);
    }

    /**
     * Build HTTP client with authorization header and default timeouts.
     */
    protected function client(?string $token = null)
    {
        $activeToken = $token ?: $this->token;

        return Http::baseUrl($this->baseUrl)
            ->withToken($activeToken)
            ->acceptJson()
            ->timeout(20);
    }

    /**
     * Test connection to WargaRPL API.
     */
    public function testConnection(?string $token = null): array
    {
        try {
            $response = $this->client($token)->get('/api/v1/students', ['per_page' => 1]);

            if ($response->successful()) {
                $body = $response->json();
                $totalStudents = $body['meta']['total'] ?? 0;

                // Check classes
                $classesResponse = $this->client($token)->get('/api/v1/classes');
                $classesCount = $classesResponse->successful() ? count($classesResponse->json('data') ?? []) : 0;

                // Check employees / teachers
                $empResponse = $this->client($token)->get('/api/v1/employees', ['per_page' => 1]);
                $totalTeachers = $empResponse->successful() ? ($empResponse->json('meta.total') ?? count($empResponse->json('data') ?? [])) : 0;

                return [
                    'success' => true,
                    'total_students' => $totalStudents,
                    'total_teachers' => $totalTeachers,
                    'classes_count' => $classesCount,
                    'message' => "Koneksi berhasil! Terhubung ke WargaRPL API ({$totalStudents} siswa, {$totalTeachers} guru & tendik, {$classesCount} rombel terdeteksi).",
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->json('message') ?? 'HTTP '.$response->status().': '.$response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('WargaRplService connection test error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => 'Gagal menghubungi server WargaRPL: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Fetch all school classes from API.
     */
    public function fetchClasses(?string $token = null): array
    {
        try {
            $response = $this->client($token)->get('/api/v1/classes');

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::warning('WargaRplService fetchClasses failed: '.$response->body());

            return [];
        } catch (\Exception $e) {
            Log::error('WargaRplService fetchClasses exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Fetch a paginated page of students.
     */
    public function fetchStudentsPage(int $page = 1, int $perPage = 50, ?string $token = null, array $queryParams = []): array
    {
        $params = array_merge([
            'page' => $page,
            'per_page' => $perPage,
        ], $queryParams);

        $response = $this->client($token)->get('/api/v1/students', $params);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        throw new \RuntimeException('Gagal mengambil data siswa: '.($response->json('message') ?? 'Status HTTP '.$response->status()));
    }

    /**
     * Fetch all students across all pagination pages.
     */
    public function fetchAllStudents(?string $token = null, array $queryParams = []): array
    {
        $allStudents = [];
        $page = 1;
        $perPage = 50;

        do {
            $response = $this->fetchStudentsPage($page, $perPage, $token, $queryParams);
            $items = $response['data'] ?? [];
            $allStudents = array_merge($allStudents, $items);

            $meta = $response['meta'] ?? [];
            $lastPage = $meta['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        return $allStudents;
    }

    /**
     * Synchronize classes and students from WargaRPL into KREASI RPL database.
     */
    public function syncStudents(?string $token = null, array $options = []): array
    {
        $apiToken = $token ?: $this->token;
        if (empty($apiToken)) {
            return [
                'success' => false,
                'message' => 'Token API WargaRPL belum diatur.',
            ];
        }

        // 1. Ensure Active Academic Year exists
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (! $academicYear) {
            $academicYear = AcademicYear::firstOrCreate(
                ['name' => '2026/2027'],
                ['is_active' => true]
            );
        }

        // 2. Fetch classes from API & sync them into local classes table
        $apiClasses = $this->fetchClasses($apiToken);
        $classesCreated = 0;
        $classMap = []; // Name in lower => SchoolClass model

        // Load existing classes
        $existingClasses = SchoolClass::all();
        foreach ($existingClasses as $c) {
            $classMap[strtolower(trim($c->name))] = $c;
        }

        foreach ($apiClasses as $ac) {
            $cName = trim($ac['name'] ?? '');
            if (! $cName) {
                continue;
            }

            $cKey = strtolower($cName);
            if (! isset($classMap[$cKey])) {
                $gradeLevel = $this->resolveGradeLevel($ac['grade'] ?? null, $cName);
                $newClass = SchoolClass::create([
                    'name' => $cName,
                    'grade_level' => $gradeLevel,
                    'academic_year_id' => $academicYear->id,
                ]);
                $classMap[$cKey] = $newClass;
                $classesCreated++;
            }
        }

        // 3. Fetch all students from API
        try {
            $studentsData = $this->fetchAllStudents($apiToken, $options['query'] ?? []);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data dari API: '.$e->getMessage(),
            ];
        }

        $createdCount = 0;
        $updatedCount = 0;
        $errorList = [];

        DB::beginTransaction();
        try {
            foreach ($studentsData as $item) {
                $nis = trim((string) ($item['nis'] ?? ''));
                $nisn = trim((string) ($item['nisn'] ?? ''));
                $fullName = trim((string) ($item['full_name'] ?? ''));
                $gender = trim((string) ($item['gender'] ?? ''));
                $birthDate = ! empty($item['birth_date']) ? Carbon::parse($item['birth_date'])->format('Y-m-d') : null;
                $className = trim((string) ($item['class']['name'] ?? ''));
                $statusApi = trim((string) ($item['status'] ?? 'Aktif'));
                $userStatus = strcasecmp($statusApi, 'Aktif') === 0 ? 'active' : 'inactive';

                if (empty($nis) || empty($fullName)) {
                    continue;
                }

                // Match class
                $matchedClass = null;
                if (! empty($className)) {
                    $cKey = strtolower($className);
                    if (isset($classMap[$cKey])) {
                        $matchedClass = $classMap[$cKey];
                    } else {
                        // Create on the fly if not in class list
                        $gradeLevel = $this->resolveGradeLevel(null, $className);
                        $matchedClass = SchoolClass::create([
                            'name' => $className,
                            'grade_level' => $gradeLevel,
                            'academic_year_id' => $academicYear->id,
                        ]);
                        $classMap[$cKey] = $matchedClass;
                        $classesCreated++;
                    }
                }

                if (! $matchedClass) {
                    $matchedClass = $existingClasses->first();
                }

                // Check if student exists by NIS
                $student = Student::with('user')->where('nis', $nis)->first();

                if ($student) {
                    // Update student
                    $student->class_id = $matchedClass ? $matchedClass->id : $student->class_id;
                    $student->gender = $gender ?: $student->gender;
                    $student->birth_date = $birthDate ?: $student->birth_date;
                    if (! empty($nisn)) {
                        $student->nisn = $nisn;
                    }
                    $student->save();

                    // Update corresponding user
                    if ($student->user) {
                        $student->user->name = $fullName;
                        $student->user->status = $userStatus;
                        $student->user->save();
                    }

                    $updatedCount++;
                } else {
                    // Check if User exists by email or NIS-like identifier
                    $desiredEmail = $nis.'@'.$this->emailDomain;
                    $user = User::where('email', $desiredEmail)->first();

                    if (! $user) {
                        // Generate initial password from birth date (DDMMYYYY) or NIS
                        $plainPassword = $birthDate ? Carbon::parse($birthDate)->format('dmY') : $nis;

                        $user = User::create([
                            'name' => $fullName,
                            'email' => $desiredEmail,
                            'password' => Hash::make($plainPassword),
                            'role' => 'siswa',
                            'status' => $userStatus,
                        ]);

                        $user->syncRoles('siswa');
                    } else {
                        $user->name = $fullName;
                        $user->status = $userStatus;
                        $user->save();
                    }

                    Student::create([
                        'user_id' => $user->id,
                        'nis' => $nis,
                        'nisn' => $nisn ?: null,
                        'class_id' => $matchedClass->id,
                        'gender' => $gender ?: null,
                        'birth_date' => $birthDate,
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WargaRplService sync error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data sinkronisasi: '.$e->getMessage(),
            ];
        }

        // Record audit log
        $actorId = Auth::id() ?? User::where('role', 'admin')->value('id') ?? 1;
        AuditLog::record(
            $actorId,
            'sync_wargarpl',
            "Sinkronisasi WargaRPL berhasil: {$createdCount} siswa baru ditambahkan, {$updatedCount} siswa diperbarui, {$classesCreated} kelas disinkronkan."
        );

        return [
            'success' => true,
            'total_fetched' => count($studentsData),
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
            'classes_created' => $classesCreated,
            'errors' => $errorList,
            'message' => 'Berhasil menyinkronkan '.count($studentsData)." siswa dari WargaRPL ({$createdCount} siswa baru, {$updatedCount} diperbarui).",
        ];
    }

    /**
     * Resolve grade level string ('X', 'XI', 'XII') from grade number or class name.
     */
    protected function resolveGradeLevel(?int $grade, string $className): string
    {
        if ($grade === 10) {
            return 'X';
        }
        if ($grade === 11) {
            return 'XI';
        }
        if ($grade === 12) {
            return 'XII';
        }

        $cUpper = strtoupper($className);
        if (str_starts_with($cUpper, 'XII') || str_starts_with($cUpper, '12')) {
            return 'XII';
        }
        if (str_starts_with($cUpper, 'XI') || str_starts_with($cUpper, '11')) {
            return 'XI';
        }
        if (str_starts_with($cUpper, 'X') || str_starts_with($cUpper, '10')) {
            return 'X';
        }

        return 'X';
    }

    /**
     * Extract Date of Birth and default password (DDMMYYYY) from 18-digit NIP.
     * NIP standard format: YYYYMMDD... -> password = DDMMYYYY.
     */
    public function extractTeacherBirthAndPass(string $nip, ?string $phone = null): array
    {
        $nipClean = preg_replace('/\D/', '', $nip);

        if (strlen($nipClean) >= 8) {
            $yyyy = substr($nipClean, 0, 4);
            $mm = substr($nipClean, 4, 2);
            $dd = substr($nipClean, 6, 2);

            if (checkdate((int) $mm, (int) $dd, (int) $yyyy)) {
                $formattedDob = sprintf('%02d-%02d-%04d', (int) $dd, (int) $mm, (int) $yyyy);
                $plainPassword = sprintf('%02d%02d%04d', (int) $dd, (int) $mm, (int) $yyyy);

                return [
                    'dob' => $formattedDob,
                    'password' => $plainPassword,
                    'is_nip_standard' => true,
                ];
            }
        }

        $fallback = $phone ? preg_replace('/\D/', '', $phone) : ($nipClean ?: 'password');

        return [
            'dob' => null,
            'password' => $fallback,
            'is_nip_standard' => false,
        ];
    }

    /**
     * Fetch a paginated page of employees / teachers.
     */
    public function fetchEmployeesPage(int $page = 1, int $perPage = 50, ?string $token = null, array $queryParams = []): array
    {
        $params = array_merge([
            'page' => $page,
            'per_page' => $perPage,
        ], $queryParams);

        $response = $this->client($token)->get('/api/v1/employees', $params);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        throw new \RuntimeException('Gagal mengambil data guru: '.($response->json('message') ?? 'Status HTTP '.$response->status()));
    }

    /**
     * Fetch all employees across all pagination pages.
     */
    public function fetchAllEmployees(?string $token = null, array $queryParams = []): array
    {
        $allEmployees = [];
        $page = 1;
        $perPage = 50;

        do {
            $response = $this->fetchEmployeesPage($page, $perPage, $token, $queryParams);
            $items = $response['data'] ?? [];
            $allEmployees = array_merge($allEmployees, $items);

            $meta = $response['meta'] ?? [];
            $lastPage = $meta['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        return $allEmployees;
    }

    /**
     * Synchronize teachers from WargaRPL into KREASI RPL database.
     * Sets password to DDMMYYYY from NIP (YYYYMMDD) and allows login with NIP.
     */
    public function syncTeachers(?string $token = null, array $options = []): array
    {
        $apiToken = $token ?: $this->token;
        if (empty($apiToken)) {
            return [
                'success' => false,
                'message' => 'Token API WargaRPL belum diatur.',
            ];
        }

        try {
            $employeesData = $this->fetchAllEmployees($apiToken, $options['query'] ?? []);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data dari API: '.$e->getMessage(),
            ];
        }

        $createdCount = 0;
        $updatedCount = 0;
        $resetPasswords = $options['reset_passwords'] ?? false;

        DB::beginTransaction();
        try {
            foreach ($employeesData as $item) {
                $nipRaw = trim((string) ($item['nip'] ?? ''));
                $nipClean = preg_replace('/\D/', '', $nipRaw);
                $phone = trim($item['phone'] ?? $item['whatsapp'] ?? '');
                $fullName = trim((string) ($item['full_name'] ?? 'Guru'));

                if (empty($fullName)) {
                    continue;
                }

                // If NIP is present, teacher_number is the NIP. Otherwise fallback to phone or UUID.
                $teacherNumber = $nipClean ?: ($phone ? preg_replace('/\D/', '', $phone) : ('GURU-'.substr($item['uuid'] ?? uniqid(), 0, 8)));

                // Extract birth date and DDMMYYYY password
                $extracted = $this->extractTeacherBirthAndPass($nipClean, $phone);
                $plainPassword = $extracted['password'];

                $statusApi = trim((string) ($item['status'] ?? 'Aktif'));
                $userStatus = strcasecmp($statusApi, 'Aktif') === 0 ? 'active' : 'inactive';

                $specialization = trim($item['primary_subject'] ?? $item['ptk_type'] ?? 'Guru Produktif');

                // Determine email
                $email = trim((string) ($item['email'] ?? ''));
                if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = $teacherNumber.'@guru.kreasi.test';
                }

                // Check if teacher exists by teacher_number
                $teacher = Teacher::with('user')->where('teacher_number', $teacherNumber)->first();

                if ($teacher) {
                    $teacher->specialization = $specialization ?: $teacher->specialization;
                    $teacher->save();

                    if ($teacher->user) {
                        $teacher->user->name = $fullName;
                        $teacher->user->status = $userStatus;
                        if (! empty($phone)) {
                            $teacher->user->phone = $phone;
                        }
                        if ($resetPasswords) {
                            $teacher->user->password = Hash::make($plainPassword);
                        }
                        $teacher->user->save();
                    }

                    $updatedCount++;
                } else {
                    // Check if User exists by email
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        // If this existing user is already linked to another teacher or student, avoid collision
                        if ($existingUser->role !== 'guru') {
                            $email = $teacherNumber.'@guru.kreasi.test';
                            $user = User::create([
                                'name' => $fullName,
                                'email' => $email,
                                'phone' => $phone ?: null,
                                'password' => Hash::make($plainPassword),
                                'role' => 'guru',
                                'status' => $userStatus,
                            ]);
                            $user->syncRoles('guru');
                        } else {
                            $user = $existingUser;
                            $user->name = $fullName;
                            $user->status = $userStatus;
                            if ($resetPasswords) {
                                $user->password = Hash::make($plainPassword);
                            }
                            $user->save();
                        }
                    } else {
                        $user = User::create([
                            'name' => $fullName,
                            'email' => $email,
                            'phone' => $phone ?: null,
                            'password' => Hash::make($plainPassword),
                            'role' => 'guru',
                            'status' => $userStatus,
                        ]);
                        $user->syncRoles('guru');
                    }

                    Teacher::create([
                        'user_id' => $user->id,
                        'teacher_number' => $teacherNumber,
                        'specialization' => $specialization,
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WargaRplService syncTeachers error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data guru: '.$e->getMessage(),
            ];
        }

        // Record audit log
        $actorId = Auth::id() ?? User::where('role', 'admin')->value('id') ?? 1;
        AuditLog::record(
            $actorId,
            'sync_wargarpl_teachers',
            "Sinkronisasi Guru WargaRPL berhasil: {$createdCount} guru baru ditambahkan, {$updatedCount} guru diperbarui."
        );

        return [
            'success' => true,
            'total_fetched' => count($employeesData),
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
            'message' => 'Berhasil menyinkronkan '.count($employeesData)." guru dari WargaRPL ({$createdCount} guru baru, {$updatedCount} diperbarui).",
        ];
    }
}
