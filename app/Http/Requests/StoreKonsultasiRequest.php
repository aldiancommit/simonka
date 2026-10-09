<?php

namespace App\Http\Requests;

use App\Models\Konsultasi;
use Illuminate\Foundation\Http\FormRequest;

class StoreKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Konsultasi::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_pemohon' => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'perihal' => 'required|string|max:500',
            'tanggal_konsultasi' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string|max:2000',
        ];
    }
}
