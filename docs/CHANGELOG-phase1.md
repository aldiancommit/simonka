# CHANGELOG - FASE 1: HARDENING & SECURITY

Dokumen ini mencatat seluruh perubahan arsitektur, konfigurasi, dan penanganan bug pada Fase 1 Hardening sistem SIMONKA.

---

## 1. Isolasi Database Pengujian (`phpunit.xml` & `tests/TestCase.php`)
* **Masalah:** Konfigurasi `phpunit.xml` sebelumnya mengarah ke basis data MySQL `simonka_db` tanpa isolasi terpisah, sehingga tes berisiko memutasi data.
* **Perubahan:**
  - `phpunit.xml` diarahkan secara eksklusif ke basis data terpisah `simonka_test`.
  - Menambahkan sistem pengaman `ensureSafeTestDatabase()` pada `tests/TestCase.php` yang langsung menghentikan eksekusi tes (*RuntimeException*) jika database aktif bukan `simonka_test`.
  - Mengaktifkan `RefreshDatabase` secara global di `tests/Pest.php` untuk seluruh Feature tests.
  - Memperbaiki `NavigationRoutesTest.php` agar menggunakan pemanggilan `factory()->create()` langsung.

---

## 2. Standardisasi Timezone Lokal (`Asia/Makassar` - WITA / UTC+8)
* **Masalah:** Konfigurasi default aplikasi sebelumnya adalah `'timezone' => 'UTC'`. Di wilayah operasional Kota Palu (WITA, UTC+8), waktu server berselisih 8 jam di belakang waktu lokal.
  - Antara pukul 00:00 - 07:59 WITA, fungsi `today()` menghasilkan tanggal kemarin.
  - Validasi `after_or_equal:today` pada form konsultasi dan reschedule mengizinkan input tanggal kemarin.
  - Metrik dashboard `$konsultasiHariIni` menghitung data tanggal kemarin pada jam kerja pagi.
* **Perubahan:**
  - Menetapkan `'timezone' => env('APP_TIMEZONE', 'Asia/Makassar')` pada `config/app.php`.
  - Menambahkan variabel `APP_TIMEZONE=Asia/Makassar` pada `.env` dan `.env.example`.
  - Menambahkan pengujian otomatis `tests/Feature/TimezoneValidationTest.php` untuk memastikan validasi `after_or_equal:today` berjalan tepat pada dini hari WITA.
* **Dampak pada Data Historis `created_at` / `updated_at`:**
  - Kolom timestamp `created_at` dan `updated_at` yang dibuat sebelum perubahan ini tersimpan dalam format UTC tanpa penanda offset.
  - Dengan perubahan timezone ke `Asia/Makassar`, Laravel membaca string timestamp tersebut langsung dalam konteks WITA.
  - Mengingat basis data masih dalam tahap pengembangan dan belum ada data transaksional riil produksi, **tidak dilakukan migrasi data historis** guna menjaga integritas data tanpa manipulasi manual. Data baru selanjutnya akan otomatis tersimpan dan ditampilkan selaras dengan zona waktu WITA.
