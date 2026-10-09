<?php

use App\Enums\Role;
use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;

test('1. store konsultasi forces status to Menunggu for admin and sekretariat even if Disetujui is submitted', function (Role $role) {
    $this->actingAsRole($role);

    $response = $this->post(route('konsultasi.store'), [
        'nama_pemohon' => 'Pemohon Uji Status',
        'instansi' => 'Dinas Kesehatan',
        'perihal' => 'Koordinasi Anggaran',
        'tanggal_konsultasi' => now()->addDays(3)->format('Y-m-d'),
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('konsultasi.index'));

    $saved = Konsultasi::where('nama_pemohon', 'Pemohon Uji Status')->first();
    expect($saved)->not->toBeNull()
        ->and($saved->status)->toBe('Menunggu');
})->with([Role::Admin, Role::Sekretariat]);

test('2. store reschedule forces status to Menunggu for admin and sekretariat even if Disetujui is submitted', function (Role $role) {
    $this->actingAsRole($role);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Disetujui']);

    $response = $this->post(route('penjadwalan-ulang.store'), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_baru' => now()->addDays(5)->format('Y-m-d'),
        'waktu_mulai_baru' => '10:00',
        'waktu_selesai_baru' => '11:30',
        'alasan' => 'Uji paksa status menunggu',
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));

    $saved = PenjadwalanUlang::where('konsultasi_id', $konsultasi->id)->latest('id')->first();
    expect($saved)->not->toBeNull()
        ->and($saved->status)->toBe('Menunggu');
})->with([Role::Admin, Role::Sekretariat]);

test('3. sekretariat update konsultasi is forbidden (403) from submitting Disetujui or Ditolak status', function (string $forbiddenStatus) {
    $this->actingAsRole(Role::Sekretariat);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'nama_pemohon' => $konsultasi->nama_pemohon,
        'perihal' => $konsultasi->perihal,
        'tanggal_konsultasi' => $konsultasi->tanggal_konsultasi->format('Y-m-d'),
        'waktu_mulai' => '09:00',
        'status' => $forbiddenStatus,
    ]);

    $response->assertStatus(403);
})->with(['Disetujui', 'Ditolak']);

test('4. sekretariat update konsultasi on Disetujui status is forbidden (403) from modifying schedule dates and times', function (array $modifiedPayload) {
    $this->actingAsRole(Role::Sekretariat);

    $konsultasi = Konsultasi::factory()->create([
        'status' => 'Disetujui',
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:00:00',
    ]);

    $payload = array_merge([
        'nama_pemohon' => $konsultasi->nama_pemohon,
        'perihal' => $konsultasi->perihal,
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'status' => 'Disetujui',
    ], $modifiedPayload);

    $response = $this->put(route('konsultasi.update', $konsultasi), $payload);

    $response->assertStatus(403);
})->with([
    'change date' => [['tanggal_konsultasi' => '2026-10-21']],
    'change start time by 1 minute' => [['waktu_mulai' => '09:01']],
    'change end time' => [['waktu_selesai' => '10:30']],
]);

test('5. sekretariat update konsultasi on Disetujui status succeeds (302) when resending identical normalized schedule', function () {
    $this->actingAsRole(Role::Sekretariat);

    $konsultasi = Konsultasi::factory()->create([
        'status' => 'Disetujui',
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:00:00',
    ]);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'nama_pemohon' => 'Nama Baru Pemohon',
        'instansi' => 'Instansi Baru',
        'perihal' => 'Perihal Diperbarui',
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'status' => 'Disetujui',
        'catatan' => 'Catatan diperbarui sekretariat',
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $konsultasi->refresh();
    expect($konsultasi->nama_pemohon)->toBe('Nama Baru Pemohon');
});

test('6. pimpinan update konsultasi is forbidden (403) when sending non-whitelisted fields', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'status' => 'Disetujui',
        'catatan' => 'Telaah pimpinan disetujui',
        'nama_pemohon' => 'Manipulated Name',
    ]);

    $response->assertStatus(403);
});

