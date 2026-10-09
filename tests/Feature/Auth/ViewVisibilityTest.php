<?php

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use App\Models\User;
use Database\Seeders\DemoSeeder;

test('1. unauthenticated guest visiting root redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('2. header displays authenticated user name and role badge with native POST logout form', function () {
    $user = User::factory()->pimpinan()->create(['name' => 'Bapak Kepala Badan']);

    $response = $this->actingAs($user)->get('/');

    $response->assertSee('Bapak Kepala Badan');
    $response->assertSee('Pimpinan');
    $response->assertSee(route('logout'));
    $response->assertDontSee('<a href="#" class="dropdown-item rounded-2 py-2 px-3 fw-semibold text-danger', false);
});

test('3. pimpinan does not see Tambah or Hapus buttons on index views', function () {
    $konsultasi = Konsultasi::factory()->create();
    $jadwal = JadwalKegiatan::factory()->create();
    $pu = PenjadwalanUlang::factory()->create();
    $user = User::factory()->pimpinan()->create();

    $responseKonsultasi = $this->actingAs($user)->get(route('konsultasi.index'));
    $responseKonsultasi->assertDontSee('Tambah Konsultasi');
    $responseKonsultasi->assertDontSee('Tambah Baru');
    $responseKonsultasi->assertDontSee('Hapus');

    $responseJadwal = $this->actingAs($user)->get(route('agenda.jadwal.index'));
    $responseJadwal->assertDontSee('Tambah Jadwal');
    $responseJadwal->assertDontSee('Hapus');

    $responsePu = $this->actingAs($user)->get(route('penjadwalan-ulang.index'));
    $responsePu->assertDontSee('Ajukan Reschedule');
    $responsePu->assertDontSee('Tambah Reschedule');
    $responsePu->assertDontSee('Hapus');
});

test('4. pimpinan edit konsultasi form only contains status and catatan inputs and displays data as plain text', function () {
    $konsultasi = Konsultasi::factory()->create([
        'nama_pemohon' => 'Drs. H. Mulyadi',
        'perihal' => 'Audiensi Strategis',
        'status' => 'Menunggu',
    ]);
    $user = User::factory()->pimpinan()->create();

    $response = $this->actingAs($user)->get(route('konsultasi.edit', $konsultasi));

    $response->assertSee('Drs. H. Mulyadi');
    $response->assertSee('Audiensi Strategis');
    $response->assertDontSee('name="nama_pemohon"', false);
    $response->assertDontSee('name="perihal"', false);
    $response->assertDontSee('name="tanggal_konsultasi"', false);
    $response->assertDontSee('name="waktu_mulai"', false);
    $response->assertSee('name="status"', false);
    $response->assertSee('name="catatan"', false);
});

test('5. sekretariat edit konsultasi on Disetujui status renders Disetujui as selected option and no Hapus button', function () {
    $konsultasi = Konsultasi::factory()->create(['status' => 'Disetujui']);
    $user = User::factory()->sekretariat()->create();

    $response = $this->actingAs($user)->get(route('konsultasi.edit', $konsultasi));
    $response->assertSee('value="Disetujui" selected', false);

    $indexResponse = $this->actingAs($user)->get(route('konsultasi.index'));
    $indexResponse->assertDontSee('Hapus');
});

test('6. submitting pimpinan edit konsultasi form payload succeeds with 302', function () {
    $konsultasi = Konsultasi::factory()->create(['status' => 'Menunggu']);
    $user = User::factory()->pimpinan()->create();

    $response = $this->actingAs($user)->put(route('konsultasi.update', $konsultasi), [
        'status' => 'Disetujui',
        'catatan' => 'Persetujuan via form telaah pimpinan',
    ]);

    $response->assertRedirect(route('konsultasi.index'));
    $response->assertSessionHas('success');
    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('Disetujui');
});

test('7. pimpinan accessing /konsultasi/create receives 403 Forbidden page', function () {
    $user = User::factory()->pimpinan()->create();

    $response = $this->actingAs($user)->get(route('konsultasi.create'));

    $response->assertStatus(403);
    $response->assertSee('Akses Ditolak');
});

test('8. DemoSeeder seeds 3 valid role users idempotently and respects password complexity', function () {
    putenv('APP_DEMO_PASSWORD=DemoSecurePassword123!@#');
    $_ENV['APP_DEMO_PASSWORD'] = 'DemoSecurePassword123!@#';
    $_SERVER['APP_DEMO_PASSWORD'] = 'DemoSecurePassword123!@#';

    $seeder = new DemoSeeder;
    $seeder->run();

    $admin = User::where('email', 'admin@simonka.test')->first();
    $pimpinan = User::where('email', 'pimpinan@simonka.test')->first();
    $sekretariat = User::where('email', 'sekretariat@simonka.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe(Role::Admin)
        ->and($admin->status)->toBe(UserStatus::Active)
        ->and($pimpinan)->not->toBeNull()
        ->and($pimpinan->role)->toBe(Role::Pimpinan)
        ->and($pimpinan->status)->toBe(UserStatus::Active)
        ->and($sekretariat)->not->toBeNull()
        ->and($sekretariat->role)->toBe(Role::Sekretariat)
        ->and($sekretariat->status)->toBe(UserStatus::Active);

    // Idempotency check: running second time does not duplicate users or throw errors
    $seeder->run();

    expect(User::where('email', 'admin@simonka.test')->count())->toBe(1)
        ->and(User::where('email', 'pimpinan@simonka.test')->count())->toBe(1)
        ->and(User::where('email', 'sekretariat@simonka.test')->count())->toBe(1);
});
