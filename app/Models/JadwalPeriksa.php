<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPeriksa extends Model
{
    protected $table = 'jadwal_periksa';

    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'nomor_dilayani',
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }

    public function daftarPolis()
    {
        return $this->hasMany(DaftarPoli::class, 'id_jadwal');
    }

    public function getNomorDilayaniAttribute()
    {
        if (array_key_exists('nomor_dilayani', $this->attributes) && $this->attributes['nomor_dilayani'] !== null) {
            return (int) $this->attributes['nomor_dilayani'];
        }

        // Antrian terkecil yang belum selesai diperiksa
        $sedangDilayani = $this->daftarPolis()
            ->whereDoesntHave('periksa')
            ->min('no_antrian');

        if ($sedangDilayani) {
            return (int) $sedangDilayani;
        }

        // Jika semua yang terdaftar sudah diperiksa
        $terakhirSelesai = $this->daftarPolis()
            ->whereHas('periksa')
            ->max('no_antrian');

        return (int) ($terakhirSelesai ?: 0);
    }
}
