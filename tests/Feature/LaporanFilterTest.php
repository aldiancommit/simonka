<?php

test('invalid report date ranges are rejected before querying records', function (string $start, string $end, string $errorField) {
    $response = $this->get('/laporan?'.http_build_query([
        'filter' => '1',
        'jenis' => 'konsultasi',
        'mulai' => $start,
        'sampai' => $end,
    ]));

    $response->assertRedirect();
    $response->assertSessionHasErrors([$errorField]);
})->with([
    'invalid date format' => ['invalid-date', '2026-10-10', 'mulai'],
    'end before start' => ['2026-10-10', '2026-10-09', 'sampai'],
]);
