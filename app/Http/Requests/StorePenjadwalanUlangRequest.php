<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenjadwalanUlangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'konsultasi_id' => [
                'required',
                Rule::exists('konsultasis', 'id')->where('status', 'Disetujui'),
            ],
            'tanggal_baru' => 'required|date|after_or_equal:today',
            'waktu_mulai_baru' => 'required|date_format:H:i',
            'waktu_selesai_baru' => 'nullable|date_format:H:i|after:waktu_mulai_baru',
            'alasan' => 'required|string|max:2000',
            'status' => 'nullable|in:Menunggu,Disetujui,Ditolak',
        ];
    }
}
