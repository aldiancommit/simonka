<?php

use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Makassar'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('1. approving reschedule with null waktu_selesai_baru does not overwrite existing waktu_selesai to null', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00',
        'waktu_selesai' => '11:00',
        'status' => 'Disetujui',
    ]);

    $reschedule = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-20',
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => null,
        'status' => 'Menunggu',
    ]);

    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-20',
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => null,
        'alasan' => 'Penyesuaian jadwal dinas',
        'status' => 'Disetujui',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->waktu_selesai)->not->toBeNull()
        ->and($konsultasi->waktu_selesai)->toBe('11:00:00');
});

test('2. reverting reschedule from Disetujui to Ditolak restores previous consultation schedule', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:30:00',
        'status' => 'Disetujui',
    ]);

    $reschedule = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'status' => 'Menunggu',
    ]);

    // 1. Setujui reschedule
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'alasan' => 'Dinas luar kota',
        'status' => 'Disetujui',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-22');

    // 2. Ubah dari Disetujui ke Ditolak (harus mengembalikan jadwal ke 2026-10-15 09:00:00)
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'alasan' => 'Dibatalkan oleh pimpinan',
        'status' => 'Ditolak',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-15')
        ->and($konsultasi->waktu_mulai)->toBe('09:00:00');
});

test('3. approving a reschedule automatically marks other pending reschedules on the same consultation as Ditolak', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'status' => 'Disetujui',
    ]);

    $reschedule1 = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-20',
        'waktu_mulai_baru' => '10:00',
        'status' => 'Menunggu',
    ]);

    $reschedule2 = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-25',
        'waktu_mulai_baru' => '14:00',
        'status' => 'Menunggu',
    ]);

    // Setujui reschedule1
    $this->put(route('penjadwalan-ulang.update', $reschedule1), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-20',
        'waktu_mulai_baru' => '10:00',
        'alasan' => 'Pilihan jadwal A',
        'status' => 'Disetujui',
    ]);

    $reschedule2->refresh();
    expect($reschedule2->status)->toBe('Ditolak');
});

test('4. store reschedule populates tanggal_lama from consultation in database and ignores fake client input', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-18',
        'status' => 'Disetujui',
    ]);

    // Client sengaja mengirim tanggal_lama palsu '1999-01-01'
    $this->post(route('penjadwalan-ulang.store'), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '1999-01-01',
        'tanggal_baru' => '2026-10-28',
        'waktu_mulai_baru' => '10:00',
        'alasan' => 'Uji manipulasi tanggal_lama',
    ]);

    $savedReschedule = PenjadwalanUlang::where('konsultasi_id', $konsultasi->id)->latest('id')->first();
    expect($savedReschedule)->not->toBeNull()
        ->and($savedReschedule->tanggal_lama->format('Y-m-d'))->toBe('2026-10-18');
});

test('5. cannot create reschedule for consultation without Disetujui status', function (string $invalidStatus) {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-18',
        'status' => $invalidStatus,
    ]);

    $response = $this->post(route('penjadwalan-ulang.store'), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_baru' => '2026-10-28',
        'waktu_mulai_baru' => '10:00',
        'alasan' => 'Reschedule pada konsultasi yang bukan Disetujui',
    ]);

    $response->assertSessionHasErrors(['konsultasi_id']);
    expect(PenjadwalanUlang::where('konsultasi_id', $konsultasi->id)->count())->toBe(0);
})->with(['Menunggu', 'Ditolak', 'Dibatalkan', 'Selesai']);

test('6. multiple saves with Disetujui status are idempotent and preserve original snapshot', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:00:00',
        'status' => 'Disetujui',
    ]);

    $reschedule = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:00',
        'status' => 'Menunggu',
    ]);

    // 1st approve
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:00',
        'alasan' => 'Persetujuan pertama',
        'status' => 'Disetujui',
    ]);

    $reschedule->refresh();
    expect($reschedule->snapshot_tanggal_lama->format('Y-m-d'))->toBe('2026-10-15')
        ->and($reschedule->snapshot_waktu_mulai_lama)->toBe('09:00:00');

    // 2nd save with Disetujui (e.g. updating note or saving again)
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-25',
        'waktu_mulai_baru' => '14:00',
        'waktu_selesai_baru' => '15:00',
        'alasan' => 'Pembaruan catatan persetujuan',
        'status' => 'Disetujui',
    ]);

    $reschedule->refresh();
    // Snapshot should STILL preserve the original before-reschedule date (2026-10-15), not 2026-10-22
    expect($reschedule->snapshot_tanggal_lama->format('Y-m-d'))->toBe('2026-10-15')
        ->and($reschedule->snapshot_waktu_mulai_lama)->toBe('09:00:00');
});

test('7. create view dropdown only displays consultations with status Disetujui', function () {
    $approved = Konsultasi::factory()->create(['status' => 'Disetujui', 'nama_pemohon' => 'Pemohon Disetujui']);
    $pending = Konsultasi::factory()->create(['status' => 'Menunggu', 'nama_pemohon' => 'Pemohon Menunggu']);
    $rejected = Konsultasi::factory()->create(['status' => 'Ditolak', 'nama_pemohon' => 'Pemohon Ditolak']);

    $response = $this->get(route('penjadwalan-ulang.create'));

    $response->assertOk();
    $response->assertSee($approved->nama_pemohon);
    $response->assertDontSee($pending->nama_pemohon);
    $response->assertDontSee($rejected->nama_pemohon);
});

test('8. cannot approve reschedule if parent consultation status is not Disetujui', function (string $parentStatus) {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'status' => $parentStatus,
    ]);

    $reschedule = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'status' => 'Menunggu',
    ]);

    $response = $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'alasan' => 'Mencoba menyetujui saat konsultasi tidak berstatus Disetujui',
        'status' => 'Disetujui',
    ]);

    $response->assertSessionHasErrors(['status']);
    $reschedule->refresh();
    expect($reschedule->status)->toBe('Menunggu');
})->with(['Menunggu', 'Ditolak', 'Dibatalkan', 'Selesai']);

test('9. reverting reschedule from Disetujui to Menunggu restores previous consultation schedule', function () {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-15',
        'waktu_mulai' => '09:00:00',
        'waktu_selesai' => '10:30:00',
        'status' => 'Disetujui',
    ]);

    $reschedule = PenjadwalanUlang::factory()->create([
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'status' => 'Menunggu',
    ]);

    // 1. Setujui reschedule
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'alasan' => 'Dinas luar kota',
        'status' => 'Disetujui',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-22');

    // 2. Ubah dari Disetujui kembali ke Menunggu
    $this->put(route('penjadwalan-ulang.update', $reschedule), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-15',
        'tanggal_baru' => '2026-10-22',
        'waktu_mulai_baru' => '13:00',
        'waktu_selesai_baru' => '14:30',
        'alasan' => 'Perlu peninjauan ulang oleh admin',
        'status' => 'Menunggu',
    ]);

    $konsultasi->refresh();
    expect($konsultasi->tanggal_konsultasi->format('Y-m-d'))->toBe('2026-10-15')
        ->and($konsultasi->waktu_mulai)->toBe('09:00:00')
        ->and($konsultasi->waktu_selesai)->toBe('10:30:00');
});
