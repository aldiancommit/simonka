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

test('5. cannot create reschedule for consultation with terminal status Ditolak Dibatalkan or Selesai', function (string $terminalStatus) {
    $konsultasi = Konsultasi::factory()->create([
        'tanggal_konsultasi' => '2026-10-18',
        'status' => $terminalStatus,
    ]);

    $response = $this->post(route('penjadwalan-ulang.store'), [
        'konsultasi_id' => $konsultasi->id,
        'tanggal_lama' => '2026-10-18',
        'tanggal_baru' => '2026-10-28',
        'waktu_mulai_baru' => '10:00',
        'alasan' => 'Reschedule pada konsultasi yang sudah non-aktif',
    ]);

    $response->assertSessionHasErrors(['konsultasi_id']);
    expect(PenjadwalanUlang::where('konsultasi_id', $konsultasi->id)->count())->toBe(0);
})->with(['Ditolak', 'Dibatalkan', 'Selesai']);
