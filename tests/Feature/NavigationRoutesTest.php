<?php

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;

beforeEach(function () {
    $this->actingAsRole();
});

test('dashboard and all static navigation routes return successful responses', function (string $route) {
    $response = $this->get($route);

    $response->assertStatus(200);
})->with([
    '/',
    '/agenda',
    '/agenda/jadwal',
    '/agenda/jadwal/create',
    '/agenda/kalender',
    '/konsultasi',
    '/konsultasi/create',
    '/penjadwalan-ulang',
    '/penjadwalan-ulang/create',
    '/riwayat',
    '/riwayat?tab=jadwal',
    '/laporan',
]);

test('dynamic detail and edit routes return successful responses', function () {
    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $penjadwalan = PenjadwalanUlang::factory()->create();

    $this->get(route('konsultasi.show', $konsultasi))->assertStatus(200);
    $this->get(route('konsultasi.edit', $konsultasi))->assertStatus(200);

    $this->get(route('agenda.jadwal.show', ['jadwal' => $jadwal]))->assertStatus(200);
    $this->get(route('agenda.jadwal.edit', ['jadwal' => $jadwal]))->assertStatus(200);

    $this->get(route('penjadwalan-ulang.show', $penjadwalan))->assertStatus(200);
    $this->get(route('penjadwalan-ulang.edit', $penjadwalan))->assertStatus(200);
});
