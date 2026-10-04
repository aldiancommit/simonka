<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalKegiatanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kegiatan' => fake('id_ID')->sentence(3),
            'tanggal' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'waktu_mulai' => fake()->time('H:i'),
            'waktu_selesai' => fake()->time('H:i'),
            'lokasi' => fake('id_ID')->address(),
            'keterangan' => fake('id_ID')->paragraph(),
            'status' => fake()->randomElement(['Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan']),
        ];
    }
}
