# SPESIFIKASI & PROMPT EKSEKUSI FASE 2: AUTENTIKASI, ROLE, DAN HAK AKSES

Dokumen ini merupakan panduan instruksi teknis (*master execution prompt*) untuk AI coding / software engineer dalam mengimplementasikan **Fase 2: Autentikasi Mandiri, Role-Based Access Control (RBAC), dan Manajemen Pengguna** pada sistem SIMONKA.

---

## 1. ATURAN MUTLAK & STANDAR KERJA AI EXECUTOR

1. **Mode Kerja Bertahap (Step-by-Step with Stops):**
   * Eksekusi dilakukan langkah demi langkah (Langkah 0 sampai Langkah 7).
   * Setelah setiap langkah selesai, jalankan verifikasi nyata, laporkan hasilnya, commit dengan pesan yang ditentukan, lalu **WAJIB BERHENTI** menunggu konfirmasi dari User sebelum melanjutkan ke langkah berikutnya.
2. **Pola Test-Driven Development (TDD):**
   * Pada langkah yang menambahkan logika baru atau pengamanan hak akses, tulis tes regresi terlebih dahulu, jalankan dan tunjukkan bahwa tes **GAGAL** karena alasan otorisasi/logika yang tepat (bukan error sintaks).
   * Lakukan implementasi minimal hingga tes menjadi **HIJAU (PASSED)**.
   * Buat commit terpisah antara penulisan tes gagal dan implementasi.
3. **Isolasi Database Pengujian (`simonka_test`):**
   * Seluruh eksekusi tes (`./vendor/bin/pest`) wajib berjalan pada database MySQL `simonka_test` yang dilindungi oleh pengaman `ensureSafeTestDatabase()` di `tests/TestCase.php`.
   * **DILARANG KERAS** menjalankan `php artisan migrate`, `php artisan db:seed`, atau tes yang menyentuh database utama `simonka_db`.
   * Perintah migrasi dan seeding untuk `simonka_db` dicatat di panduan untuk dieksekusi secara mandiri oleh User.
4. **Keamanan Kredensial & Environtment:**
   * Jangan pernah menampilkan isi plain password, APP_KEY, atau token rahasia pada output terminal, log, maupun dokumen laporan.
   * `AdminUserSeeder` membaca kredensial dari environment variable (`APP_ADMIN_NAME`, `APP_ADMIN_EMAIL`, `APP_ADMIN_PASSWORD`). **TIDAK ADA FALLBACK HARDCODE**. Jika variabel tersebut kosong, seeder **WAJIB GAGAL (throw RuntimeException)**.
   * **Catatan Risiko Keamanan Database:** Password database di `phpunit.xml` dan `.env.example` tidak boleh memuat kredensial riil produksi. Kredensial tes disarankan dipindahkan ke `.env.testing` (yang masuk `.gitignore`) sebagai tugas pemeliharaan terpisah.
5. **Standar Kode & Format Pint:**
   * Format seluruh file PHP yang diubah menggunakan `vendor/bin/pint --dirty` sebelum commit.
   * Jangan mengubah struktur visual FundFlow / styling UI di luar integrasi autentikasi dan penegakan otorisasi.

---

## 2. KEPUTUSAN ARSITEKTUR & ATURAN BISNIS FINAL

1. **Registrasi Publik DIMATIKAN:**
   * Sistem SIMONKA merupakan aplikasi internal pemerintahan tertutup.
   * Tidak ada rute, controller, maupun view registrasi publik (rute `GET /register` dan `POST /register` menghasilkan **404 Not Found**).
   * Seluruh akun pengguna dibuat dan dikelola secara eksklusif oleh Administrator melalui modul Manajemen Pengguna.
   * Status akun pengguna disederhanakan menjadi 2 status: **`active`** dan **`inactive`** (status pending ditiadakan dari Enum, migrasi, model, dan tes).
2. **Pimpinan Login Mandiri:**
   * Pimpinan memiliki akun mandiri dengan role `pimpinan` untuk masuk ke sistem dan menelaah/memutuskan permohonan.
   * Sistem memiliki 3 role tetap: `admin`, `pimpinan`, `sekretariat`.
3. **Pembatasan Ketat Pimpinan pada Update Konsultasi (Strict Key Whitelist):**
   * Form update konsultasi bagi Pimpinan hanya menerima key `status` dan `catatan` (selain `_token` dan `_method`).
   * Jika request memuat key lain (`nama_pemohon`, `tanggal_konsultasi`, `waktu_mulai`, dll), server **WAJIB LANGSUNG MENOLAK dengan HTTP 403 Forbidden** tanpa perlu memeriksa atau membandingkan isi nilai lamanya.
   * Di view `resources/views/konsultasi/edit.blade.php`, field data pemohon/waktu bagi Pimpinan dirender sebagai teks statis atau menggunakan atribut `disabled` (bukan readonly) agar browser **TIDAK** mengirimkan key tersebut dalam payload form, sehingga Pimpinan tidak akan terkena 403.
4. **Pembatasan Ketat Sekretariat pada Konsultasi Disetujui:**
   * Sekretariat **DILARANG** mengubah `tanggal_konsultasi`, `waktu_mulai`, atau `waktu_selesai` pada permohonan konsultasi yang sudah berstatus `Disetujui` (perubahan jadwal wajib melalui mekanisme resmi pengajuan Penjadwalan Ulang).
   * Upaya pengiriman perubahan field jadwal pada konsultasi berstatus `Disetujui` oleh Sekretariat **WAJIB DITOLAK dengan HTTP 403 Forbidden**.
   * Administrator tetap memiliki wewenang penuh untuk mengubah data jadwal secara langsung bila diperlukan darurat.
