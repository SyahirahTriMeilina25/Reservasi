<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;
use Exception;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $identifier = $request->username;
            $password = $request->password;

            // Cek admin
            $admin = Admin::where('username', $identifier)->first();
            if ($admin && Hash::check($password, $admin->password)) {
                // Periksa role dari database
                $roleData = DB::table('role')->where('id', $admin->role_id ?? 0)->first();

                // Jika role admin tidak ditemukan, gunakan strategi fallback
                if (!$roleData) {
                    $adminRoleId = DB::table('role')->where('role_akses', 'admin')->value('id');

                    if ($adminRoleId) {
                        // Update admin dengan role admin yang tersedia
                        DB::table('admin')
                            ->where('id', $admin->id)
                            ->update(['role_id' => $adminRoleId]);

                        $roleData = DB::table('role')->where('id', $adminRoleId)->first();
                    } else {
                        // Buat role admin baru jika tidak ada
                        $adminRoleId = DB::table('role')->insertGetId([
                            'role_akses' => 'admin',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Update admin
                        DB::table('admin')
                            ->where('id', $admin->id)
                            ->update(['role_id' => $adminRoleId]);

                        $roleData = DB::table('role')->where('id', $adminRoleId)->first();
                    }
                }

                // Login admin dengan parameter remember me = true
                Auth::guard('admin')->login($admin, false);

                // Simpan informasi penting ke session
                session([
                    'login_admin_' . md5('admin') => 1, // Tambahkan key session dengan prefix
                    'role' => 'admin',
                    'role_akses' => $roleData ? $roleData->role_akses : 'admin',
                    'role_id' => $admin->role_id,
                    'user_id' => $admin->id,
                    'user_name' => $admin->nama ?? 'Administrator',
                    'username' => $admin->username
                ]);

                // Regenerate session ID untuk mencegah session fixation
                $request->session()->regenerate();

                // Paksa save session untuk memastikan data disimpan
                session()->save();

                // Log login sukses untuk debugging
                Log::info('Admin login successful', [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'session_id' => session()->getId()
                ]);

                return redirect()->route('admin.dashboard');
            }

            // Cek mahasiswa
            $mahasiswa = Mahasiswa::where('nim', $identifier)->first();
            if ($mahasiswa && Hash::check($password, $mahasiswa->password)) {
                // Periksa role dari database
                $roleData = DB::table('role')->where('id', $mahasiswa->role_id ?? 0)->first();

                // Jika role mahasiswa tidak ditemukan, gunakan strategi fallback
                if (!$roleData) {
                    $mahasiswaRoleId = DB::table('role')->where('role_akses', 'mahasiswa')->value('id');

                    if ($mahasiswaRoleId) {
                        // Update mahasiswa dengan role yang tersedia
                        DB::table('mahasiswas')
                            ->where('id', $mahasiswa->id)
                            ->update(['role_id' => $mahasiswaRoleId]);

                        $roleData = DB::table('role')->where('id', $mahasiswaRoleId)->first();
                    } else {
                        // Buat role mahasiswa baru jika tidak ada
                        $mahasiswaRoleId = DB::table('role')->insertGetId([
                            'role_akses' => 'mahasiswa',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Update mahasiswa
                        DB::table('mahasiswas')
                            ->where('id', $mahasiswa->id)
                            ->update(['role_id' => $mahasiswaRoleId]);

                        $roleData = DB::table('role')->where('id', $mahasiswaRoleId)->first();
                    }
                }

                // Login mahasiswa dengan parameter remember me = true
                Auth::guard('mahasiswa')->login($mahasiswa, false);

                // Simpan informasi penting ke session
                session([
                    'login_mahasiswa_' . md5('mahasiswa') => 1, // Tambahkan key session dengan prefix
                    'role' => 'mahasiswa',
                    'role_akses' => $roleData ? $roleData->role_akses : 'mahasiswa',
                    'role_id' => $mahasiswa->role_id,
                    'user_id' => $mahasiswa->id,
                    'user_name' => $mahasiswa->nama ?? 'Mahasiswa',
                    'nim' => $mahasiswa->nim
                ]);

                // Regenerate session ID untuk mencegah session fixation
                $request->session()->regenerate();

                // Paksa save session untuk memastikan data disimpan
                session()->save();

                // Log login sukses untuk debugging
                Log::info('Mahasiswa login successful', [
                    'id' => $mahasiswa->id,
                    'nim' => $mahasiswa->nim,
                    'session_id' => session()->getId()
                ]);

                return redirect('/usulanbimbingan');
            }

            // Cek dosen
            $dosen = Dosen::where('nip', $identifier)->first();
            if ($dosen && Hash::check($password, $dosen->password)) {
                // Periksa role dari database
                $roleData = DB::table('role')->where('id', $dosen->role_id ?? 0)->first();

                // Jika role dosen tidak ditemukan, gunakan strategi fallback
                if (!$roleData) {
                    $dosenRoleId = DB::table('role')->where('role_akses', 'dosen')->value('id');

                    if ($dosenRoleId) {
                        // Update dosen dengan role yang tersedia
                        DB::table('dosens')
                            ->where('id', $dosen->id)
                            ->update(['role_id' => $dosenRoleId]);

                        $roleData = DB::table('role')->where('id', $dosenRoleId)->first();
                    } else {
                        // Buat role dosen baru jika tidak ada
                        $dosenRoleId = DB::table('role')->insertGetId([
                            'role_akses' => 'dosen',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Update dosen
                        DB::table('dosens')
                            ->where('id', $dosen->id)
                            ->update(['role_id' => $dosenRoleId]);

                        $roleData = DB::table('role')->where('id', $dosenRoleId)->first();
                    }
                }

                // Login dosen dengan parameter remember me = true
                Auth::guard('dosen')->login($dosen, false);

                // Simpan informasi penting ke session
                session([
                    'login_dosen_' . md5('dosen') => 1, // Tambahkan key session dengan prefix
                    'role' => 'dosen',
                    'role_akses' => $roleData ? $roleData->role_akses : 'dosen',
                    'role_id' => $dosen->role_id,
                    'user_id' => $dosen->id,
                    'user_name' => $dosen->nama ?? 'Dosen',
                    'nip' => $dosen->nip
                ]);

                // Regenerate session ID untuk mencegah session fixation
                $request->session()->regenerate();

                // Paksa save session untuk memastikan data disimpan
                session()->save();

                // Log login sukses untuk debugging
                Log::info('Dosen login successful', [
                    'id' => $dosen->id,
                    'nip' => $dosen->nip,
                    'session_id' => session()->getId()
                ]);

                return redirect('/persetujuan');
            }

            // Jika login gagal, log untuk debugging
            Log::warning('Login failed', [
                'identifier' => $identifier,
                'ip' => $request->ip()
            ]);

            // Jika login gagal
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'login' => 'Username/NIP/NIM atau password salah.'
                ]);
        } catch (Exception $e) {
            // Log error
            Log::error('Login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mode debug untuk development: tampilkan error detail
            if (config('app.debug') && !$request->expectsJson() && !$request->ajax()) {
                echo "<h2>Error Debug</h2>";
                echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
                echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                echo "<p><a href=\"javascript:history.back()\">Kembali</a></p>";
                exit;
            }

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'login' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Log logout untuk debugging
            if (Auth::guard('admin')->check()) {
                Log::info('Admin logout', [
                    'id' => Auth::guard('admin')->id(),
                    'username' => Auth::guard('admin')->user()->username ?? 'unknown'
                ]);
            } elseif (Auth::guard('mahasiswa')->check()) {
                Log::info('Mahasiswa logout', [
                    'id' => Auth::guard('mahasiswa')->id(),
                    'nim' => Auth::guard('mahasiswa')->user()->nim ?? 'unknown'
                ]);
            } elseif (Auth::guard('dosen')->check()) {
                Log::info('Dosen logout', [
                    'id' => Auth::guard('dosen')->id(),
                    'nip' => Auth::guard('dosen')->user()->nip ?? 'unknown'
                ]);
            }

            // Cache control header
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

            // Logout dari semua guard untuk memastikan
            if (Auth::guard('admin')->check()) {
                Auth::guard('admin')->logout();
            }

            if (Auth::guard('mahasiswa')->check()) {
                Auth::guard('mahasiswa')->logout();
            }

            if (Auth::guard('dosen')->check()) {
                Auth::guard('dosen')->logout();
            }

            // Hapus semua data session
            $request->session()->flush();

            // Invalidasi dan regenerasi session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect dengan header anti-cache
            $response = redirect()->route('login');
            $response->withCookie(cookie('LOGOUT_CACHE_BUSTER', uniqid(), 30));
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

            return $response;
        } catch (Exception $e) {
            // Log error
            Log::error('Logout error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Coba logout dengan cara alternatif
            Auth::guard('admin')->logout();
            Auth::guard('mahasiswa')->logout();
            Auth::guard('dosen')->logout();

            session()->flush();

            return redirect()->route('login');
        }
    }
}
