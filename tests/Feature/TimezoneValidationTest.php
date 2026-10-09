<?php

use App\Http\Requests\StoreKonsultasiRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

test('validation after_or_equal:today correctly uses local WITA date during early morning hours', function () {
    // Freeze time to 2026-10-08 02:00:00 in Asia/Makassar (WITA)
    // In UTC, this is still 2026-10-07 18:00:00
    Carbon::setTestNow(Carbon::parse('2026-10-08 02:00:00', 'Asia/Makassar'));

    $rules = (new StoreKonsultasiRequest)->rules();

    // 1. Tanggal 2026-10-07 sudah lampau di WITA (harus DITOLAK)
    $yesterdayData = [
        'nama_pemohon' => 'Budi Santoso',
        'perihal' => 'Konsultasi Koordinasi Wilayah',
        'tanggal_konsultasi' => '2026-10-07',
        'waktu_mulai' => '09:00',
    ];

    $validatorYesterday = Validator::make($yesterdayData, [
        'tanggal_konsultasi' => $rules['tanggal_konsultasi'],
    ]);

    expect($validatorYesterday->fails())->toBeTrue(
        'Validation should FAIL for 2026-10-07 because at 02:00 WITA on Oct 8, Oct 7 is already yesterday.'
    );

    // 2. Tanggal 2026-10-08 adalah hari ini di WITA (harus DITERIMA)
    $todayData = [
        'nama_pemohon' => 'Budi Santoso',
        'perihal' => 'Konsultasi Koordinasi Wilayah',
        'tanggal_konsultasi' => '2026-10-08',
        'waktu_mulai' => '09:00',
    ];

    $validatorToday = Validator::make($todayData, [
        'tanggal_konsultasi' => $rules['tanggal_konsultasi'],
    ]);

    expect($validatorToday->passes())->toBeTrue(
        'Validation should PASS for 2026-10-08 because it is today in WITA.'
    );

    Carbon::setTestNow(); // Reset
});
