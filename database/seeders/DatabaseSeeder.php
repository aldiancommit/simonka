<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Konsultasi::factory(15)->create();
        JadwalKegiatan::factory(10)->create();
        PenjadwalanUlang::factory(5)->create();
    }
}
