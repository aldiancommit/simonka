<?php

use App\Models\Konsultasi;

beforeEach(function () {
    $this->actingAsRole();
});

test('alert carrier renders flash success on navigated page', function () {
    $response = $this->withSession([
        'success' => 'Permohonan konsultasi berhasil ditambahkan.',
    ])->get(route('konsultasi.index'));

    $response->assertOk();
    $response->assertSee('data-simonka-flash', false);
    $response->assertSee('Permohonan konsultasi berhasil ditambahkan.', false);
    $response->assertSee('data-flash-type="success"', false);
});

test('alert carrier renders flash error on navigated page', function () {
    $response = $this->withSession([
        'error' => 'Gagal memproses data permohonan.',
    ])->get(route('konsultasi.index'));

    $response->assertOk();
    $response->assertSee('data-simonka-flash', false);
    $response->assertSee('Gagal memproses data permohonan.', false);
    $response->assertSee('data-flash-type="error"', false);
});

test('table views include modern confirm delete attributes', function () {
    $konsultasi = Konsultasi::factory()->create();

    $response = $this->get(route('konsultasi.index'));

    $response->assertOk();
    $response->assertSee('data-confirm-title="Hapus Permohonan Konsultasi"', false);
    $response->assertSee('data-confirm-btn="Ya, Hapus Data"', false);
});