5. **Penetapan Status Default pada Store:**
   * Pada pembuatan Permohonan Konsultasi baru (`POST /konsultasi`) oleh Sekretariat maupun Admin, input `status` dari payload diabaikan dan server secara otomatis menetapkan status awal `'Menunggu'`.
   * Pada pengajuan Penjadwalan Ulang baru (`POST /penjadwalan-ulang`), status pengajuan otomatis dipaksa `'Menunggu'`.
6. **Standar Kode Respons HTTP Mutlak:**
   * **HTTP 403 Forbidden:** Untuk seluruh pelanggaran hak akses peran, manipulasi status terlarang, pengiriman field terlarang, upaya IDOR, dan pelanggaran aturan pengaman admin.
   * **HTTP 422 Unprocessable Content:** Khusus kegagalan validasi format data input (misal format email salah, format jam keliru, perihal kosong).
   * **HTTP 302 Found (Redirect):** Untuk mutasi sukses atau redirect guest ke `/login`.
   * **HTTP 404 Not Found:** Untuk akses ke rute registrasi (`/register`) yang dinonaktifkan.

---

## 3. MATRIKS HAK AKSES & ATURAN TRANSISI STATUS

### 3.1 Matriks Hak Akses Per Role x Endpoint

| Route / Resource | Method & URI | Administrator (`admin`) | Pimpinan (`pimpinan`) | Sekretariat (`sekretariat`) | Guest / Unauthenticated |
|---|---|:---:|:---:|:---:|:---:|
| **Dashboard** | `GET /` | 200 | 200 | 200 | 302 |
| **Arsip Riwayat** | `GET /riwayat` | 200 | 200 | 200 | 302 |
| **Cetak Laporan** | `GET /laporan` | 200 | 200 | 200 | 302 |
| **Kalender Agenda** | `GET /agenda/kalender` | 200 | 200 | 200 | 302 |
| **Konsultasi - Index & Show** | `GET /konsultasi`, `GET /konsultasi/{konsultasi}` | 200 | 200 | 200 | 302 |
| **Konsultasi - Form Create** | `GET /konsultasi/create` | 200 | 403 | 200 | 302 |
| **Konsultasi - Store** | `POST /konsultasi` | 302 (Status dipaksa `Menunggu`) | 403 | 302 (Status dipaksa `Menunggu`) | 302 |
| **Konsultasi - Form Edit** | `GET /konsultasi/{konsultasi}/edit` | 200 | 200 (Mode Telaah) | 200 (Mode Data) | 302 |
| **Konsultasi - Update (Data)** | `PUT /konsultasi/{konsultasi}` | 302 | 403 | 302 | 302 |
| **Konsultasi - Update (Putusan)**| `PUT /konsultasi/{konsultasi}` | 302 | 302 | 403 | 302 |
| **Konsultasi - Hapus** | `DELETE /konsultasi/{konsultasi}` | 302 | 403 | 403 | 302 |
| **Jadwal - Index & Show** | `GET /agenda/jadwal`, `GET /agenda/jadwal/{jadwal}` | 200 | 200 | 200 | 302 |
| **Jadwal - Form Create & Edit**| `GET /agenda/jadwal/create`, `GET /agenda/jadwal/{jadwal}/edit` | 200 | 403 | 200 | 302 |
| **Jadwal - Store & Update** | `POST /agenda/jadwal`, `PUT /agenda/jadwal/{jadwal}` | 302 | 403 | 302 | 302 |
| **Jadwal - Hapus** | `DELETE /agenda/jadwal/{jadwal}` | 302 | 403 | 403 | 302 |
| **Reschedule - Index & Show** | `GET /penjadwalan-ulang`, `GET /penjadwalan-ulang/{penjadwalan_ulang}` | 200 | 200 | 200 | 302 |
| **Reschedule - Form Create** | `GET /penjadwalan-ulang/create` | 200 | 403 | 200 | 302 |
| **Reschedule - Store** | `POST /penjadwalan-ulang` | 302 (Status dipaksa `Menunggu`) | 403 | 302 (Status dipaksa `Menunggu`) | 302 |
| **Reschedule - Form Review** | `GET /penjadwalan-ulang/{penjadwalan_ulang}/edit` | 200 | 200 | 403 | 302 |
| **Reschedule - Update Putusan**| `PUT /penjadwalan-ulang/{penjadwalan_ulang}` | 302 | 302 | 403 | 302 |
| **Reschedule - Hapus** | `DELETE /penjadwalan-ulang/{penjadwalan_ulang}` | 302 | 403 | 403 | 302 |
| **Manajemen Pengguna (Semua)** | `/users/*` (Index, Create, Store, Edit, Update, Toggle, Reset, Destroy) | 200 / 302 | 403 | 403 | 302 |
| **Registrasi Publik** | `GET /register`, `POST /register` | 404 | 404 | 404 | 404 |

---

### 3.2 Tabel Aturan Transisi Status per Role

#### A. Permohonan Konsultasi (`Konsultasi`)
| Role | Status Awal | Status Baru yang Diizinkan | Field yang Boleh Dikirim | Status Response Jika Melanggar |
|---|---|---|---|:---:|
| **`admin`** | Baru (`store`) | Otomatis dipaksa `'Menunggu'` | Semua field | - |
| **`admin`** | Mana saja | `Menunggu`, `Disetujui`, `Ditolak`, `Selesai`, `Dibatalkan` | Semua field | - |
| **`pimpinan`** | `Menunggu`, `Disetujui` | `Disetujui`, `Ditolak`, `Selesai`, `Dibatalkan` | **HANYA** `status` dan `catatan` | **403** (jika kirim field lain) |
| **`sekretariat`** | Baru (`store`) | Otomatis dipaksa `'Menunggu'` | Semua field data | - |
| **`sekretariat`** | `Menunggu` | `Menunggu`, `Selesai`, `Dibatalkan` | Semua field data | **403** (jika kirim `Disetujui`/`Ditolak`) |
| **`sekretariat`** | `Disetujui` | `Disetujui`, `Selesai`, `Dibatalkan` | **HANYA** `nama_pemohon`, `instansi`, `no_telepon`, `email`, `perihal`, `catatan` | **403** (jika ubah `tanggal_konsultasi`/`waktu_mulai`/`waktu_selesai`) |

