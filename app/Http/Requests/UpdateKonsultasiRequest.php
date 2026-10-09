<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $konsultasi = $this->route('konsultasi');

        if (! $user || ! $konsultasi) {
            return false;
        }

        if (! $user->can('update', $konsultasi)) {
            return false;
        }

        // Pimpinan: Strict Key Whitelist (HANYA status dan catatan, plus _token, _method)
        if ($user->hasRole(Role::Pimpinan)) {
            $allowedKeys = ['_token', '_method', 'status', 'catatan'];
            $submittedKeys = array_keys($this->all());
            $forbiddenKeys = array_diff($submittedKeys, $allowedKeys);

            if (! empty($forbiddenKeys)) {
                return false;
            }
        }

        // Sekretariat:
        if ($user->hasRole(Role::Sekretariat)) {
            if ($this->has('status')) {
                $submittedStatus = $this->input('status');
                $allowedStatuses = [$konsultasi->status, 'Selesai', 'Dibatalkan'];

                if (! in_array($submittedStatus, $allowedStatuses, true)) {
                    return false;
                }
            }

            // Pada konsultasi berstatus Disetujui: dilarang mengubah tanggal_konsultasi, waktu_mulai, waktu_selesai
            if ($konsultasi->status === 'Disetujui') {
                if ($this->has('tanggal_konsultasi')) {
                    $inputDate = Carbon::parse($this->input('tanggal_konsultasi'))->format('Y-m-d');
                    $currentDate = Carbon::parse($konsultasi->tanggal_konsultasi)->format('Y-m-d');
                    if ($inputDate !== $currentDate) {
                        return false;
                    }
                }

                if ($this->has('waktu_mulai')) {
                    $inputStart = Carbon::parse($this->input('waktu_mulai'))->format('H:i');
                    $currentStart = Carbon::parse($konsultasi->waktu_mulai)->format('H:i');
                    if ($inputStart !== $currentStart) {
                        return false;
                    }
                }

                if ($this->has('waktu_selesai') && $this->input('waktu_selesai') !== null) {
                    $inputEnd = Carbon::parse($this->input('waktu_selesai'))->format('H:i');
                    $currentEnd = $konsultasi->waktu_selesai ? Carbon::parse($konsultasi->waktu_selesai)->format('H:i') : null;
                    if ($inputEnd !== $currentEnd) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user && $user->hasRole(Role::Pimpinan)) {
            return [
                'status' => 'required|in:Menunggu,Disetujui,Ditolak,Selesai,Dibatalkan',
                'catatan' => 'nullable|string|max:2000',
            ];
        }

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
