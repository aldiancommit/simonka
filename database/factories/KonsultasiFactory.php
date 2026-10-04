<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KonsultasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_pemohon' => fake('id_ID')->name(),
            'instansi' => fake('id_ID')->company(),
            'no_telepon' => fake('id_ID')->phoneNumber(),
            'email' => fake('id_ID')->safeEmail(),
            'perihal' => fake('id_ID')->sentence(),
            'tanggal_konsultasi' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'waktu_mulai' => fake()->time('H:i'),
            'waktu_selesai' => fake()->time('H:i'),
            'status' => fake()->randomElement(['Menunggu', 'Disetujui', 'Ditolak', 'Selesai', 'Dibatalkan']),
            'catatan' => fake('id_ID')->paragraph(),
        ];
    }
}