test('7. pimpinan update konsultasi succeeds (302) when sending only status and catatan', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'status' => 'Disetujui',
        'catatan' => 'Disetujui oleh Pimpinan',
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('Disetujui')
        ->and($konsultasi->catatan)->toBe('Disetujui oleh Pimpinan');
});

test('8. sekretariat update reschedule is forbidden (403) from approving or rejecting', function (string $status) {
    $this->actingAsRole(Role::Sekretariat);

    $pu = PenjadwalanUlang::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'konsultasi_id' => $pu->konsultasi_id,
        'tanggal_lama' => $pu->tanggal_lama->format('Y-m-d'),
        'tanggal_baru' => $pu->tanggal_baru->format('Y-m-d'),
        'waktu_mulai_baru' => '14:00',
        'alasan' => $pu->alasan,
        'status' => $status,
    ]);

    $response->assertStatus(403);
})->with(['Disetujui', 'Ditolak']);

test('9. pimpinan update reschedule is forbidden (403) when sending fields other than status', function () {
    $this->actingAsRole(Role::Pimpinan);

    $pu = PenjadwalanUlang::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'status' => 'Disetujui',
        'alasan' => 'Mencoba mengubah alasan',
    ]);

    $response->assertStatus(403);
});

test('10. pimpinan update reschedule succeeds (302) when sending only status', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Disetujui']);
    $pu = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'status' => 'Menunggu',
    ]);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));
    $pu->refresh();
    expect($pu->status)->toBe('Disetujui');
});

test('11. jadwal kegiatan mutation is forbidden (403) for pimpinan', function () {
    $this->actingAsRole(Role::Pimpinan);

    $jadwal = JadwalKegiatan::factory()->create();

    $this->get(route('agenda.jadwal.create'))->assertStatus(403);

    $this->post(route('agenda.jadwal.store'), [
        'nama_kegiatan' => 'Kegiatan Baru',
        'tanggal' => now()->addDays(2)->format('Y-m-d'),
        'waktu_mulai' => '09:00',
    ])->assertStatus(403);

    $this->get(route('agenda.jadwal.edit', ['jadwal' => $jadwal]))->assertStatus(403);

    $this->put(route('agenda.jadwal.update', ['jadwal' => $jadwal]), [
        'nama_kegiatan' => 'Update Kegiatan',
        'tanggal' => $jadwal->tanggal->format('Y-m-d'),
        'waktu_mulai' => '10:00',
    ])->assertStatus(403);

    $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]))->assertStatus(403);
});

test('12. non-admin roles are forbidden (403) from deleting resources', function (Role $role) {
    $this->actingAsRole($role);

    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $pu = PenjadwalanUlang::factory()->create();

    $this->delete(route('konsultasi.destroy', $konsultasi))->assertStatus(403);
    $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]))->assertStatus(403);
    $this->delete(route('penjadwalan-ulang.destroy', $pu))->assertStatus(403);
})->with([Role::Pimpinan, Role::Sekretariat]);

test('13. admin can delete resources', function () {
    $this->actingAsRole(Role::Admin);

    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $pu = PenjadwalanUlang::factory()->create();

    $this->delete(route('konsultasi.destroy', $konsultasi))->assertRedirect(route('konsultasi.index'));
    $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]))->assertRedirect(route('agenda.jadwal.index'));
    $this->delete(route('penjadwalan-ulang.destroy', $pu))->assertRedirect(route('penjadwalan-ulang.index'));
});

test('14. IDOR protection: accessing non-existent record returns 404', function () {
    $this->actingAsRole(Role::Sekretariat);

    $this->get('/konsultasi/999999')->assertNotFound();
    $this->get('/agenda/jadwal/999999')->assertNotFound();
    $this->get('/penjadwalan-ulang/999999')->assertNotFound();
});
