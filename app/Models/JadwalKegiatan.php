<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan', 'tanggal', 'waktu_mulai',
        'waktu_selesai', 'lokasi', 'keterangan', 'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