#### B. Penjadwalan Ulang (`PenjadwalanUlang`)
| Role | Status Awal | Status Baru yang Diizinkan | Status Response Jika Melanggar |
|---|---|---|:---:|
| **`admin`** | Baru (`store`) | Otomatis dipaksa `'Menunggu'` | - |
| **`admin`** | Mana saja | `Menunggu`, `Disetujui`, `Ditolak` | - |
| **`pimpinan`** | `Menunggu` | `Disetujui`, `Ditolak` | - |
| **`sekretariat`** | Baru (`store`) | Otomatis dipaksa `'Menunggu'` | - |
| **`sekretariat`** | Update (`PUT`) | Dilarang update putusan reschedule | **403** |

#### C. Jadwal Kegiatan Resmi (`JadwalKegiatan`)
| Role | Hak Akses | Status Response Jika Melanggar |
|---|---|:---:|
| **`admin`** | Penuh (Create, Read, Update, Delete) | - |
| **`sekretariat`** | Create, Read, Update | Delete $\rightarrow$ **403** |
| **`pimpinan`** | Read-only (Index, Show) | Create/Update/Delete $\rightarrow$ **403** |

---

## 4. SPESIFIKASI KEAMANAN MODEL & AUTH

### 4.1 Model `User` & Proteksi Mass-Assignment
* **Kolom Tabel `users`:** `id`, `name`, `email`, `password`, `role` (`VARCHAR(50)`, `nullable`), `status` (`VARCHAR(50)`, default `'active'`), `last_login_at` (`TIMESTAMP`, `nullable`), `remember_token`, `created_at`, `updated_at`.
* **Proteksi Mass-Assignment Mutlak:** Atribut `#[Fillable(['name', 'email', 'password'])]`. Kolom `role`, `status`, dan `last_login_at` **TIDAK BOLEH** ada di `$fillable`.
* **Kode Lengkap Model `User`:**
  ```php
  <?php

  namespace App\Models;

  use App\Enums\Role;
  use App\Enums\UserStatus;
  use Database\Factories\UserFactory;
  use Illuminate\Database\Eloquent\Attributes\Fillable;
  use Illuminate\Database\Eloquent\Attributes\Hidden;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Foundation\Auth\User as Authenticatable;
  use Illuminate\Notifications\Notifiable;

  #[Fillable(['name', 'email', 'password'])]
  #[Hidden(['password', 'remember_token'])]
  class User extends Authenticatable
  {
      /** @use HasFactory<UserFactory> */
      use HasFactory, Notifiable;

      protected function casts(): array
      {
          return [
              'email_verified_at' => 'datetime',
              'password' => 'hashed',
              'role' => Role::class,
              'status' => UserStatus::class,
              'last_login_at' => 'datetime',
          ];
      }

      public function hasRole(Role|string|array $roles): bool
      {
          if (empty($this->role)) {
              return false;
          }

          $roleValue = is_string($this->role) ? $this->role : $this->role->value;
          $rolesArray = is_array($roles) ? $roles : [$roles];

          foreach ($rolesArray as $r) {
              $compareValue = $r instanceof Role ? $r->value : (string) $r;
              if ($roleValue === $compareValue) {
                  return true;
              }
          }

          return false;
      }

      public function isActive(): bool
      {
          return $this->status === UserStatus::Active && $this->role !== null;
      }

      public function assignRole(Role $role): void
      {
          $this->forceFill(['role' => $role])->save();
      }

      public function activate(): void
      {
          $this->forceFill(['status' => UserStatus::Active])->save();
      }

      public function deactivate(): void
      {
          $this->forceFill(['status' => UserStatus::Inactive])->save();
      }

      public function recordLogin(): void
      {
          $this->forceFill(['last_login_at' => now()])->save();
      }
  }
  ```

### 4.2 Alur Keamanan Login, Throttle, & Session
1. **Rate Limiting (Throttle):**
   * Pada `POST /login`: Maksimal 5 kali percobaan gagal per 1 menit per kombinasi `email + IP` via `RateLimiter`.
   * Pada perubahan kata sandi sendiri (`PUT /profile/password`): Diberikan rate limiting throttle (misal maksimal 3 kali per menit) untuk mencegah brute force.
2. **Pesan Error Anti-Enumerasi:**
   * Jika email tidak ditemukan ATAU password salah: Kembalikan pesan generik `"Kredensial yang diberikan tidak cocok dengan data kami."`.
   * Pengecekan status akun dilakukan **HANYA SETELAH KREDENSIAL TERBUKTI BENAR**:
     - Jika `status === UserStatus::Inactive`: Segera logout dan kembalikan error `"Akun Anda telah dinonaktifkan. Silakan hubungi Administrator."`.
     - Jika `role === null`: Segera logout dan kembalikan error `"Akun Anda belum memiliki hak akses peran. Hubungi Administrator."`.
3. **Session Hardening:**
   * Saat login sukses: `$request->session()->regenerate()` dan `$user->recordLogin()`.
   * Saat logout: `Auth::logout()`, `$request->session()->invalidate()`, `$request->session()->regenerateToken()`.
   * Logout wajib berupa request `POST /logout` yang dilindungi token CSRF.
