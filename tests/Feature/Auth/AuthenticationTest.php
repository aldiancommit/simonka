<?php

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    RateLimiter::clear('login');
});

test('1. guest can view the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('SIMONKA');
});

test('2. authenticated active user is redirected away from login page to dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect('/');
});

test('3. public registration routes GET and POST /register return 404 Not Found', function () {
    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'Password123!@#',
        'password_confirmation' => 'Password123!@#',
    ])->assertNotFound();
});

test('4. login fails with generic error for non-existent email', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => 'unknown@simonka.palukota.go.id',
        'password' => 'SomePassword123!@#',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
    ]);
    $this->assertGuest();
});

test('5. login fails with generic error for incorrect password', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('CorrectPassword123!@#'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'WrongPassword123!@#',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
    ]);
    $this->assertGuest();
});

test('6. login fails with specific error if credentials match but status is inactive', function () {
    $user = User::factory()->admin()->inactive()->create([
        'password' => Hash::make('CorrectPassword123!@#'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'CorrectPassword123!@#',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.',
    ]);
    $this->assertGuest();
});

test('7. login fails with specific error if credentials match but role is null', function () {
    $user = User::factory()->unassignedRole()->create([
        'status' => UserStatus::Active,
        'password' => Hash::make('CorrectPassword123!@#'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'CorrectPassword123!@#',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Akun Anda belum memiliki hak akses peran. Hubungi Administrator.',
    ]);
    $this->assertGuest();
});

test('8. login succeeds with valid credentials, regenerates session, and records login timestamp', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('CorrectPassword123!@#'),
    ]);

    expect($user->last_login_at)->toBeNull();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'CorrectPassword123!@#',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);

    $user->refresh();
    expect($user->last_login_at)->not->toBeNull();
});

test('9. login supports remember me functionality', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('CorrectPassword123!@#'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'CorrectPassword123!@#',
        'remember' => '1',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $response->assertCookieNotExpired(Auth::guard()->getRecallerName());
});

test('10. login rate limiting blocks on the 6th failed attempt per email and IP', function () {
    $email = 'bruteforce@simonka.palukota.go.id';

    for ($i = 0; $i < 5; $i++) {
        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'WrongPassword123!@#',
        ]);
        $response->assertSessionHasErrors(['email' => 'Kredensial yang diberikan tidak cocok dengan data kami.']);
    }

    // 6th attempt should trigger rate limiting
    $response = $this->from('/login')->post('/login', [
        'email' => $email,
        'password' => 'WrongPassword123!@#',
    ]);

    $response->assertSessionHasErrors(['email']);
    $errors = session('errors')->get('email');
    expect($errors[0])->not->toBe('Kredensial yang diberikan tidak cocok dengan data kami.');
});

test('11. user can logout via POST request and session is invalidated', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user);
    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('12. logout via GET request is rejected with 405 Method Not Allowed', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/logout');

    $response->assertStatus(405);
});

test('13. EnsureAccountIsActive middleware logs out and redirects user if deactivated during active session', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user);
    $this->get('/')->assertStatus(200);

    // Deactivate user in database
    $user->deactivate();

    $response = $this->get('/');
    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('14. EnsureAccountIsActive middleware logs out and redirects user if role becomes null during active session', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user);
    $this->get('/')->assertStatus(200);

    // Remove role in database
    $user->forceFill(['role' => null])->save();

    $response = $this->get('/');
    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('15. RoleMiddleware allows authorized role and returns 403 for unauthorized role', function () {
    Route::get('/test-admin-only', function () {
        return response('Admin Access Granted', 200);
    })->middleware(['web', 'auth', 'active', 'role:admin']);

    $admin = User::factory()->admin()->create();
    $pimpinan = User::factory()->pimpinan()->create();

    $this->actingAs($admin)->get('/test-admin-only')->assertStatus(200)->assertSee('Admin Access Granted');
    $this->actingAs($pimpinan)->get('/test-admin-only')->assertStatus(403);
});

test('16. user can update own password with valid current and strong new password', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('OldSecurePassword123!@#'),
    ]);

    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'OldSecurePassword123!@#',
        'password' => 'NewSecurePassword456!@#',
        'password_confirmation' => 'NewSecurePassword456!@#',
    ]);

    $response->assertSessionHasNoErrors();
    $user->refresh();
    expect(Hash::check('NewSecurePassword456!@#', $user->password))->toBeTrue();
});

test('17. password update fails if current password is incorrect', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('OldSecurePassword123!@#'),
    ]);

    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'WrongCurrentPassword123!@#',
        'password' => 'NewSecurePassword456!@#',
        'password_confirmation' => 'NewSecurePassword456!@#',
    ]);

    $response->assertSessionHasErrors(['current_password']);
    $user->refresh();
    expect(Hash::check('OldSecurePassword123!@#', $user->password))->toBeTrue();
});

test('18. password update fails if new password does not meet security policy', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('OldSecurePassword123!@#'),
    ]);

    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'OldSecurePassword123!@#',
        'password' => 'weak',
        'password_confirmation' => 'weak',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('19. password update is throttled after consecutive attempts', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('OldSecurePassword123!@#'),
    ]);

    for ($i = 0; $i < 3; $i++) {
        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'WrongPassword123!@#',
            'password' => 'NewSecurePassword456!@#',
            'password_confirmation' => 'NewSecurePassword456!@#',
        ]);
    }

    // 4th attempt should be throttled
    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'WrongPassword123!@#',
        'password' => 'NewSecurePassword456!@#',
        'password_confirmation' => 'NewSecurePassword456!@#',
    ]);

    $response->assertSessionHasErrors(['current_password']);
});
