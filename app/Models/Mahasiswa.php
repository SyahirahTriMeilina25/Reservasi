<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasGoogleCalendar;
use App\Traits\HasFcmNotification;

class Mahasiswa extends Authenticatable
{
    use HasGoogleCalendar;
    use HasFcmNotification;
    use HasFactory, Notifiable;
    
    protected $table = 'mahasiswas'; // Pastikan ini sesuai dengan nama tabel di database
    protected $primaryKey = 'nim';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nim',
        'nama',
        'angkatan',
        'email',
        'password',
        'foto', // Kolom ini bisa tetap ada untuk kompatibilitas, tapi tidak digunakan lagi
        'prodi_id',
        'konsentrasi_id',
        'role_id',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_in',
        'google_token_created_at'
    ];

    protected $hidden = [
        'password',
        'google_access_token',
        'google_refresh_token'
    ];

    protected $casts = [
        'google_token_created_at' => 'datetime',
        'google_token_expires_in' => 'integer',
    ];

    // ======================= RELASI YANG SUDAH ADA =======================
    public function role()
    {
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function konsentrasi()
    {
        return $this->belongsTo(Konsentrasi::class);
    }

    // ======================= RELASI FOTO BARU (BASE64) =======================
    /**
     * Relasi dengan tabel user_photos untuk sistem foto base64
     */
    public function foto()
    {
        return $this->hasOne(UserPhoto::class, 'user_id', 'nim')
                    ->where('user_type', 'mahasiswa');
    }

    // ======================= METHODS YANG SUDAH ADA =======================
    public function hasRole($roleName)
    {
        return $this->role && $this->role->role_akses === $roleName;
    }

    // ======================= ACCESSOR FOTO (DIPERBARUI) =======================
    /**
     * Accessor untuk foto URL - menggunakan sistem base64 yang baru
     * Jika ada foto di tabel user_photos, gunakan itu
     * Jika tidak ada, fallback ke sistem lama atau default avatar
     */
    public function getFotoUrlAttribute()
    {
        // Prioritas 1: Cek foto dari tabel user_photos (sistem baru base64)
        $photoRecord = $this->foto;
        if ($photoRecord && $photoRecord->foto_base64) {
            return 'data:' . $photoRecord->mime_type . ';base64,' . $photoRecord->foto_base64;
        }

        // Prioritas 2: Fallback ke sistem lama jika masih ada
        if ($this->attributes['foto'] ?? null) {
            $oldPhotoPath = 'storage/foto_profil/' . $this->attributes['foto'];
            if (file_exists(public_path($oldPhotoPath))) {
                return asset($oldPhotoPath);
            }
        }

        // Prioritas 3: Default avatar untuk mahasiswa
        return UserPhoto::getDefaultAvatar('mahasiswa');
    }

    // ======================= HELPER METHODS UNTUK FOTO =======================
    /**
     * Cek apakah mahasiswa memiliki foto
     */
    public function hasFoto()
    {
        $photoRecord = $this->foto;
        return $photoRecord && $photoRecord->foto_base64;
    }

    /**
     * Get informasi foto
     */
    public function getFotoInfo()
    {
        $photoRecord = $this->foto;
        if ($photoRecord) {
            return [
                'original_name' => $photoRecord->original_name,
                'file_size' => $photoRecord->file_size,
                'mime_type' => $photoRecord->mime_type,
                'size_formatted' => number_format(($photoRecord->file_size ?? 0) / 1024, 1) . ' KB'
            ];
        }
        return null;
    }
}