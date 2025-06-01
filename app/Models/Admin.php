<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'username',
        'password',
        'nama',
        'email',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relasi ke foto profil - TAMBAHAN INI
     */
    public function foto()
    {
        return $this->hasOne(UserPhoto::class, 'user_id', 'id')
                    ->where('user_type', 'admin');
    }

    /**
     * Accessor untuk mendapatkan foto base64
     */
    public function getFotoBase64Attribute()
    {
        if ($this->foto && $this->foto->foto_base64) {
            return 'data:' . $this->foto->mime_type . ';base64,' . $this->foto->foto_base64;
        }
        return null;
    }

    /**
     * Accessor untuk nama lengkap
     */
    public function getNamaLengkapAttribute()
    {
        return $this->nama ?? 'Administrator';
    }
}