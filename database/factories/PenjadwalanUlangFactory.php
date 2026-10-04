<?php

namespace Database\Factories;

use App\Models\Konsultasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenjadwalanUlangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'konsultasi_id' => Konsultasi::factory(),
            'tanggal_lama' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'tanggal_baru' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'waktu_mulai_baru' => fake()->time('H:i'),
            'waktu_selesai_baru' => fake()->time('H:i'),
            'alasan' => fake('id_ID')->sentence(),
            'status' => fake()->randomElement(['Menunggu', 'Disetujui', 'Ditolak']),
        ];
    }
}