4. **Kebijakan Kata Sandi:**
   * Password minimal 10 karakter, wajib memuat huruf besar, huruf kecil, angka, dan simbol: `Password::min(10)->letters()->mixedCase()->numbers()->symbols()`.

### 4.3 Aturan Pengaman Mutlak Administrator (Hard Safety Guards)
1. **Larangan Self-Demote & Self-Deactivate:** Admin yang sedang login ditolak (HTTP 403) jika mencoba menonaktifkan statusnya sendiri atau menurunkan rolenya sendiri menjadi non-admin.
2. **Proteksi Admin Terakhir:** Sistem menolak (HTTP 403) penghapusan, penonaktifan, atau penurunan role admin jika user tersebut merupakan satu-satunya Admin aktif yang tersisa di database (`User::where('role', Role::Admin)->where('status', UserStatus::Active)->count() <= 1`).

---

## 5. ESTIMASI EFFORT REALISTIS

| Tahapan Kerja | Cakupan Modul | Estimasi Waktu |
|---|---|:---:|
| **Langkah 0** | Pra-cek, branching `fix/phase-2-auth`, verifikasi 42 tes baseline hijau | 30 menit |
| **Langkah 1** | Enum Role & UserStatus (Active/Inactive), Migrasi users, Model User, Seeder Admin | 1 jam |
| **Langkah 2** | Auth LoginController, Throttle login & password, POST Logout, Middleware Active & Role | 1.5 jam |
| **Langkah 3** | Policies nyata (4 model), FormRequest status & field guards, AuthorizesRequests | 2.5 jam |
| **Langkah 4** | UI Auth FundFlow, Profile dropdown, Navigasi `@can`, Error 403 view, Mode telaah Pimpinan | 1.5 jam |
| **Langkah 5** | Modul Manajemen Pengguna Admin (CRUD, Reset Sandi, Safety Guards) | 2 jam |
| **Langkah 6** | Pembaruan 42 tes lama + Test Matrix RBAC lengkap, IDOR, & session drop | 2 jam |
| **Langkah 7** | Dokumentasi `CHANGELOG-phase2.md` & verifikasi akhir | 1 jam |
| **TOTAL ESTIMASI** | | **8 – 12 Jam** |

---

## 6. PANDUAN EKSEKUSI LANGKAH DEMI LANGKAH (STEP-BY-STEP)

---

### LANGKAH 0 — PRA-CEK & PERSIAPAN BRANCH

#### A. Tujuan
Memastikan working tree bersih, berada pada commit terbaru dari `main`, dan seluruh 42 tes bawaan berjalan 100% hijau pada `simonka_test`.

#### B. File yang Boleh Diubah
* Tidak ada file yang diubah pada langkah ini.

#### C. Perintah Eksekusi
1. `git checkout main && git pull`
2. `git checkout -b fix/phase-2-auth`
3. `./vendor/bin/pest`

#### D. Hasil yang Diharapkan
```text
Tests:    42 passed (109 assertions)
Duration: ~1.0s
```

#### E. Titik Berhenti
* Laporkan hasil eksekusi tes dan output `git status`.
* **BERHENTI** dan tunggu konfirmasi User sebelum Langkah 1.

---

### LANGKAH 1 — ENUM, SKEMA DATABASE, MODEL USER, & SEEDER ADMIN

#### A. Tujuan
Membuat Enum `Role` dan `UserStatus` (`Active = 'active'`, `Inactive = 'inactive'`), migrasi kolom baru tabel `users`, implementasi model `User` dengan proteksi mass-assignment, dan seeder admin tanpa hardcode.

#### B. File yang Dibuat / Diubah
1. `app/Enums/Role.php` (Baru)
2. `app/Enums/UserStatus.php` (Baru)
3. `database/migrations/2026_10_08_160000_add_role_and_status_to_users_table.php` (Baru)
4. `app/Models/User.php` (Modifikasi)
5. `database/factories/UserFactory.php` (Modifikasi)
6. `database/seeders/AdminUserSeeder.php` (Baru)
7. `database/seeders/DatabaseSeeder.php` (Modifikasi)

#### C. Spesifikasi Implementasi
1. **`app/Enums/Role.php`:**
   ```php
   namespace App\Enums;

   enum Role: string
   {
       case Admin = 'admin';
       case Pimpinan = 'pimpinan';
       case Sekretariat = 'sekretariat';

       public function label(): string
       {
           return match ($this) {
               self::Admin => 'Administrator',
               self::Pimpinan => 'Pimpinan',
               self::Sekretariat => 'Sekretariat',
           };
       }
   }
   ```
2. **`app/Enums/UserStatus.php`:**
   ```php
   namespace App\Enums;

   enum UserStatus: string
   {
       case Active = 'active';
       case Inactive = 'inactive';

       public function label(): string
       {
           return match ($this) {
               self::Active => 'Aktif',
               self::Inactive => 'Nonaktif',
           };
       }
   }
   ```
3. **Migrasi Baru (`2026_10_08_160000_add_role_and_status_to_users_table.php`):**
   * Tambahkan kolom:
     - `$table->string('role', 50)->nullable()->after('email');`
     - `$table->string('status', 50)->default('active')->after('role');`
     - `$table->timestamp('last_login_at')->nullable()->after('remember_token');`
     - `$table->index(['role', 'status']);`
