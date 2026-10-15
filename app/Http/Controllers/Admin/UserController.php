<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->query('role', 'all');
        $search = $request->query('search');

        $query = User::with(['teacher', 'student.schoolClass'])->latest();

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('nis', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function ($tq) use ($search) {
                      $tq->where('teacher_number', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::all();

        $counts = [
            'all' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'siswa' => User::whereIn('role', ['siswa', 'ketua_kelompok'])->count(),
        ];

        return view('admin.users.index', compact('users', 'classes', 'roleFilter', 'search', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,guru,ketua_kelompok,siswa'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            // Student specific
            'nis' => ['nullable', 'required_if:role,siswa,ketua_kelompok', 'string', 'unique:students,nis'],
            'class_id' => ['nullable', 'required_if:role,siswa,ketua_kelompok', 'exists:classes,id'],
            'gender' => ['nullable', 'in:L,P'],
            // Teacher specific
            'teacher_number' => ['nullable', 'required_if:role,guru', 'string', 'unique:teachers,teacher_number'],
            'specialization' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        $user->syncRoles($request->role);

        if (in_array($request->role, ['siswa', 'ketua_kelompok'])) {
            Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'class_id' => $request->class_id,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);
        } elseif ($request->role === 'guru') {
            Teacher::create([
                'user_id' => $user->id,
                'teacher_number' => $request->teacher_number,
                'specialization' => $request->specialization,
            ]);
        }

        AuditLog::record(Auth::id(), 'create_user', "Menambahkan pengguna baru: {$user->name} ({$user->role})");

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,guru,ketua_kelompok,siswa'],
            'status' => ['required', 'in:active,inactive'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles($request->role);

        // Update profile student/teacher
        if (in_array($request->role, ['siswa', 'ketua_kelompok'])) {
            $student = $user->student ?: new Student(['user_id' => $user->id]);
            if ($request->filled('nis')) $student->nis = $request->nis;
            if ($request->filled('class_id')) $student->class_id = $request->class_id;
            if ($request->filled('gender')) $student->gender = $request->gender;
            $student->save();
        } elseif ($request->role === 'guru') {
            $teacher = $user->teacher ?: new Teacher(['user_id' => $user->id]);
            if ($request->filled('teacher_number')) $teacher->teacher_number = $request->teacher_number;
            if ($request->filled('specialization')) $teacher->specialization = $request->specialization;
            $teacher->save();
        }

        AuditLog::record(Auth::id(), 'update_user', "Memperbarui akun pengguna: {$user->name}");

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        AuditLog::record(Auth::id(), 'toggle_user_status', "Mengubah status {$user->name} menjadi {$user->status}");

        return back()->with('success', "Status {$user->name} diubah menjadi {$user->status}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::record(Auth::id(), 'delete_user', "Menghapus akun pengguna: {$userName}");

        return back()->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
