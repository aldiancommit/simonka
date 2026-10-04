<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenjadwalanUlangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'konsultasi_id' => 'required|exists:konsultasis,id',
            'tanggal_lama' => 'required|date',
            'tanggal_baru' => 'required|date',
            'waktu_mulai_baru' => 'required|date_format:H:i',
            'waktu_selesai_baru' => 'nullable|date_format:H:i|after:waktu_mulai_baru',
            'alasan' => 'required|string|max:2000',
            'status' => 'nullable|in:Menunggu,Disetujui,Ditolak',
        ];
    }
}
