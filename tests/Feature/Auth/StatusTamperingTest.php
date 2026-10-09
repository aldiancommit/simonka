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

test('3. sekretariat update konsultasi forbidden status transitions return 403', function (string $currentStatus, string $targetStatus) {
    $this->actingAsRole(Role::Sekretariat);

    $konsultasi = Konsultasi::factory()->create([
        'status' => $currentStatus,
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:00:00',
    ]);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'nama_pemohon' => $konsultasi->nama_pemohon,
        'perihal' => $konsultasi->perihal,
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'status' => $targetStatus,
    ]);

    $response->assertStatus(403);
})->with([
    'Disetujui to Menunggu' => ['Disetujui', 'Menunggu'],
    'Ditolak to Menunggu' => ['Ditolak', 'Menunggu'],
    'Menunggu to Disetujui' => ['Menunggu', 'Disetujui'],
    'Menunggu to Ditolak' => ['Menunggu', 'Ditolak'],
    'Selesai to Disetujui' => ['Selesai', 'Disetujui'],
    'Dibatalkan to Disetujui' => ['Dibatalkan', 'Disetujui'],
    'Ditolak to Disetujui' => ['Ditolak', 'Disetujui'],
]);

test('4. sekretariat update konsultasi allowed status transitions succeed (302)', function (string $currentStatus, string $targetStatus) {
    $this->actingAsRole(Role::Sekretariat);

    $konsultasi = Konsultasi::factory()->create([
        'status' => $currentStatus,
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:00:00',
    ]);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'nama_pemohon' => 'Perubahan Pemohon',
        'perihal' => 'Perubahan Perihal',
        'tanggal_konsultasi' => '2026-10-20',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '10:00',
        'status' => $targetStatus,
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $konsultasi->refresh();
    expect($konsultasi->status)->toBe($targetStatus);
})->with([
    'Disetujui to Disetujui (same status)' => ['Disetujui', 'Disetujui'],
    'Disetujui to Selesai' => ['Disetujui', 'Selesai'],
    'Disetujui to Dibatalkan' => ['Disetujui', 'Dibatalkan'],
]);

test('5. sekretariat update konsultasi on Disetujui status is forbidden (403) from modifying schedule dates and times', function (array $modifiedPayload) {
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

test('6. sekretariat update konsultasi on Disetujui status succeeds (302) when resending identical normalized schedule', function () {
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

test('7. pimpinan update konsultasi is forbidden (403) when sending non-whitelisted fields', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'status' => 'Disetujui',
        'catatan' => 'Telaah pimpinan disetujui',
        'nama_pemohon' => 'Manipulated Name',
    ]);

    $response->assertStatus(403);
});

test('8. pimpinan update konsultasi succeeds (302) when sending only status and catatan and does not mutate other fields', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create([
        'nama_pemohon' => 'Dr. H. Ahmad Dahlan',
        'instansi' => 'Inspektorat Kota Palu',
        'no_telepon' => '081122334455',
        'email' => 'ahmad@palukota.go.id',
        'perihal' => 'Pengawasan Berkala Triwulan',
        'tanggal_konsultasi' => '2026-10-25',
        'waktu_mulai' => '08:30:00',
        'waktu_selesai' => '10:00:00',
        'status' => 'Menunggu',
        'catatan' => 'Catatan awal sekretariat',
    ]);

    $response = $this->put(route('konsultasi.update', $konsultasi), [
        'status' => 'Disetujui',
        'catatan' => 'Disetujui oleh Pimpinan untuk diagendakan',
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $konsultasi->refresh();

    expect($konsultasi->status)->toBe('Disetujui')
        ->and($konsultasi->catatan)->toBe('Disetujui oleh Pimpinan untuk diagendakan')
        ->and($konsultasi->nama_pemohon)->toBe('Dr. H. Ahmad Dahlan')
        ->and($konsultasi->instansi)->toBe('Inspektorat Kota Palu')
        ->and($konsultasi->no_telepon)->toBe('081122334455')
        ->and($konsultasi->email)->toBe('ahmad@palukota.go.id')
        ->and($konsultasi->perihal)->toBe('Pengawasan Berkala Triwulan')
        ->and($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-25')
        ->and($konsultasi->waktu_mulai)->toBe('08:30:00')
        ->and($konsultasi->waktu_selesai)->toBe('10:00:00');
});

test('9. sekretariat update reschedule is forbidden (403) from approving or rejecting', function (string $status) {
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

test('10. sekretariat update reschedule is forbidden (403) from editing fields when status is Disetujui or Ditolak', function (string $rescheduleStatus) {
    $this->actingAsRole(Role::Sekretariat);

    $pu = PenjadwalanUlang::factory()->create(['status' => $rescheduleStatus]);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'konsultasi_id' => $pu->konsultasi_id,
        'tanggal_lama' => $pu->tanggal_lama->format('Y-m-d'),
        'tanggal_baru' => now()->addDays(10)->format('Y-m-d'),
        'waktu_mulai_baru' => '15:00',
        'alasan' => 'Mencoba mengedit reschedule yang sudah diputuskan',
        'status' => $rescheduleStatus,
    ]);

    $response->assertStatus(403);
})->with(['Disetujui', 'Ditolak']);

test('11. sekretariat update reschedule succeeds (302) when current status is Menunggu', function () {
    $this->actingAsRole(Role::Sekretariat);

    $pu = PenjadwalanUlang::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'konsultasi_id' => $pu->konsultasi_id,
        'tanggal_lama' => $pu->tanggal_lama->format('Y-m-d'),
        'tanggal_baru' => now()->addDays(12)->format('Y-m-d'),
        'waktu_mulai_baru' => '15:30',
        'waktu_selesai_baru' => '17:00',
        'alasan' => 'Pembaruan alasan reschedule oleh sekretariat',
        'status' => 'Menunggu',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));
    $pu->refresh();
    expect($pu->alasan)->toBe('Pembaruan alasan reschedule oleh sekretariat')
        ->and($pu->waktu_mulai_baru)->toBe('15:30:00');
});

test('12. pimpinan update reschedule is forbidden (403) when sending fields other than status', function () {
    $this->actingAsRole(Role::Pimpinan);

    $pu = PenjadwalanUlang::factory()->create(['status' => 'Menunggu']);

    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'status' => 'Disetujui',
        'alasan' => 'Mencoba mengubah alasan',
    ]);

    $response->assertStatus(403);
});

