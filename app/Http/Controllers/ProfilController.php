<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserPhoto;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil user
     */
    public function show()
    {
        try {
            // Ambil data user yang sedang login
            $user = $this->getCurrentUser();
            $role = $this->getUserRole();

            if (!$user) {
                abort(404, 'User not found');
            }

            // Ambil foto user
            $userId = $this->getUserId($user, $role);
            $photo = UserPhoto::getUserPhoto($userId, $role);

            // Tambahkan foto_url ke object user
            $user->foto_url = UserPhoto::getUserPhotoUrl($userId, $role);
            $user->foto = $photo;

            // Informasi kontak admin untuk lupa password
            $adminContact = $this->getAdminContactFromDatabase();

            return view('profile.show', compact('user', 'role', 'adminContact'))->with('profile', $user);
        } catch (\Exception $e) {
            Log::error('Error in profile show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat profil');
        }
    }

    /**
     * Upload/Update foto profil
     */
    public function update(Request $request)
    {
        try {
            // Validasi file foto
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048'
            ], [
                'foto.required' => 'Foto profil wajib dipilih',
                'foto.image' => 'File harus berupa gambar',
                'foto.mimes' => 'Format foto harus: JPEG, JPG, PNG, GIF, atau WebP',
                'foto.max' => 'Ukuran foto maksimal 2MB'
            ]);

            $user = $this->getCurrentUser();
            $role = $this->getUserRole();
            $userId = $this->getUserId($user, $role);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $file = $request->file('foto');

            // Validasi tambahan file
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak valid atau rusak'
                ], 400);
            }

            // Convert gambar ke base64
            $imageData = file_get_contents($file->getPathname());
            $base64 = base64_encode($imageData);

            if (!$base64) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses gambar'
                ], 500);
            }

            // Simpan atau update foto
            UserPhoto::updateOrCreate(
                [
                    'user_id' => $userId,
                    'user_type' => $role
                ],
                [
                    'foto_base64' => $base64,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'updated_at' => now()
                ]
            );

            // Log untuk debugging
            Log::info("Foto berhasil diupload", [
                'user_id' => $userId,
                'user_type' => $role,
                'file_name' => $file->getClientOriginalName()
            ]);

            // Response untuk AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diupdate!',
                    'foto_url' => UserPhoto::getUserPhotoUrl($userId, $role)
                ]);
            }

            return back()->with('success', 'Foto profil berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Error uploading photo: " . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload foto. Silakan coba lagi.'
                ], 500);
            }

            return back()->with('error', 'Gagal mengupload foto. Silakan coba lagi.');
        }
    }

    /**
     * Hapus foto profil
     */
    public function remove(Request $request)
    {
        try {
            $user = $this->getCurrentUser();
            $role = $this->getUserRole();
            $userId = $this->getUserId($user, $role);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Cek apakah foto ada
            $photoExists = UserPhoto::where('user_id', $userId)
                ->where('user_type', $role)
                ->exists();

            if (!$photoExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto profil tidak ditemukan'
                ], 404);
            }

            // Hapus foto
            $deleted = UserPhoto::where('user_id', $userId)
                ->where('user_type', $role)
                ->delete();

            if ($deleted) {
                Log::info("Foto berhasil dihapus", [
                    'user_id' => $userId,
                    'user_type' => $role
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil dihapus!',
                    'foto_url' => UserPhoto::getUserPhotoUrl($userId, $role) // Default avatar
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto profil'
            ], 500);
        } catch (\Exception $e) {
            Log::error("Error removing photo: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Ganti password user
     */
    public function changePassword(Request $request)
    {
        try {
            // Validasi input - Password lebih fleksibel (PERUBAHAN UTAMA)
            $request->validate([
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'confirmed',
                    'min:6',        // Hanya minimal 6 karakter
                    'max:50'        // Maksimal 50 karakter
                ],
            ], [
                'current_password.required' => 'Password saat ini wajib diisi',
                'new_password.required' => 'Password baru wajib diisi',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
                'new_password.min' => 'Password minimal 6 karakter',
                'new_password.max' => 'Password maksimal 50 karakter'
            ]);

            $user = $this->getCurrentUser();
            $role = $this->getUserRole();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Cek password lama
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak benar'
                ], 400);
            }

            // Cek apakah password baru berbeda
            if (Hash::check($request->new_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password baru harus berbeda dari password saat ini'
                ], 400);
            }

            // Update password berdasarkan role
            $updated = $this->updatePasswordByRole($user, $role, $request->new_password);

            if ($updated) {
                // Log untuk security audit
                Log::info("Password berhasil diubah", [
                    'user_id' => $this->getUserId($user, $role),
                    'user_type' => $role,
                    'timestamp' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil diubah!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password. Silakan coba lagi.'
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error changing password: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password. Silakan coba lagi.'
            ], 500);
        }
    }

    private function getAdminContactFromDatabase()
    {
        try {
            // Prioritas 1: Cari admin yang sedang login (jika admin)
            if (Auth::guard('admin')->check()) {
                $currentAdminId = Auth::guard('admin')->id();
                $adminData = DB::table('admin')
                    ->select('email', 'nama', 'username')
                    ->where('id', $currentAdminId)
                    ->first();

                if ($adminData && $adminData->email) {
                    return [
                        'phone' => '+62 812-6824-0068', // Default phone karena tidak ada kolom phone
                        'email' => $adminData->email,
                        'name' => $adminData->nama ?? $adminData->username ?? 'Administrator SEPTI',
                        'working_hours' => 'Senin - Jumat, 08:00 - 17:00 WIB'
                    ];
                }
            }

            // Prioritas 2: Cari admin pertama yang memiliki email
            $adminData = DB::table('admin')
                ->select('email', 'nama', 'username')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->orderBy('id', 'asc')
                ->first();

            if ($adminData) {
                return [
                    'phone' => '+62 812-6824-0068', // Default phone
                    'email' => $adminData->email,
                    'name' => $adminData->nama ?? $adminData->username ?? 'Administrator SEPTI',
                    'working_hours' => 'Senin - Jumat, 08:00 - 17:00 WIB'
                ];
            }

            // Prioritas 3: Cari admin dengan ID 1 (admin pertama)
            $adminData = DB::table('admin')
                ->select('email', 'nama', 'username')
                ->where('id', 1)
                ->first();

            if ($adminData) {
                return [
                    'phone' => '+62 812-6824-0068',
                    'email' => $adminData->email ?? 'admin@university.ac.id',
                    'name' => $adminData->nama ?? $adminData->username ?? 'Administrator SEPTI',
                    'working_hours' => 'Senin - Jumat, 08:00 - 17:00 WIB'
                ];
            }
        } catch (\Exception $e) {
            // Log error tapi jangan break aplikasi
            Log::warning("Error fetching admin contact from database: " . $e->getMessage());
        }

        // Fallback: Data default jika tidak ada di database
        return [
            'phone' => '+62 812-6824-0068',
            'email' => 'syahirahtrimeilinaa@gmail.com',
            'name' => 'Administrator SEPTI',
            'working_hours' => 'Senin - Jumat, 08:00 - 17:00 WIB'
        ];
    }

    // ==================== HELPER METHODS ====================

    /**
     * Ambil user yang sedang login
     */
    private function getCurrentUser()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return Auth::guard('mahasiswa')->user();
        } elseif (Auth::guard('dosen')->check()) {
            return Auth::guard('dosen')->user();
        } elseif (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }

        return null;
    }

    /**
     * Ambil role user yang sedang login
     */
    private function getUserRole()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            return 'dosen';
        } elseif (Auth::guard('admin')->check()) {
            return 'admin';
        }

        return null;
    }

    /**
     * Ambil ID user berdasarkan role
     */
    private function getUserId($user, $role)
    {
        switch ($role) {
            case 'mahasiswa':
                return $user->nim;
            case 'dosen':
                return $user->nip;
            case 'admin':
                return $user->id;
            default:
                return null;
        }
    }

    /**
     * Update password berdasarkan role user
     */
    private function updatePasswordByRole($user, $role, $newPassword)
    {
        $hashedPassword = Hash::make($newPassword);

        try {
            switch ($role) {
                case 'mahasiswa':
                    return DB::table('mahasiswas')
                        ->where('nim', $user->nim)
                        ->update([
                            'password' => $hashedPassword,
                            'updated_at' => now()
                        ]);

                case 'dosen':
                    return DB::table('dosens')
                        ->where('nip', $user->nip)
                        ->update([
                            'password' => $hashedPassword,
                            'updated_at' => now()
                        ]);

                case 'admin':
                    return DB::table('admins')
                        ->where('id', $user->id)
                        ->update([
                            'password' => $hashedPassword,
                            'updated_at' => now()
                        ]);

                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Error updating password in database: " . $e->getMessage());
            return false;
        }
    }
}
