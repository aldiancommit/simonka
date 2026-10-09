<?php

use App\Models\Konsultasi;

beforeEach(function () {
    $this->actingAsRole();
});

test('invalid report date ranges are rejected before querying records', function (string $start, string $end, string $errorField) {
    $response = $this->get('/laporan?'.http_build_query([
        'filter' => '1',
        'jenis' => 'konsultasi',
        'mulai' => $start,
        'sampai' => $end,
    ]));

    $response->assertRedirect();
    $response->assertSessionHasErrors([$errorField]);
})->with([
    'invalid date format' => ['invalid-date', '2026-10-10', 'mulai'],
    'end before start' => ['2026-10-10', '2026-10-09', 'sampai'],
]);

test('laporan view renders official kop surat with logo, kesbangpol agency name, and signature block', function () {
    Konsultasi::factory()->create([
        'nama_pemohon' => 'Ir. H. Syamsuddin',
        'instansi' => 'Dinas Pendidikan',
        'perihal' => 'Koordinasi Kerukunan Umat',
        'tanggal_konsultasi' => '2026-10-15',
        'status' => 'Disetujui',
    ]);

    $response = $this->get('/laporan?'.http_build_query([
        'filter' => '1',
        'jenis' => 'konsultasi',
        'mulai' => '2026-10-01',
        'sampai' => '2026-10-31',
        'status' => 'Semua',
    ]));

    $response->assertOk();
    $response->assertSee('assets/images/logo-palu.png', false);
    $response->assertSee('PEMERINTAH KOTA PALU');
    $response->assertSee('BADAN KESATUAN BANGSA DAN POLITIK (KESBANGPOL)');
    $response->assertSee('SIMONKA');
    $response->assertSee('LAPORAN REKAPITULASI PERMOHONAN KONSULTASI');
    $response->assertSee('Ir. H. Syamsuddin');
    $response->assertSee('Dinas Pendidikan');
    $response->assertSee('Koordinasi Kerukunan Umat');
    $response->assertSee('Kepala Badan Kesatuan Bangsa dan Politik');
});
