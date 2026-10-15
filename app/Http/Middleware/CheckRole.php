<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // If 'siswa' is accepted, also allow 'ketua_kelompok'
        if (in_array('siswa', $roles) && $userRole === 'ketua_kelompok') {
            return $next($request);
        }

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
    }
}