4. **`database/seeders/AdminUserSeeder.php`:**
   ```php
   namespace Database\Seeders;

   use App\Enums\Role;
   use App\Enums\UserStatus;
   use App\Models\User;
   use Illuminate\Database\Seeder;
   use Illuminate\Support\Facades\Hash;
   use RuntimeException;

   class AdminUserSeeder extends Seeder
   {
       public function run(): void
       {
           $email = env('APP_ADMIN_EMAIL');
           $password = env('APP_ADMIN_PASSWORD');
           $name = env('APP_ADMIN_NAME', 'Administrator SIMONKA');

           if (empty($email) || empty($password)) {
               throw new RuntimeException('Variabel APP_ADMIN_EMAIL dan APP_ADMIN_PASSWORD wajib dikonfigurasi di file environment sebelum menjalankan seeder.');
           }

           if (strlen($password) < 10) {
               throw new RuntimeException('APP_ADMIN_PASSWORD harus memenuhi kebijakan keamanan minimal 10 karakter.');
           }

           $admin = User::updateOrCreate(
               ['email' => $email],
               [
                   'name' => $name,
                   'password' => Hash::make($password),
                   'email_verified_at' => now(),
               ]
           );

           $admin->assignRole(Role::Admin);
           $admin->activate();
       }
   }
   ```

#### D. Verifikasi
* Jalankan `./vendor/bin/pest` untuk memastikan skema migrasi dieksekusi bersih pada `simonka_test`.
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `feat(auth): add role and status enums, users migration, and admin seeder`.

#### E. Titik Berhenti
* Laporkan hasil eksekusi tes dan commit git. **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 2 — AUTH LOGIN CONTROLLER, THROTTLE, POST LOGOUT, & MIDDLEWARE

#### A. Tujuan
Mengimplementasikan login dengan rate-limiting (throttle), validasi kredensial anti-enumerasi, validasi status aktif & role bukan null, logout native via POST, throttle pada perubahan kata sandi, serta middleware `EnsureAccountIsActive` dan `RoleMiddleware`.

#### B. File yang Dibuat / Diubah
1. `app/Http/Controllers/Auth/LoginController.php` (Baru)
2. `app/Http/Middleware/EnsureAccountIsActive.php` (Baru)
3. `app/Http/Middleware/RoleMiddleware.php` (Baru)
4. `bootstrap/app.php` (Modifikasi - registrasi alias middleware)
5. `routes/web.php` (Modifikasi - rute login dan POST logout)
6. `tests/Feature/Auth/AuthenticationTest.php` (Baru)

#### C. Siklus TDD & Daftar Lengkap Tes di `AuthenticationTest.php`
1. **Tulis Tes di `tests/Feature/Auth/AuthenticationTest.php`:**
   - Guest dapat melihat halaman login (`GET /login`).
   - Rute registrasi publik `GET /register` dan `POST /register` mengembalikan **404 Not Found**.
   - Login gagal dengan kredensial salah menghasilkan pesan generik anti-enumerasi: `"Kredensial yang diberikan tidak cocok dengan data kami."`.
   - Login dengan kredensial benar tetapi `status === 'inactive'` ditolak dengan pesan dinonaktifkan: `"Akun Anda telah dinonaktifkan. Silakan hubungi Administrator."`.
   - Login dengan kredensial benar tetapi `role === null` ditolak dengan pesan: `"Akun Anda belum memiliki hak akses peran. Hubungi Administrator."`.
   - Login sukses meregenerate session dan mengupdate `last_login_at`.
   - Rate limiting (throttle) memblokir percobaan login ke-6 berturut-turut dengan status 429 Too Many Requests / pesan error throttle.
   - Logout via `POST /logout` menghapus sesi autentikasi dan meregenerate token CSRF.
   - Logout via `GET /logout` ditolak (HTTP 405 Method Not Allowed).
2. Jalankan `./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php` dan buktikan seluruh tes **GAGAL**.
3. Implementasikan controller dan middleware.
4. **`EnsureAccountIsActive.php`:**
   ```php
   namespace App\Http\Middleware;

   use App\Enums\UserStatus;
   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Symfony\Component\HttpFoundation\Response;

   class EnsureAccountIsActive
   {
       public function handle(Request $request, Closure $next): Response
       {
           $user = $request->user();

           if ($user && ($user->status !== UserStatus::Active || $user->role === null)) {
               Auth::logout();
               $request->session()->invalidate();
               $request->session()->regenerateToken();

               return redirect()->route('login')->withErrors([
                   'email' => 'Sesi Anda telah berakhir karena akun tidak aktif atau belum memiliki hak akses peran.',
               ]);
           }

           return $next($request);
       }
   }
   ```
5. Jalankan tes kembali hingga **HIJAU**.

#### D. Verifikasi
* Jalankan: `./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php`
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `feat(auth): implement login, throttle, logout, active account guard, and role middleware`.

#### E. Titik Berhenti
* Tampilkan hasil pest test. **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 3 — RESOURCE POLICIES, STATUS GATES, & PROTEKSI FORUM REQUEST

#### A. Tujuan
Mengimplementasikan policy otorisasi nyata untuk 4 model (`KonsultasiPolicy`, `JadwalKegiatanPolicy`, `PenjadwalanUlangPolicy`, `UserPolicy`), proteksi mutasi field & status di `FormRequest` tingkat server, serta otorisasi eksplisit di controller via `AuthorizesRequests`.

