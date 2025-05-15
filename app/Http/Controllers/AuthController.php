<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $identifier = $request->username;
        $password = $request->password;

        // Cek mahasiswa
        $mahasiswa = Mahasiswa::where('nim', $identifier)->first();
        if ($mahasiswa && Hash::check($password, $mahasiswa->password)) {
            Auth::guard('mahasiswa')->login($mahasiswa);
            session(['role' => 'mahasiswa']);
            Log::info('Login berhasil untuk mahasiswa: ' . $mahasiswa->nim);
            return redirect('/usulanbimbingan');
        }

        // Cek dosen
        $dosen = Dosen::where('nip', $identifier)->first();
        if ($dosen && Hash::check($password, $dosen->password)) {
            Auth::guard('dosen')->login($dosen);
            session(['role' => 'dosen']);
            Log::info('Login berhasil untuk dosen: ' . $dosen->nip);
            return redirect('/persetujuan');
        }

        // Jika login gagal
        Log::warning('Login gagal untuk: ' . $identifier);
        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'NIP/NIM atau password salah.'
            ]);
    }

    public function logout(Request $request)
    {
        // Cache control header - penting untuk mengirim ke browser agar tidak menyimpan halaman terproteksi
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

        // Logout dari guard yang aktif
        if (Auth::guard('mahasiswa')->check()) {
            Auth::guard('mahasiswa')->logout();
        } else if (Auth::guard('dosen')->check()) {
            Auth::guard('dosen')->logout();
        }

        // Invalidasi sesi dan regenerasi token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set cookie dengan nilai berbeda untuk memaksa browser menghapus cache
        $response = redirect()->route('login');
        $response->withCookie(cookie('LOGOUT_CACHE_BUSTER', uniqid(), 30));

        // Set header anti-cache tambahan
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
