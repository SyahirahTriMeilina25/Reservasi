<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        Log::info('=== Debug CheckRole Middleware ===');
        Log::info('Middleware CheckRole dipanggil');
        Log::info('Role yang diminta: ' . $role);
        Log::info('Current Path: ' . $request->path());

        // Cek autentikasi untuk kedua guard
        $mahasiswaAuth = Auth::guard('mahasiswa')->check();
        $dosenAuth = Auth::guard('dosen')->check();
        
        Log::info('Mahasiswa Auth: ' . ($mahasiswaAuth ? 'true' : 'false'));
        Log::info('Dosen Auth: ' . ($dosenAuth ? 'true' : 'false'));

        if (!$mahasiswaAuth && !$dosenAuth) {
            Log::info('Tidak ada user yang login');
            return redirect()->route('login');
        }

        // Ambil user yang sedang login
        $user = $mahasiswaAuth ? Auth::guard('mahasiswa')->user() 
                              : Auth::guard('dosen')->user();
        
        Log::info('User yang login: ' . ($user ? $user->nim ?? $user->nip : 'none'));
        
        try {
            // Load relasi role
            $userRole = $user->role;
            
            if (!$userRole) {
                Log::error('Role tidak ditemukan untuk user');
                abort(403, 'Role tidak ditemukan');
            }

            Log::info('Role user: ' . $userRole->role_akses);
            
            // Cek apakah role_akses sesuai
            if ($userRole->role_akses !== $role) {
                Log::info('Role tidak sesuai');
                abort(403, 'Unauthorized access');
            }
            
            Log::info('Role sesuai, akses diberikan');
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error saat cek role: ' . $e->getMessage());
            abort(403, 'Unauthorized access');
        }
    }
}