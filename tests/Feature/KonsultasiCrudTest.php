<?php

use App\Models\Konsultasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('konsultasi can be created with valid data', function () {
    $data = [
        'nama_pemohon' => 'Ir. Hendra Gunawan',
        'instansi' => 'Bappeda Prov. Jawa Barat',
        'no_telepon' => '081298765432',
        'email' => 'hendra@bappeda.go.id',
        'perihal' => 'Koordinasi evaluasi program triwulan',
        'tanggal_konsultasi' => now()->addDays(2)->format('Y-m-d'),
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:30',
        'status' => 'Menunggu',
        'catatan' => 'Membawa dokumen paparan capaian kinerja',
    ];

    $response = $this->post(route('konsultasi.store'), $data);

    $response->assertRedirect(route('konsultasi.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('konsultasis', [
        'nama_pemohon' => 'Ir. Hendra Gunawan',
        'instansi' => 'Bappeda Prov. Jawa Barat',
    ]);
});

test('konsultasi can be updated', function () {
    $konsultasi = Konsultasi::factory()->create([
        'status' => 'Menunggu',
    ]);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'nama_pemohon' => $konsultasi->nama_pemohon,
        'perihal' => 'Pembaruan perihal konsultasi strategis',
        'tanggal_konsultasi' => $konsultasi->tanggal_konsultasi->format('Y-m-d'),
        'waktu_mulai' => '10:00',
        'waktu_selesai' => '11:00',
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('konsultasis', [
        'id' => $konsultasi->id,
        'status' => 'Disetujui',
        'perihal' => 'Pembaruan perihal konsultasi strategis',
    ]);
});

test('konsultasi can be deleted', function () {
    $konsultasi = Konsultasi::factory()->create();

    $response = $this->delete(route('konsultasi.destroy', $konsultasi));

    $response->assertRedirect(route('konsultasi.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('konsultasis', [
        'id' => $konsultasi->id,
    ]);
});