test('13. pimpinan update reschedule with status Disetujui updates parent consultation schedule, fills snapshot, and rejects sibling reschedules', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:30:00',
        'status' => 'Disetujui',
    ]);

    $pu1 = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => '15:30',
        'alasan' => 'Permintaan pergeseran jadwal ke minggu depan',
        'status' => 'Menunggu',
    ]);

    $pu2 = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-25',
        'waktu_mulai_baru' => '10:00',
        'alasan' => 'Permintaan alternatif B',
        'status' => 'Menunggu',
    ]);

    $response = $this->put(route('penjadwalan-ulang.update', $pu1), [
        'status' => 'Disetujui',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));

    $pu1->refresh();
    $pu2->refresh();
    $konsultasi->refresh();

    // 1. Reschedule 1 is Disetujui, and snapshot is saved
    expect($pu1->status)->toBe('Disetujui')
        ->and($pu1->snapshot_tanggal_lama->format('Y-m-d'))->toBe('2026-10-15')
        ->and($pu1->snapshot_waktu_mulai_lama)->toBe('09:00:00')
        ->and($pu1->snapshot_waktu_selesai_lama)->toBe('10:30:00')
        ->and($pu1->alasan)->toBe('Permintaan pergeseran jadwal ke minggu depan'); // Proposal field untouched

    // 2. Parent consultation schedule is updated to new date/time
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-22')
        ->and($konsultasi->waktu_mulai)->toBe('14:00:00')
        ->and($konsultasi->waktu_selesai)->toBe('15:30:00');

    // 3. Sibling reschedule is automatically marked Ditolak
    expect($pu2->status)->toBe('Ditolak');
});

test('14. pimpinan update reschedule with status Ditolak restores consultation schedule from snapshot and leaves proposal fields untouched', function () {
    $this->actingAsRole(Role::Pimpinan);

    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:30:00',
        'status' => 'Disetujui',
    ]);

    $pu = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-28',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'alasan' => 'Dinas luar kota ke Jakarta',
        'status' => 'Menunggu',
    ]);

    // 1. Setujui dulu oleh pimpinan
    $this->put(route('penjadwalan-ulang.update', $pu), [
        'status' => 'Disetujui',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-28');

    // 2. Pimpinan kemudian mengubah putusan menjadi Ditolak
    $response = $this->put(route('penjadwalan-ulang.update', $pu), [
        'status' => 'Ditolak',
    ]);

    $response->assertRedirect(route('penjadwalan-ulang.index'));

    $pu->refresh();
    $konsultasi->refresh();

    expect($pu->status)->toBe('Ditolak')
        ->and($pu->alasan)->toBe('Dinas luar kota ke Jakarta')
        ->and($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-15')
        ->and($konsultasi->waktu_mulai)->toBe('09:00:00')
        ->and($konsultasi->waktu_selesai)->toBe('10:30:00');
});

test('15. jadwal kegiatan mutation is forbidden (403) for pimpinan', function () {
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

test('16. non-admin roles are forbidden (403) from deleting resources', function (Role $role) {
    $this->actingAsRole($role);

    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $pu = PenjadwalanUlang::factory()->create();

    $this->delete(route('konsultasi.destroy', $konsultasi))->assertStatus(403);
    $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]))->assertStatus(403);
    $this->delete(route('penjadwalan-ulang.destroy', $pu))->assertStatus(403);
})->with([Role::Pimpinan, Role::Sekretariat]);

test('17. admin can delete resources', function () {
    $this->actingAsRole(Role::Admin);

    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $pu = PenjadwalanUlang::factory()->create();

    $this->delete(route('konsultasi.destroy', $konsultasi))->assertRedirect(route('konsultasi.index'));
    $this->delete(route('agenda.jadwal.destroy', ['jadwal' => $jadwal]))->assertRedirect(route('agenda.jadwal.index'));
    $this->delete(route('penjadwalan-ulang.destroy', $pu))->assertRedirect(route('penjadwalan-ulang.index'));
});

test('18. IDOR protection: accessing non-existent record returns 404', function () {
    $this->actingAsRole(Role::Sekretariat);

    $this->get('/konsultasi/999999')->assertNotFound();
    $this->get('/agenda/jadwal/999999')->assertNotFound();
    $this->get('/penjadwalan-ulang/999999')->assertNotFound();
});