#### B. File yang Dibuat / Diubah
1. `app/Http/Controllers/Controller.php` (Modifikasi - gunakan trait `AuthorizesRequests`)
2. `app/Policies/KonsultasiPolicy.php` (Modifikasi)
3. `app/Policies/JadwalKegiatanPolicy.php` (Modifikasi)
4. `app/Policies/PenjadwalanUlangPolicy.php` (Modifikasi)
5. `app/Http/Requests/StoreKonsultasiRequest.php` (Modifikasi - paksa status `Menunggu` di server)
6. `app/Http/Requests/UpdateKonsultasiRequest.php` (Modifikasi - strict key whitelist untuk pimpinan & tolak mutasi jadwal konsultasi disetujui untuk sekretariat)
7. `app/Http/Requests/StoreJadwalKegiatanRequest.php` (Modifikasi)
8. `app/Http/Requests/UpdateJadwalKegiatanRequest.php` (Modifikasi)
9. `app/Http/Requests/StorePenjadwalanUlangRequest.php` (Modifikasi - paksa status `Menunggu` di server)
10. `app/Http/Requests/UpdatePenjadwalanUlangRequest.php` (Modifikasi - tolak update putusan oleh sekretariat)
11. `app/Http/Controllers/KonsultasiController.php` (Modifikasi - panggil `$this->authorize(...)`)
12. `app/Http/Controllers/JadwalKegiatanController.php` (Modifikasi - panggil `$this->authorize(...)`)
13. `app/Http/Controllers/PenjadwalanUlangController.php` (Modifikasi - panggil `$this->authorize(...)`)
14. `tests/Feature/Auth/StatusTamperingTest.php` (Baru)

#### C. Spesifikasi Penegakan Server-Side di FormRequest
1. **`UpdateKonsultasiRequest.php`:**
   ```php
   public function authorize(): bool
   {
       $user = $this->user();
       $konsultasi = $this->route('konsultasi');

       if (!$user || !$konsultasi) {
           return false;
       }

       // Pimpinan: Strict Key Whitelist (HANYA status dan catatan)
       if ($user->hasRole(\App\Enums\Role::Pimpinan)) {
           $allowedKeys = ['_token', '_method', 'status', 'catatan'];
           $submittedKeys = array_keys($this->all());
           $forbiddenKeys = array_diff($submittedKeys, $allowedKeys);

           if (!empty($forbiddenKeys)) {
               return false; // Mengembalikan HTTP 403 Forbidden
           }
       }

       // Sekretariat: Dilarang mengirim status Disetujui/Ditolak
       if ($user->hasRole(\App\Enums\Role::Sekretariat)) {
           if (in_array($this->input('status'), ['Disetujui', 'Ditolak'])) {
               return false; // Mengembalikan HTTP 403 Forbidden
           }

           // Sekretariat: Dilarang mengubah jadwal jika status konsultasi saat ini sudah Disetujui
           if ($konsultasi->status === 'Disetujui') {
               $scheduleKeys = ['tanggal_konsultasi', 'waktu_mulai', 'waktu_selesai'];
               foreach ($scheduleKeys as $key) {
                   if ($this->has($key) && $this->input($key) != $konsultasi->{$key}) {
                       return false; // Mengembalikan HTTP 403 Forbidden
                   }
               }
           }
       }

       return $user->can('update', $konsultasi);
   }
   ```
2. **`UpdatePenjadwalanUlangRequest.php`:**
   * Di method `authorize()`: Kembalikan `false` (HTTP 403 Forbidden) jika role user adalah `Sekretariat`.
3. **Pencocokan Parameter Route Model Binding:**
   * `{konsultasi}` $\rightarrow$ `KonsultasiPolicy@update(User $user, Konsultasi $konsultasi)`
   * `{jadwal}` $\rightarrow$ `JadwalKegiatanPolicy@update(User $user, JadwalKegiatan $jadwal)`
   * `{penjadwalan_ulang}` $\rightarrow$ `PenjadwalanUlangPolicy@update(User $user, PenjadwalanUlang $penjadwalanUlang)`

#### D. Siklus TDD & Verifikasi
* Tulis tes di `tests/Feature/Auth/StatusTamperingTest.php`:
  - Store konsultasi oleh Sekretariat atau Admin yang menyertakan input `status = Disetujui` tetap dipaksa tersimpan sebagai `Menunggu` di database.
  - Store reschedule yang menyertakan input `status = Disetujui` tetap dipaksa tersimpan sebagai `Menunggu` di database.
  - Sekretariat mencoba mengirim `status = Disetujui` pada konsultasi update $\rightarrow$ **403**.
  - Sekretariat mencoba mengubah tanggal konsultasi yang sudah `Disetujui` $\rightarrow$ **403**.
  - Pimpinan mencoba mengirim `nama_pemohon` pada update konsultasi $\rightarrow$ **403**.
  - Pimpinan berhasil memperbarui `status` dan `catatan` pada konsultasi $\rightarrow$ **302**.
  - Sekretariat mencoba update putusan reschedule $\rightarrow$ **403**.
* Jalankan: `./vendor/bin/pest tests/Feature/Auth/StatusTamperingTest.php`
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `feat(auth): enforce resource policies and strict request authorization rules`.

#### E. Titik Berhenti
* Tampilkan hasil pest test. **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 4 — UI AUTH, PROFILE DROPDOWN, & KONDISIONAL VIEW (@can)

#### A. Tujuan
Membangun antarmuka Login dan Halaman Error 403 bergaya FundFlow Glassmorphism, menghubungkan nama dan badge role pada header, mengganti tombol dummy logout dengan form native POST, serta menyembunyikan aksi di view menggunakan `@can`.

#### B. File yang Dibuat / Diubah
1. `resources/views/layouts/auth.blade.php` (Baru)
2. `resources/views/auth/login.blade.php` (Baru)
3. `resources/views/errors/403.blade.php` (Baru)
4. `resources/views/partials/header.blade.php` (Modifikasi - nama user, badge role, form POST logout)
5. `resources/views/partials/navigation.blade.php` (Modifikasi - menu users terproteksi `@can`)
6. `resources/views/konsultasi/index.blade.php` (Modifikasi - sembunyikan Tambah/Hapus sesuai `@can`)
7. `resources/views/konsultasi/edit.blade.php` (Modifikasi - mode telaah pimpinan dengan field disabled & filter opsi status)
8. `resources/views/agenda/jadwal/index.blade.php` (Modifikasi - sembunyikan Tambah/Hapus sesuai `@can`)
9. `resources/views/penjadwalan-ulang/index.blade.php` (Modifikasi - sembunyikan Ajukan/Hapus sesuai `@can`)

