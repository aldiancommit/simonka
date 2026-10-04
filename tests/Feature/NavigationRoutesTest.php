<?php

test('dashboard and all navigation routes return successful responses', function (string $route) {
    $response = $this->get($route);

    $response->assertStatus(200);
})->with([
    '/',
    '/agenda/jadwal',
    '/agenda/jadwal/create',
    '/agenda/jadwal/1/edit',
    '/agenda/jadwal/1/delete',
    '/agenda/kalender',
    '/agenda/kalender/create',
    '/agenda/kalender/1/edit',
    '/agenda/kalender/1/delete',
    '/konsultasi',
    '/konsultasi/create',
    '/konsultasi/1/edit',
    '/konsultasi/1/delete',
    '/penjadwalan-ulang',
    '/penjadwalan-ulang/create',
    '/penjadwalan-ulang/1/edit',
    '/penjadwalan-ulang/1/delete',
    '/riwayat',
    '/riwayat/create',
    '/riwayat/1/edit',
    '/riwayat/1/delete',
    '/laporan',
    '/laporan/create',
    '/laporan/1/edit',
    '/laporan/1/delete',
]);
