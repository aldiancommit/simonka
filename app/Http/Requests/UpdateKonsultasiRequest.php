<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pemohon' => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'perihal' => 'required|string|max:500',
            'tanggal_konsultasi' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'status' => 'nullable|in:Menunggu,Disetujui,Ditolak,Selesai,Dibatalkan',
            'catatan' => 'nullable|string|max:2000',
        ];
    }
}
