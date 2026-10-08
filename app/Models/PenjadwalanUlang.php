<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjadwalanUlang extends Model
{
    use HasFactory;

    protected $fillable = [
        'konsultasi_id',
        'tanggal_lama',
        'tanggal_baru',
        'waktu_mulai_baru',
        'waktu_selesai_baru',
        'snapshot_tanggal_lama',
        'snapshot_waktu_mulai_lama',
        'snapshot_waktu_selesai_lama',
        'alasan',
        'status',
    ];

    protected $casts = [
        'tanggal_lama' => 'date',
        'tanggal_baru' => 'date',
        'snapshot_tanggal_lama' => 'date',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}
