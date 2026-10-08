# CHANGELOG - FASE 1: HARDENING & SECURITY

Dokumen ini mencatat seluruh perubahan arsitektur, konfigurasi, penanganan bug integritas data, serta hasil pengujian pada **Fase 1 Hardening** sistem SIMONKA.

---

## 1. Ringkasan Perubahan per Modul

### 1.1 Isolasi Lingkungan Pengujian Database
* **Masalah:** Konfigurasi pengujian sebelumnya mengarah ke basis data utama `simonka_db` tanpa isolasi, berpotensi memutasi atau mengosongkan data development/operasional.
* **Perubahan Solutif:**
  - `phpunit.xml` diarahkan secara eksklusif ke basis data terpisah `simonka_test`.
  - Menambahkan pengaman `ensureSafeTestDatabase()` pada `tests/TestCase.php` yang langsung memutus eksekusi tes (*RuntimeException*) apabila koneksi DB aktif bukan `simonka_test`.
  - Mengaktifkan `RefreshDatabase` di `tests/Pest.php` untuk seluruh Feature tests agar skema dan transaksi selalu bersih serta independen.
  - Memperbaiki `tests/Feature/NavigationRoutesTest.php` agar menggunakan `factory()->create()` murni tanpa fallback ke basis data riil.

### 1.2 Standardisasi Zona Waktu (`Asia/Makassar` - WITA / UTC+8)
* **Masalah:** Konfigurasi default sebelumnya adalah UTC, menyebabkan perbedaan 8 jam dari zona operasional Kota Palu (WITA). Dini hari hingga pukul 07:59 WITA, fungsi `today()` menghasilkan tanggal kemarin sehingga validasi `after_or_equal:today` mengizinkan input tanggal lampau dan metrik harian dashboard keliru.
* **Perubahan Solutif:**
  - Mengatur `'timezone' => env('APP_TIMEZONE', 'Asia/Makassar')` pada `config/app.php`.
  - Menambahkan `APP_TIMEZONE=Asia/Makassar` pada file `.env` dan `.env.example`.
  - Menambahkan file tes `tests/Feature/TimezoneValidationTest.php` dengan pembekuan waktu (`Carbon::setTestNow`) untuk memvalidasi batas pergantian hari WITA secara deterministik.
  - **Dampak Data Historis:** Timestamp historis dibiarkan apa adanya tanpa migrasi manual guna mencegah korupsi data; data baru otomatis tercatat dalam zona waktu WITA.

### 1.3 Penguatan Integritas & Perbaikan Reschedule (`PenjadwalanUlang`)
* **Masalah & Celah yang Diperbaiki:**
  1. *Bug Waktu Selesai:* Menyetujui reschedule tanpa `waktu_selesai_baru` (`null`) sebelumnya menimpa jam selesai asli konsultasi menjadi `null`. Sekarang menggunakan null coalescing (`$waktu_selesai_baru ?? $konsultasi->waktu_selesai`).
  2. *Kehilangan Riwayat & Schedule Revert:* Saat reschedule berstatus `Disetujui` diubah kembali ke status `Ditolak` atau `Menunggu`, jadwal konsultasi tidak pernah dipulihkan. Sekarang jadwal lama disimpan ke kolom snapshot dan otomatis dipulihkan jika status keluar dari `Disetujui`.
  3. *Auto-Reject Pengajuan Reschedule Lain:* Ketika satu reschedule disetujui, pengajuan reschedule lain yang masih `Menunggu` pada konsultasi yang sama kini otomatis ditolak (`Ditolak`).
  4. *Manipulasi `tanggal_lama` Form:* Input `tanggal_lama` dari client diabaikan sepenuhnya; server membaca langsung dari relasi record `Konsultasi` di basis data.
  5. *Pembatasan Status Konsultasi:* Reschedule hanya dapat dibuat dan disetujui untuk konsultasi yang berstatus `Disetujui`. Percobaan approval pada konsultasi selain `Disetujui` langsung dibatalkan dengan `ValidationException`.
  6. *Idempotensi & Transaksional Lock:* Proses persetujuan dibungkus dalam `DB::transaction()` dengan pessimistic locking `lockForUpdate()`. Penyimpanan berulang kali dengan status `Disetujui` bersifat idempoten dan tidak merusak snapshot jadwal asli.

---

## 2. Daftar File yang Berubah

| Modul / Komponen | File | Deskripsi Perubahan |
|---|---|---|
| **Konfigurasi & Environtment** | `config/app.php` | Pengaturan timezone `Asia/Makassar` |
| | `.env`, `.env.example` | Penambahan variabel `APP_TIMEZONE=Asia/Makassar` |
| | `phpunit.xml` | Pengalihan database tes ke `simonka_test` |
| **Model & Controller** | `app/Models/PenjadwalanUlang.php` | Penambahan kolom snapshot pada `$fillable` dan `$casts` |
| | `app/Http/Controllers/PenjadwalanUlangController.php` | Pengamanan `store()`, `update()`, locking, snapshot, auto-reject, dan filter `create()` |
| | `app/Http/Requests/StorePenjadwalanUlangRequest.php` | Hapus rule `tanggal_lama`, wajibkan `konsultasi_id` berstatus `Disetujui` |
| **Migrasi Basis Data** | `database/migrations/2026_10_08_150434_add_snapshot_columns_to_penjadwalan_ulangs_table.php` | Migrasi baru: penambahan kolom snapshot |
| **Tampilan (View)** | `resources/views/penjadwalan-ulang/create.blade.php` | Hapus `name="tanggal_lama"`, jadikan murni visualisasi display |
| **Pengujian (Tests)** | `tests/TestCase.php` | Penambahan `ensureSafeTestDatabase()` safety guard |
| | `tests/Pest.php` | Aktivasi `RefreshDatabase` pada feature test suite |
| | `tests/Feature/NavigationRoutesTest.php` | Perbaikan instansiasi factory konsultasi |
| | `tests/Feature/PenjadwalanUlangCrudTest.php` | Penyesuaian status `Disetujui` pada payload test |
| | `tests/Feature/TimezoneValidationTest.php` | File baru: tes validasi batas pergantian tanggal WITA |
| | `tests/Feature/PenjadwalanUlangRegressionTest.php` | File baru: 9 kelompok skenario tes regresi lengkap reschedule |

