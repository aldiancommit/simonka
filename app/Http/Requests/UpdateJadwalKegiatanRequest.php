<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jadwal = $this->route('jadwal');

        return $jadwal && ($this->user()?->can('update', $jadwal) ?? false);
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
            'status' => 'nullable|in:Terjadwal,Berlangsung,Selesai,Dibatalkan',
        ];
    }
}
