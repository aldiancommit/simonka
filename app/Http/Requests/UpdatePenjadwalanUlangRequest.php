<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePenjadwalanUlangRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $penjadwalanUlang = $this->route('penjadwalan_ulang');

        if (! $user || ! $penjadwalanUlang) {
            return false;
        }

        if (! $user->can('update', $penjadwalanUlang)) {
            return false;
        }

        // Pimpinan: Strict key whitelist (HANYA status, plus _token, _method)
        if ($user->hasRole(Role::Pimpinan)) {
            $allowedKeys = ['_token', '_method', 'status'];
            $submittedKeys = array_keys($this->all());
            $forbiddenKeys = array_diff($submittedKeys, $allowedKeys);

            if (! empty($forbiddenKeys)) {
                return false;
            }

            if ($this->has('status') && ! in_array($this->input('status'), ['Disetujui', 'Ditolak', 'Menunggu'])) {
                return false;
            }
        }

        // Sekretariat:
        if ($user->hasRole(Role::Sekretariat)) {
            // Dilarang mengirim status Disetujui atau Ditolak
            if (in_array($this->input('status'), ['Disetujui', 'Ditolak'])) {
                return false;
            }

            // Sekretariat hanya boleh mengubah field pengajuan selama status reschedule saat ini Menunggu
            if ($penjadwalanUlang->status !== 'Menunggu') {
                return false;
            }
        }

        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user && $user->hasRole(Role::Pimpinan)) {
            return [
                'status' => 'required|in:Menunggu,Disetujui,Ditolak',
            ];
        }

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