---

## 3. Hasil Pengujian Sebelum vs Sesudah

* **Sebelum Fase 1:** 26 tes (berjalan pada database aktif tanpa isolasi `RefreshDatabase`).
* **Setelah Fase 1:** **42 tes (100% Passed / Hijau)** dengan isolasi database `simonka_test` dan 109 assertions.

```text
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\AuthTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\JadwalKegiatanCrudTest
   PASS  Tests\Feature\KonsultasiCrudTest
   PASS  Tests\Feature\NavigationRoutesTest
   PASS  Tests\Feature\PenjadwalanUlangCrudTest
   PASS  Tests\Feature\PenjadwalanUlangRegressionTest
   PASS  Tests\Feature\TimezoneValidationTest

  Tests:    42 passed (109 assertions)
  Duration: 1.03s
```

---

## 4. Migrasi Basis Data & Panduan Eksekusi

Satu migrasi baru telah dibuat untuk tabel `penjadwalan_ulangs`:
* **File Migrasi:** `database/migrations/2026_10_08_150434_add_snapshot_columns_to_penjadwalan_ulangs_table.php`
* **Kolom Baru:**
  - `snapshot_tanggal_lama` (`DATE`, `NULLABLE`)
  - `snapshot_waktu_mulai_lama` (`TIME`, `NULLABLE`)
  - `snapshot_waktu_selesai_lama` (`TIME`, `NULLABLE`)

### Perintah Migrasi untuk Lingkungan Database Utama (`simonka_db`):
Jalankan perintah berikut di server / environment lokal Anda:
```bash
php artisan migrate
```

---

## 5. Keputusan Bisnis yang Telah Ditetapkan
1. **Aturan Kelayakan Pengajuan Reschedule:** Pengajuan penjadwalan ulang (`PenjadwalanUlang`) **hanya diizinkan untuk permohonan konsultasi yang berstatus `Disetujui`**. Permohonan dengan status `Menunggu`, `Ditolak`, `Dibatalkan`, maupun `Selesai` dilarang untuk di-reschedule.
2. **Status Konsultasi Pasca Reschedule:** Persetujuan reschedule **TIDAK mengubah status permohonan konsultasi** (status konsultasi tetap `Disetujui`, hanya tanggal dan waktu mulainya yang diperbarui).
3. **Penyimpanan Snapshot Jadwal:** Snapshot jadwal asli dicatat secara permanen saat persetujuan pertama kali dilakukan guna menjamin pemulihan jadwal jika status di kemudian hari diubah ke `Ditolak` atau `Menunggu`.

---

## 6. Daftar Risiko Tersisa & Pekerjaan Lanjutan (Fase Berikutnya)

Berikut adalah item penting yang **BELUM dikerjakan** dan direkomendasikan masuk ke fase pengembangan selanjutnya:

1. **Autentikasi & Otorisasi Pengguna (Auth Middleware & Policies):**
   - Rute-rute modul admin (`/konsultasi`, `/jadwal-kegiatan`, `/penjadwalan-ulang`, `/dashboard`) saat ini belum diproteksi oleh middleware `auth` sehingga masih dapat diakses secara publik.
   - Belum adanya Laravel Policy untuk memisahkan hak akses pemohon/publik vs admin operator.
2. **Formula Perhitungan Metrik Dashboard (`DashboardController`):**
   - Agregasi rasio penyelesaian dan total konsultasi pada dashboard belum memfilter konsultasi yang berstatus `Dibatalkan` atau `Ditolak`.
3. **Integrasi FullCalendar & Filter Event:**
   - Feed API kalender saat ini mengambil seluruh data tanpa filter rentang tanggal viewport (`start` - `end`).
   - Jadwal berstatus `Dibatalkan` masih berpotensi tampil di feed kalender.
4. **Asset CDN & Fallback Offline:**
   - Template frontend mengandalkan CDN publik eksternal (Bootstrap, FontAwesome, Google Fonts, FullCalendar). Perlu disediakan bundle lokal / offline asset fallback.
5. **Implementasi Soft Deletes:**
   - Model `Konsultasi`, `JadwalKegiatan`, dan `PenjadwalanUlang` saat ini menggunakan hard delete (`DELETE CASCADE`), yang berisiko menghilangkan riwayat audit data secara permanen bila terhapus tidak sengaja.
6. **Kredensial Database Pengujian:**
   - Kredensial MySQL `simonka_test` saat ini didefinisikan secara statis di `phpunit.xml`. Disarankan memindahkannya ke file `.env.testing` atau environment variable CI/CD.
