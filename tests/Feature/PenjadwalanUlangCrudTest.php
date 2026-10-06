<?php

use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('penjadwalan ulang can be created with valid data', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => now()->addDays(5)->format('Y-m-d'),
        'waktu_mulai' => '09:00',
    ]);

    $data = [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => $konsultasi->tanggal_konsultasi->format('Y-m-d'),
        'tanggal_baru' => now()->addDays(7)->format('Y-m-d'),
        'waktu_mulai_baru' => '13:30',
        'waktu_selesai_baru' => '15:00',
        'alasan' => 'Permintaan pemohon karena bertepatan dengan dinas luar kota',
        'status' => 'Menunggu',
    ];

    $response = $this->post(route('penjadwalan-ulang.store'), $data);

    $response->assertRedirect(route('penjadwalan-ulang.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('penjadwalan_ulangs', [
        'konsultasi_id' => $konsultasi->id,
        'status' => 'Menunggu',
    ]);
});

test('approving penjadwalan ulang synchronizes konsultasi schedule atomically', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => now()->addDays(4)->format('Y-m-d'),
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
    ]);

    $pu = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => $konsultasi->tanggal_konsultasi->format('Y-m-d'),
        'tanggal_baru' => now()->addDays(8)->format('Y-m-d'),
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => '15:30',
        'status' => 'Menunggu',
    ]);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => $pu->tanggal_lama->format('Y-m-d'),
        'tanggal_baru' => $pu->tanggal_baru->format('Y-m-d'),
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => '15:30',
        'alasan' => $pu->alasan,
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('penjadwalan_ulangs', [
        'id' => $pu->id,
        'status' => 'Disetujui',
    ]);

    $this->assertDatabaseHas('konsultasis', [
        'id' => $konsultasi->id,
        'tanggal_konsultasi' => $pu->tanggal_baru->format('Y-m-d'),
        'waktu_mulai' => '14:00:00',
    ]);
});

test('penjadwalan ulang can be deleted', function () {
    $pu = PenjadwalanUlang::factory()->create();

    $response = $this->delete(route('penjadwalan-ulang.destroy', $pu));

    $response->assertRedirect(route('penjadwalan-ulang.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('penjadwalan_ulangs', [
        'id' => $pu->id,
    ]);
});
