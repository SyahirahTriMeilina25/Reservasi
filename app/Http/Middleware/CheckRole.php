<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        try {
            Log::info('CheckRole dijalankan', [
                'session_id' => session()->getId(),
                'path' => $request->path(),
                'role' => $role,
                'session_data' => session()->all(),
                'admin_auth' => Auth::guard('admin')->check(),
                'mahasiswa_auth' => Auth::guard('mahasiswa')->check(),
                'dosen_auth' => Auth::guard('dosen')->check()
            ]);
            
            // Debug info jika debug mode aktif
            if (config('app.debug') && $request->has('debug_role')) {
                $this->showDebugInfo($request, $role);
            }
            
            Log::info('CheckRole untuk role: ' . $role . ', path: ' . $request->path());
            Log::info('Session data: ' . json_encode(session()->all()));
            
            // Marker session khusus untuk admin/mahasiswa/dosen
            $adminMarker = 'login_admin_'.md5('admin');
            $mahasiswaMarker = 'login_mahasiswa_'.md5('mahasiswa');
            $dosenMarker = 'login_dosen_'.md5('dosen');
            
            // ===== STRATEGI 0: Verifikasi berdasarkan Auth Guard =====
            // Prioritaskan Auth guard untuk mencegah logout otomatis
            
            $adminAuth = Auth::guard('admin')->check();
            $mahasiswaAuth = Auth::guard('mahasiswa')->check();
            $dosenAuth = Auth::guard('dosen')->check();
            
            Log::info('Auth status - Admin: ' . ($adminAuth ? 'Ya' : 'Tidak') . 
                    ', Mahasiswa: ' . ($mahasiswaAuth ? 'Ya' : 'Tidak') . 
                    ', Dosen: ' . ($dosenAuth ? 'Ya' : 'Tidak'));
            
            // Verifikasi admin berdasarkan auth guard
            if ($role === 'admin' && $adminAuth) {
                $admin = Auth::guard('admin')->user();
                Log::info('Admin terautentikasi via guard: ' . $admin->username);
                
                // Regenerasi session admin
                $this->regenerateAdminSession($admin);
                
                return $next($request);
            }
            
            // Verifikasi mahasiswa berdasarkan auth guard
            if ($role === 'mahasiswa' && $mahasiswaAuth) {
                $mahasiswa = Auth::guard('mahasiswa')->user();
                Log::info('Mahasiswa terautentikasi via guard: ' . $mahasiswa->nim);
                
                $this->regenerateMahasiswaSession($mahasiswa);
                
                return $next($request);
            }
            
            // Verifikasi dosen berdasarkan auth guard
            if ($role === 'dosen' && $dosenAuth) {
                $dosen = Auth::guard('dosen')->user();
                Log::info('Dosen terautentikasi via guard: ' . $dosen->nip);
                
                $this->regenerateDosenSession($dosen);
                
                return $next($request);
            }
            
            // ===== STRATEGI 1: Verifikasi berdasarkan session marker dan role_akses =====
            
            // Cek admin dengan session marker
            if ($role === 'admin' && session()->has($adminMarker) && session('role_akses') === 'admin') {
                Log::info('Akses diberikan: admin (via session marker)');
                return $next($request);
            }
            
            // Cek mahasiswa dengan session marker
            if ($role === 'mahasiswa' && session()->has($mahasiswaMarker) && session('role_akses') === 'mahasiswa') {
                Log::info('Akses diberikan: mahasiswa (via session marker)');
                return $next($request);
            }
            
            // Cek dosen dengan session marker (termasuk koordinator prodi)
            if ($role === 'dosen' && session()->has($dosenMarker) && 
                (session('role_akses') === 'dosen' || session('role_akses') === 'koordinator_prodi')) {
                Log::info('Akses diberikan: dosen (via session marker)');
                return $next($request);
            }
            
            // ===== STRATEGI 2: Verifikasi berdasarkan session role tanpa marker =====
            // Ini backward compatible dengan session yang sudah ada
            
            $sessionRole = session('role');
            $sessionRoleAkses = session('role_akses');
            
            Log::info('Session role: ' . ($sessionRole ?? 'tidak ada'));
            Log::info('Session role_akses: ' . ($sessionRoleAkses ?? 'tidak ada'));
            
            // Penanganan khusus untuk admin berdasarkan session saja
            if ($role === 'admin' && ($sessionRole === 'admin' || $sessionRoleAkses === 'admin')) {
                Log::info('Akses diberikan: admin (via session role)');
                
                // Tambahkan session marker untuk selanjutnya
                session([$adminMarker => 1]);
                session()->save();
                
                return $next($request);
            }
            
            // Penanganan untuk mahasiswa
            if ($role === 'mahasiswa' && ($sessionRole === 'mahasiswa' || $sessionRoleAkses === 'mahasiswa')) {
                Log::info('Akses diberikan: mahasiswa (via session role)');
                
                // Tambahkan session marker untuk selanjutnya
                session([$mahasiswaMarker => 1]);
                session()->save();
                
                return $next($request);
            }
            
            // Penanganan untuk dosen
            if ($role === 'dosen' && (
                $sessionRole === 'dosen' || 
                $sessionRoleAkses === 'dosen' || 
                $sessionRoleAkses === 'koordinator_prodi'
            )) {
                Log::info('Akses diberikan: dosen (via session role)');
                
                // Tambahkan session marker untuk selanjutnya
                session([$dosenMarker => 1]);
                session()->save();
                
                return $next($request);
            }
            
            // ===== Jika sampai di sini, berarti akses ditolak =====
            Log::warning('Akses ditolak untuk role: ' . $role);
            
            // Redirect berdasarkan guard yang aktif
            if ($adminAuth) {
                Log::info('Redirect ke admin dashboard karena user adalah admin');
                return redirect()->route('admin.dashboard');
            } else if ($mahasiswaAuth) {
                Log::info('Redirect ke usulanbimbingan karena user adalah mahasiswa');
                return redirect('/usulanbimbingan');
            } else if ($dosenAuth) {
                Log::info('Redirect ke persetujuan karena user adalah dosen');
                return redirect('/persetujuan');
            }
            
            // Jika tidak ada guard yang aktif, redirect ke login
            Log::warning('User tidak terotentikasi, redirect ke login');
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman tersebut.');
            
        } catch (Exception $e) {
            // Log error untuk debugging
            Log::error('CheckRole error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Debug mode: tampilkan error
            if (config('app.debug') && !$request->expectsJson() && !$request->ajax()) {
                echo "<h2>CheckRole Error</h2>";
                echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
                echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                echo "<p><a href=\"javascript:history.back()\">Kembali</a></p>";
                exit;
            }
            
            // Fallback ke login
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem. Silakan coba login kembali.');
        }
    }
    
    /**
     * Regenerasi session admin
     */
    private function regenerateAdminSession($admin)
    {
        // Marker untuk admin
        $adminMarker = 'login_admin_'.md5('admin');
        
        // Verifikasi role dari database untuk keamanan
        $roleData = DB::table('role')->where('id', $admin->role_id)->first();
        
        if ($roleData && $roleData->role_akses === 'admin') {
            // Pulihkan session lengkap
            Log::info('Regenerating session admin untuk: ' . $admin->username);
            
            session([
                $adminMarker => 1,
                'login_admin_'.md5($admin->username) => 1, // tambahan
                'role' => 'admin',
                'role_akses' => 'admin',
                'role_id' => $admin->role_id,
                'user_id' => $admin->id,
                'user_name' => $admin->nama ?? 'Administrator',
                'username' => $admin->username
            ]);
            session()->save();
            
            return true;
        }
        
        // Fallback: Jika admin terotentikasi tapi tidak ada role di database
        if (!$roleData) {
            Log::info('Admin terautentikasi tanpa role, membuat role otomatis');
            
            // Cari role admin yang sudah ada
            $adminRoleId = DB::table('role')->where('role_akses', 'admin')->value('id');
            
            if (!$adminRoleId) {
                // Buat role admin baru
                $adminRoleId = DB::table('role')->insertGetId([
                    'role_akses' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Update admin dengan role yang benar
            DB::table('admin')->where('id', $admin->id)->update(['role_id' => $adminRoleId]);
            
            // Pulihkan session
            session([
                $adminMarker => 1,
                'login_admin_'.md5($admin->username) => 1, // tambahan
                'role' => 'admin',
                'role_akses' => 'admin',
                'role_id' => $adminRoleId,
                'user_id' => $admin->id,
                'user_name' => $admin->nama ?? 'Administrator',
                'username' => $admin->username
            ]);
            session()->save();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Regenerasi session mahasiswa
     */
    private function regenerateMahasiswaSession($mahasiswa)
    {
        // Marker untuk mahasiswa
        $mahasiswaMarker = 'login_mahasiswa_'.md5('mahasiswa');
        
        // Verifikasi role dari database
        $roleData = DB::table('role')->where('id', $mahasiswa->role_id)->first();
        
        if ($roleData && $roleData->role_akses === 'mahasiswa') {
            // Pulihkan session lengkap
            Log::info('Regenerating session mahasiswa untuk: ' . $mahasiswa->nim);
            
            session([
                $mahasiswaMarker => 1,
                'login_mahasiswa_'.md5($mahasiswa->nim) => 1, // tambahan
                'role' => 'mahasiswa',
                'role_akses' => 'mahasiswa',
                'role_id' => $mahasiswa->role_id,
                'user_id' => $mahasiswa->id,
                'user_name' => $mahasiswa->nama ?? 'Mahasiswa',
                'nim' => $mahasiswa->nim
            ]);
            session()->save();
            
            return true;
        }
        
        // Fallback: role tidak ditemukan
        if (!$roleData) {
            Log::info('Mahasiswa terautentikasi tanpa role, membuat role otomatis');
            
            // Cari role mahasiswa yang sudah ada
            $mahasiswaRoleId = DB::table('role')->where('role_akses', 'mahasiswa')->value('id');
            
            if (!$mahasiswaRoleId) {
                // Buat role mahasiswa baru
                $mahasiswaRoleId = DB::table('role')->insertGetId([
                    'role_akses' => 'mahasiswa',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Update mahasiswa
            DB::table('mahasiswas')->where('id', $mahasiswa->id)->update(['role_id' => $mahasiswaRoleId]);
            
            // Pulihkan session
            session([
                $mahasiswaMarker => 1,
                'login_mahasiswa_'.md5($mahasiswa->nim) => 1, // tambahan
                'role' => 'mahasiswa',
                'role_akses' => 'mahasiswa',
                'role_id' => $mahasiswaRoleId,
                'user_id' => $mahasiswa->id,
                'user_name' => $mahasiswa->nama ?? 'Mahasiswa',
                'nim' => $mahasiswa->nim
            ]);
            session()->save();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Regenerasi session dosen
     */
    private function regenerateDosenSession($dosen)
    {
        // Marker untuk dosen
        $dosenMarker = 'login_dosen_'.md5('dosen');
        
        // Verifikasi role dari database
        $roleData = DB::table('role')->where('id', $dosen->role_id)->first();
        
        // Berikan akses jika dosen atau koordinator_prodi
        if ($roleData && ($roleData->role_akses === 'dosen' || $roleData->role_akses === 'koordinator_prodi')) {
            // Pulihkan session lengkap
            Log::info('Regenerating session dosen untuk: ' . $dosen->nip);
            
            session([
                $dosenMarker => 1,
                'login_dosen_'.md5($dosen->nip) => 1, // tambahan
                'role' => 'dosen',
                'role_akses' => $roleData->role_akses,
                'role_id' => $dosen->role_id,
                'user_id' => $dosen->id,
                'user_name' => $dosen->nama ?? 'Dosen',
                'nip' => $dosen->nip
            ]);
            session()->save();
            
            return true;
        }
        
        // Fallback: role tidak ditemukan
        if (!$roleData) {
            Log::info('Dosen terautentikasi tanpa role, membuat role otomatis');
            
            // Cari role dosen yang sudah ada
            $dosenRoleId = DB::table('role')->where('role_akses', 'dosen')->value('id');
            
            if (!$dosenRoleId) {
                // Buat role dosen baru
                $dosenRoleId = DB::table('role')->insertGetId([
                    'role_akses' => 'dosen',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Update dosen
            DB::table('dosens')->where('id', $dosen->id)->update(['role_id' => $dosenRoleId]);
            
            // Pulihkan session
            session([
                $dosenMarker => 1,
                'login_dosen_'.md5($dosen->nip) => 1, // tambahan
                'role' => 'dosen',
                'role_akses' => 'dosen',
                'role_id' => $dosenRoleId,
                'user_id' => $dosen->id,
                'user_name' => $dosen->nama ?? 'Dosen',
                'nip' => $dosen->nip
            ]);
            session()->save();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Tampilkan informasi debug untuk middleware CheckRole
     */
    private function showDebugInfo(Request $request, $role)
    {
        echo "<h2>Debug CheckRole Middleware</h2>";
        
        echo "<h3>Request Info:</h3>";
        echo "<ul>";
        echo "<li>URL: " . $request->fullUrl() . "</li>";
        echo "<li>Path: " . $request->path() . "</li>";
        echo "<li>Role yang diminta: " . $role . "</li>";
        echo "</ul>";
        
        echo "<h3>Session Info:</h3>";
        echo "<ul>";
        echo "<li>Session ID: " . session()->getId() . "</li>";
        echo "<li>Session login_admin_".md5('admin').": " . (session('login_admin_'.md5('admin')) ? 'ADA' : 'tidak ada') . "</li>";
        echo "<li>Session login_mahasiswa_".md5('mahasiswa').": " . (session('login_mahasiswa_'.md5('mahasiswa')) ? 'ADA' : 'tidak ada') . "</li>";
        echo "<li>Session login_dosen_".md5('dosen').": " . (session('login_dosen_'.md5('dosen')) ? 'ADA' : 'tidak ada') . "</li>";
        echo "<li>Session role: " . (session('role') ?? 'tidak ada') . "</li>";
        echo "<li>Session role_akses: " . (session('role_akses') ?? 'tidak ada') . "</li>";
        echo "<li>Session role_id: " . (session('role_id') ?? 'tidak ada') . "</li>";
        echo "<li>Session user_id: " . (session('user_id') ?? 'tidak ada') . "</li>";
        echo "<li>Session user_name: " . (session('user_name') ?? 'tidak ada') . "</li>";
        echo "</ul>";
        
        echo "<h3>Semua Data Session:</h3>";
        echo "<pre>";
        print_r(session()->all());
        echo "</pre>";
        
        echo "<h3>Auth Info:</h3>";
        echo "<ul>";
        echo "<li>Admin Auth: " . (Auth::guard('admin')->check() ? 'Ya' : 'Tidak') . "</li>";
        echo "<li>Mahasiswa Auth: " . (Auth::guard('mahasiswa')->check() ? 'Ya' : 'Tidak') . "</li>";
        echo "<li>Dosen Auth: " . (Auth::guard('dosen')->check() ? 'Ya' : 'Tidak') . "</li>";
        echo "</ul>";
        
        echo "<h3>Session in Database:</h3>";
        echo "<pre>";
        try {
            $sessionId = session()->getId();
            echo "Current Session ID: $sessionId\n\n";
            
            $dbSession = DB::table('sessions')->where('id', $sessionId)->first();
            if ($dbSession) {
                print_r((array)$dbSession);
            } else {
                echo "Session not found in database!";
            }
        } catch (\Exception $e) {
            echo "Error checking session table: " . $e->getMessage();
        }
        echo "</pre>";
        
        // Tampilkan info user jika login
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            echo "<h3>Admin Info:</h3>";
            echo "<ul>";
            echo "<li>ID: " . $user->id . "</li>";
            echo "<li>Username: " . $user->username . "</li>";
            echo "<li>Nama: " . ($user->nama ?? 'tidak ada') . "</li>";
            echo "<li>Role ID: " . ($user->role_id ?? 'tidak ada') . "</li>";
            
            // Ambil role dari database
            $roleData = DB::table('role')->where('id', $user->role_id ?? 0)->first();
            echo "<li>Role dari database: " . ($roleData ? $roleData->role_akses : 'TIDAK DITEMUKAN') . "</li>";
            echo "</ul>";
        }
        
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            echo "<h3>Mahasiswa Info:</h3>";
            echo "<ul>";
            echo "<li>ID: " . $user->id . "</li>";
            echo "<li>NIM: " . $user->nim . "</li>";
            echo "<li>Nama: " . ($user->nama ?? 'tidak ada') . "</li>";
            echo "<li>Role ID: " . ($user->role_id ?? 'tidak ada') . "</li>";
            
            // Ambil role dari database
            $roleData = DB::table('role')->where('id', $user->role_id ?? 0)->first();
            echo "<li>Role dari database: " . ($roleData ? $roleData->role_akses : 'TIDAK DITEMUKAN') . "</li>";
            echo "</ul>";
        }
        
        if (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            echo "<h3>Dosen Info:</h3>";
            echo "<ul>";
            echo "<li>ID: " . $user->id . "</li>";
            echo "<li>NIP: " . $user->nip . "</li>";
            echo "<li>Nama: " . ($user->nama ?? 'tidak ada') . "</li>";
            echo "<li>Role ID: " . ($user->role_id ?? 'tidak ada') . "</li>";
            
            // Ambil role dari database
            $roleData = DB::table('role')->where('id', $user->role_id ?? 0)->first();
            echo "<li>Role dari database: " . ($roleData ? $roleData->role_akses : 'TIDAK DITEMUKAN') . "</li>";
            echo "</ul>";
        }
        
        // Tampilkan semua roles di database
        echo "<h3>Roles di Database:</h3>";
        $roles = DB::table('role')->get();
        if ($roles->isEmpty()) {
            echo "<p>Tidak ada data role di database!</p>";
        } else {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Role Akses</th></tr>";
            foreach ($roles as $role) {
                echo "<tr>";
                echo "<td>{$role->id}</td>";
                echo "<td>{$role->role_akses}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<h3>Cookies:</h3>";
        echo "<pre>";
        print_r($_COOKIE);
        echo "</pre>";
        
        echo "<p>Ini adalah informasi debug. <a href='javascript:history.back()'>Kembali</a> atau 
              <a href='" . url()->current() . "'>Lanjutkan tanpa debug</a></p>";
        exit;
    }
}