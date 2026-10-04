<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjadwalanUlang extends Model
{
    use HasFactory;

    protected $fillable = [
        'konsultasi_id', 'tanggal_lama', 'tanggal_baru',
        'waktu_mulai_baru', 'waktu_selesai_baru', 'alasan', 'status',
    ];

    protected $casts = [
        'tanggal_lama' => 'date',
        'tanggal_baru' => 'date',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}