#### C. Spesifikasi Tampilan
1. **`resources/views/partials/header.blade.php`:**
   - Nama: `{{ auth()->user()->name }}`
   - Role badge: `<span class="badge bg-soft-primary small">{{ auth()->user()->role?->label() }}</span>`
   - Form Logout POST:
     ```html
     <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
         @csrf
         <button type="submit" class="dropdown-item rounded-2 py-2 px-3 fw-semibold text-danger d-flex align-items-center gap-2.5 w-100 border-0 bg-transparent">
             <i class="fas fa-sign-out-alt text-danger fa-fw"></i>
             <span>Logout</span>
         </button>
     </form>
     ```
2. **`resources/views/konsultasi/edit.blade.php`:**
   - Jika role `pimpinan`: Tampilkan banner *"Mode Telaah Pimpinan"*, render field data pemohon/waktu dengan atribut `disabled` (sehingga browser tidak mengirim key tersebut ke server), dan jadikan area `status` serta `catatan` sebagai input aktif.
   - Jika role `sekretariat`: Dropdown status hanya merender opsi `Menunggu`, `Selesai`, `Dibatalkan`.

#### D. Verifikasi
* Verifikasi Blade view rendering.
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `feat(auth): implement auth layout, user header profile, and role-conditioned views`.

#### E. Titik Berhenti
* Laporkan hasil perubahan view. **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 5 — MODUL MANAJEMEN PENGGUNA (ADMIN USER MANAGEMENT)

#### A. Tujuan
Membangun modul CRUD pengguna khusus Administrator, termasuk fitur pembuatan akun, penetapan role, penonaktifan/pengaktifan akun, reset sandi oleh admin, serta penegakan aturan anti-self-demote dan proteksi admin terakhir.

#### B. File yang Dibuat / Diubah
1. `app/Http/Controllers/UserController.php` (Baru)
2. `app/Http/Requests/StoreUserRequest.php` (Baru)
3. `app/Http/Requests/UpdateUserRequest.php` (Baru)
4. `app/Policies/UserPolicy.php` (Baru)
5. `resources/views/users/index.blade.php` (Baru)
6. `resources/views/users/create.blade.php` (Baru)
7. `resources/views/users/edit.blade.php` (Baru)
8. `routes/web.php` (Modifikasi - resource `users` dengan middleware `role:admin`)
9. `tests/Feature/Auth/UserManagementTest.php` (Baru)

#### C. Siklus TDD & Daftar Lengkap Tes di `UserManagementTest.php`
1. **Tulis Tes di `tests/Feature/Auth/UserManagementTest.php`:**
   - Non-admin (Pimpinan/Sekretariat/Guest) yang mengakses `/users` atau aksi pengguna manapun mendapat **403 Forbidden** (atau **302** ke login untuk guest).
   - Admin dapat membuat user baru dengan role dan status aktif langsung.
   - Admin dapat memperbarui nama, email, dan role user lain.
   - Admin dapat mengaktifkan dan menonaktifkan akun user lain.
   - Admin dapat mereset sandi user lain ke sandi baru yang valid.
   - Admin **DITOLAK (403 Forbidden)** saat mencoba menonaktifkan akunnya sendiri.
   - Admin **DITOLAK (403 Forbidden)** saat mencoba menurunkan rolenya sendiri menjadi non-admin.
   - Admin **DITOLAK (403 Forbidden)** saat mencoba menghapus satu-satunya admin aktif yang tersisa.
   - Admin **DITOLAK (403 Forbidden)** saat mencoba menonaktifkan satu-satunya admin aktif yang tersisa.
   - Admin **DITOLAK (403 Forbidden)** saat mencoba menurunkan role satu-satunya admin aktif yang tersisa.
2. Implementasikan controller, request validation, dan policy.
3. Jalankan tes hingga **HIJAU**.

#### D. Verifikasi
* Jalankan: `./vendor/bin/pest tests/Feature/Auth/UserManagementTest.php`
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `feat(auth): implement user management with last-admin and self-demote safety guards`.

#### E. Titik Berhenti
* Tampilkan hasil pest test. **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 6 — PEMBARUAN TEST SUITE LAMA & TEST MATRIX RBAC LENGKAP

#### A. Tujuan
Menambahkan helper autentikasi di `tests/TestCase.php`, memperbarui seluruh 42 tes lama agar lolos autentikasi dengan role yang tepat, serta menambahkan pengujian matriks RBAC komprehensif, uji IDOR, dan uji pemutusan sesi aktif nonaktif.

#### B. File yang Dibuat / Diubah
1. `tests/TestCase.php` (Modifikasi - penambahan method `actingAsRole`)
2. `tests/Feature/NavigationRoutesTest.php` (Modifikasi - login as admin)
3. `tests/Feature/KonsultasiCrudTest.php` (Modifikasi - login as admin/sekretariat)
4. `tests/Feature/JadwalKegiatanCrudTest.php` (Modifikasi - login as sekretariat)
5. `tests/Feature/PenjadwalanUlangCrudTest.php` (Modifikasi - login as admin)
6. `tests/Feature/PenjadwalanUlangRegressionTest.php` (Modifikasi - login as admin/pimpinan)
7. `tests/Feature/TimezoneValidationTest.php` (Modifikasi - login as sekretariat)
8. `tests/Feature/ExampleTest.php` (Modifikasi - adaptasi login)
9. `tests/Feature/Auth/AuthorizationMatrixTest.php` (Baru)

