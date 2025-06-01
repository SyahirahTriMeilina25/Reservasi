<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'foto_base64',
        'original_name',
        'mime_type',
        'file_size'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $attributes = [
        'mime_type' => 'image/jpeg', // default value sesuai migration
    ];

    /**
     * Get the URL data untuk ditampilkan di img src
     */
    public function getDataUrlAttribute()
    {
        if (!$this->foto_base64) {
            return null;
        }
        
        // Jika mime_type null, gunakan default dari migration
        $mimeType = $this->mime_type ?: 'image/jpeg';
        
        return 'data:' . $mimeType . ';base64,' . $this->foto_base64;
    }

    /**
     * Scope untuk mencari foto berdasarkan user
     */
    public function scopeForUser($query, $userId, $userType)
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    /**
     * Static method untuk mendapatkan foto user
     */
    public static function getUserPhoto($userId, $userType)
    {
        if (!$userId || !$userType) {
            return null;
        }
        
        return self::where('user_id', $userId)
                   ->where('user_type', $userType)
                   ->first();
    }

    /**
     * Static method untuk mendapatkan URL foto atau default
     * This method is used in the controller: $user->foto_url = UserPhoto::getUserPhotoUrl($userId, $role);
     */
    public static function getUserPhotoUrl($userId, $userType)
    {
        $photo = self::getUserPhoto($userId, $userType);
        
        if ($photo && $photo->foto_base64) {
            return $photo->data_url;
        }
        
        // Return default avatar berdasarkan user type
        return self::getDefaultAvatar($userType);
    }

    /**
     * Get default avatar berdasarkan user type
     */
    public static function getDefaultAvatar($userType)
    {
        $defaultAvatars = [
            'mahasiswa' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="studentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#059669;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#10b981;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="200" height="200" fill="url(#studentGrad)" rx="100"/>
                    <circle cx="100" cy="75" r="35" fill="white" opacity="0.9"/>
                    <path d="M 65 140 Q 100 120 135 140 Q 135 165 100 170 Q 65 165 65 140" fill="white" opacity="0.9"/>
                    <text x="100" y="190" font-family="Arial, sans-serif" font-size="12" fill="white" text-anchor="middle" font-weight="bold">MAHASISWA</text>
                </svg>
            '),
            'dosen' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="dosenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="200" height="200" fill="url(#dosenGrad)" rx="100"/>
                    <circle cx="100" cy="75" r="35" fill="white" opacity="0.9"/>
                    <path d="M 65 140 Q 100 120 135 140 Q 135 165 100 170 Q 65 165 65 140" fill="white" opacity="0.9"/>
                    <text x="100" y="190" font-family="Arial, sans-serif" font-size="14" fill="white" text-anchor="middle" font-weight="bold">DOSEN</text>
                </svg>
            '),
            'admin' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="adminGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#dc2626;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#ef4444;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="200" height="200" fill="url(#adminGrad)" rx="100"/>
                    <circle cx="100" cy="75" r="35" fill="white" opacity="0.9"/>
                    <path d="M 65 140 Q 100 120 135 140 Q 135 165 100 170 Q 65 165 65 140" fill="white" opacity="0.9"/>
                    <path d="M 85 55 L 100 40 L 115 55 L 110 50 L 105 55 L 100 50 L 95 55 L 90 50 Z" fill="gold" opacity="0.8"/>
                    <text x="100" y="190" font-family="Arial, sans-serif" font-size="16" fill="white" text-anchor="middle" font-weight="bold">ADMIN</text>
                </svg>
            ')
        ];

        return $defaultAvatars[$userType] ?? $defaultAvatars['mahasiswa'];
    }

    /**
     * Helper method untuk format ukuran file
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Check if user has photo
     */
    public static function hasPhoto($userId, $userType)
    {
        return self::where('user_id', $userId)
                   ->where('user_type', $userType)
                   ->exists();
    }

    /**
     * Delete user photo
     */
    public static function deleteUserPhoto($userId, $userType)
    {
        return self::where('user_id', $userId)
                   ->where('user_type', $userType)
                   ->delete();
    }

    /**
     * Relationship methods (optional, for future use)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'user_id', 'nim')
                    ->where('user_type', 'mahasiswa');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'user_id', 'nip')
                    ->where('user_type', 'dosen');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id')
                    ->where('user_type', 'admin');
    }
}