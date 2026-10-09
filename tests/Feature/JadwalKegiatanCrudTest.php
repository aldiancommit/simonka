<?php

use App\Models\JadwalKegiatan;

beforeEach(function () {
    $this->actingAsRole();
});

test('jadwal kegiatan can be created with valid data', function () {
    $data = [
        'nama_kegiatan' => 'Rapat Paripurna DPRD Prov. Jabar',
        'tanggal' => now()->addDays(3)->format('Y-m-d'),
        'waktu_mulai' => '08:30',
        'waktu_selesai' => '11:30',
        'lokasi' => 'Gedung DPRD Prov. Jabar',
        'keterangan' => 'Menghadiri sidang pembahasan anggaran',
        'status' => 'Terjadwal',
    ];

    $response = $this->post(route('agenda.jadwal.store'), $data);

    $response->assertRedirect(route('agenda.jadwal.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('jadwal_kegiatans', [
        'nama_kegiatan' => 'Rapat Paripurna DPRD Prov. Jabar',
        'lokasi' => 'Gedung DPRD Prov. Jabar',
    ]);
});

test('jadwal kegiatan can be updated', function () {
    $jadwal = JadwalKegiatan::factory()->create([
        'status' => 'Terjadwal',
    ]);

    $response = $this->put(route('agenda.jadwal.update', ['jadwal' => $jadwal]), [
        'nama_kegiatan' => 'Rapat Koordinasi Updated',
        'tanggal' => $jadwal->tanggal->format('Y-m-d'),
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '11:00',
        'lokasi' => 'Ruang Rapat Utama',
        'status' => 'Selesai',
    ]);

    $response->assertRedirect(route('agenda.jadwal.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('jadwal_kegiatans', [
        'id' => $jadwal->id,
        'status' => 'Selesai',
        'nama_kegiatan' => 'Rapat Koordinasi Updated',
    ]);
});

test('jadwal kegiatan can be deleted', function () {
    $jadwal = JadwalKegiatan::factory()->create();

    $response = $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]));

    $response->assertRedirect(route('agenda.jadwal.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('jadwal_kegiatans', [
        'id' => $jadwal->id,
    ]);
});
