<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nip',
        'waktu_mulai',
        'waktu_selesai',
        'catatan',
        'status',
        'kapasitas',
        'sisa_kapasitas',
        'lokasi'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'kapasitas' => 'integer',
        'sisa_kapasitas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_TIDAK_TERSEDIA = 'tidak_tersedia';
    const STATUS_PENUH = 'penuh';

    // Status default
    protected $attributes = [
        'status' => self::STATUS_TERSEDIA,
        'kapasitas' => 1,
        'sisa_kapasitas' => 1
    ];

    // Relasi dengan dosen
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'nip', 'nip');
    }

    // Relasi dengan bimbingan
    public function bimbingans(): HasMany
    {
        return $this->hasMany(Bimbingan::class, 'event_id', 'event_id');
    }

    // Scopes
    public function scopeTersedia($query)
    {
        return $query->where('status', self::STATUS_TERSEDIA)
                    ->where('sisa_kapasitas', '>', 0)
                    ->where('waktu_mulai', '>', now());
    }

    public function scopeByDosen($query, $nip)
    {
        return $query->where('nip', $nip);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_TERSEDIA && 
               $this->sisa_kapasitas > 0 &&
               Carbon::parse($this->waktu_mulai)->isFuture();
    }

    public function decrementKapasitas(): bool
    {
        if ($this->sisa_kapasitas > 0) {
            $this->decrement('sisa_kapasitas');
            
            if ($this->sisa_kapasitas === 0) {
                $this->update(['status' => self::STATUS_PENUH]);
            }
            
            return true;
        }
        return false;
    }

    public function incrementKapasitas(): bool
    {
        if ($this->sisa_kapasitas < $this->kapasitas) {
            $this->increment('sisa_kapasitas');
            
            if ($this->status === self::STATUS_PENUH) {
                $this->update(['status' => self::STATUS_TERSEDIA]);
            }
            
            return true;
        }
        return false;
    }

    // Accessor
    public function getWaktuLengkapAttribute(): string
    {
        return Carbon::parse($this->waktu_mulai)->isoFormat('dddd, D MMMM Y') . 
               ' | ' . 
               Carbon::parse($this->waktu_mulai)->format('H:i') . 
               ' - ' . 
               Carbon::parse($this->waktu_selesai)->format('H:i');
    }

    public function getDurasiAttribute(): int
    {
        return Carbon::parse($this->waktu_mulai)->diffInMinutes($this->waktu_selesai);
    }

    public function getIsSelesaiAttribute(): bool
    {
        return Carbon::parse($this->waktu_selesai)->isPast();
    }
}