#### C. Spesifikasi Helper di `tests/TestCase.php`
```php
public function actingAsRole(\App\Enums\Role|string $role = \App\Enums\Role::Admin, array $attributes = []): \App\Models\User
{
    $roleEnum = is_string($role) ? \App\Enums\Role::from($role) : $role;

    $user = \App\Models\User::factory()->create(array_merge([
        'role' => $roleEnum,
        'status' => \App\Enums\UserStatus::Active,
        'email_verified_at' => now(),
    ], $attributes));

    $this->actingAs($user);

    return $user;
}
```

#### D. Cakupan Pengujian Matriks di `AuthorizationMatrixTest.php`
* Uji seluruh sel matriks hak akses per role terhadap semua rute.
* **Uji IDOR:** Mengakses atau menghapus ID record lain oleh role yang tidak berhak (misal Pimpinan memanggil `DELETE /konsultasi/{id}` atau Sekretariat memanggil `DELETE /agenda/jadwal/{id}` atau `PUT /penjadwalan-ulang/{id}`) dipastikan tetap menghasilkan **403 Forbidden**.
* **Uji Sesi Nonaktif:** User yang sedang login, kemudian di database statusnya diubah menjadi `inactive` atau `role`-nya dihapus, pada request HTTP berikutnya langsung ditolak dan dialihkan ke login oleh `EnsureAccountIsActive`.

#### E. Verifikasi Test Suite Lengkap
* Jalankan seluruh suite tes: `./vendor/bin/pest`
* **Kriteria Keberhasilan:** Seluruh tes wajib **PASSED (100% HIJAU)**. Seluruh sel matriks dan skenario keamanan tercakup tanpa ada kegagalan.
* Format kode: `vendor/bin/pint --dirty`.
* Commit: `test(auth): update legacy tests and add complete RBAC authorization matrix test`.

#### F. Titik Berhenti
* Tampilkan ringkasan lengkap Pest (total tests passed, assertions, duration). **BERHENTI** menunggu konfirmasi User.

---

### LANGKAH 7 — DOKUMENTASI & PANDUAN EKSEKUSI PRODUKSI

#### A. Tujuan
Menyusun changelog komprehensif pada `docs/CHANGELOG-phase2.md` dan mendokumentasikan panduan migrasi basis data riil.

#### B. File yang Dibuat
1. `docs/CHANGELOG-phase2.md` (Baru)

#### C. Isi Dokumentasi
* Ringkasan arsitektur autentikasi mandiri & RBAC 3 role.
* Matriks hak akses dan tabel transisi status.
* Daftar seluruh file yang dibuat dan diubah.
* Hasil pengujian sebelum (42 tes) vs sesudah (seluruh matriks tercakup).
* **Panduan Eksekusi Produksi untuk Database Utama (`simonka_db`):**
  ```bash
  # 1. Pastikan APP_ADMIN_EMAIL, APP_ADMIN_PASSWORD, APP_ADMIN_NAME terisi di .env
  # 2. Jalankan migrasi kolom tabel users
  php artisan migrate

  # 3. Jalankan seeder admin pertama
  php artisan db:seed --class=AdminUserSeeder
  ```

#### D. Verifikasi
* Commit: `docs: complete Phase 2 Authentication and RBAC documentation`.
* Tampilkan `git log --oneline -10` dan `git status`.

---

## 7. DAFTAR LENGKAP PATH FILE YANG TERLIBAT

```text
app/
├── Enums/
│   ├── Role.php
│   └── UserStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── Controller.php
│   │   ├── JadwalKegiatanController.php
│   │   ├── KonsultasiController.php
│   │   ├── PenjadwalanUlangController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   ├── EnsureAccountIsActive.php
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── StoreJadwalKegiatanRequest.php
│       ├── StoreKonsultasiRequest.php
│       ├── StorePenjadwalanUlangRequest.php
│       ├── StoreUserRequest.php
│       ├── UpdateJadwalKegiatanRequest.php
│       ├── UpdateKonsultasiRequest.php
│       ├── UpdatePenjadwalanUlangRequest.php
│       └── UpdateUserRequest.php
├── Models/
│   └── User.php
└── Policies/
    ├── JadwalKegiatanPolicy.php
    ├── KonsultasiPolicy.php
    ├── PenjadwalanUlangPolicy.php
    └── UserPolicy.php

database/
├── factories/
│   └── UserFactory.php
├── migrations/
│   └── 2026_10_08_160000_add_role_and_status_to_users_table.php
└── seeders/
    ├── AdminUserSeeder.php
    └── DatabaseSeeder.php

resources/views/
├── auth/
│   └── login.blade.php
├── errors/
│   └── 403.blade.php
├── layouts/
│   └── auth.blade.php
├── partials/
│   ├── header.blade.php
│   └── navigation.blade.php
└── users/
    ├── create.blade.php
    ├── edit.blade.php
    └── index.blade.php

tests/
├── TestCase.php
└── Feature/
    ├── Auth/
    │   ├── AuthenticationTest.php
    │   ├── AuthorizationMatrixTest.php
    │   ├── StatusTamperingTest.php
    │   └── UserManagementTest.php
    ├── ExampleTest.php
    ├── JadwalKegiatanCrudTest.php
    ├── KonsultasiCrudTest.php
    ├── NavigationRoutesTest.php
    ├── PenjadwalanUlangCrudTest.php
    ├── PenjadwalanUlangRegressionTest.php
    └── TimezoneValidationTest.php
```
