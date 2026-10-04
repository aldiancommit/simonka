<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pemohon', 'instansi', 'no_telepon', 'email',
        'perihal', 'tanggal_konsultasi', 'waktu_mulai',
        'waktu_selesai', 'status', 'catatan',
    ];

    protected $casts = [
        'tanggal_konsultasi' => 'date',
    ];

    public function penjadwalanUlangs()
    {
        return $this->hasMany(PenjadwalanUlang::class);
    }
}
