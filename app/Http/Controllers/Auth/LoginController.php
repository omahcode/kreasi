<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email, NIS, atau NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Check whether loginInput is email, NIS, or teacher number
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            // Check student NIS
            $student = Student::where('nis', $loginInput)->first();
            if ($student) {
                $user = $student->user;
            } else {
                // Check teacher number
                $teacher = Teacher::where('teacher_number', $loginInput)->first();
                if ($teacher) {
                    $user = $teacher->user;
                }
            }
        }

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            if ($user->status !== 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => ['Akun Anda berstatus non-aktif. Silakan hubungi admin sekolah.'],
                ]);
            }

            // Record audit log
            AuditLog::record($user->id, 'login', 'Pengguna berhasil masuk ke sistem (' . $user->role . ')');

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'login' => ['Kredensial yang dimasukkan tidak cocok dengan data kami.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record(Auth::id(), 'logout', 'Pengguna keluar dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar.');
    }

    /**
     * Redirect authenticated user based on role.
     */
    public function redirectBasedOnRole(User $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'guru':
                return redirect()->intended(route('guru.dashboard'));
            case 'ketua_kelompok':
            case 'siswa':
            default:
                return redirect()->intended(route('siswa.dashboard'));
        }
    }
}
