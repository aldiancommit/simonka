<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoSeeder extends Seeder
{
    /**
     * Run the demo database seeds.
     */
    public function run(): void
    {
        $password = env('APP_DEMO_PASSWORD');

        if (empty($password)) {
            throw new RuntimeException('Variabel APP_DEMO_PASSWORD wajib dikonfigurasi di file environment sebelum menjalankan DemoSeeder.');
        }

        if (
            strlen($password) < 10
            || ! preg_match('/[a-z]/', $password)
            || ! preg_match('/[A-Z]/', $password)
            || ! preg_match('/[0-9]/', $password)
            || ! preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            throw new RuntimeException('APP_DEMO_PASSWORD harus memenuhi kebijakan keamanan: minimal 10 karakter, memuat huruf besar, huruf kecil, angka, dan simbol.');
        }

        // 1. Seed 3 demo users (Admin, Pimpinan, Sekretariat)
        $usersData = [
            [
                'name' => 'Administrator SIMONKA Demo',
                'email' => 'admin@simonka.test',
                'role' => Role::Admin,
            ],
            [
                'name' => 'Pimpinan SIMONKA Demo',
                'email' => 'pimpinan@simonka.test',
                'role' => Role::Pimpinan,
            ],
            [
                'name' => 'Sekretariat SIMONKA Demo',
                'email' => 'sekretariat@simonka.test',
                'role' => Role::Sekretariat,
            ],
        ];

        foreach ($usersData as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($u['role']);
            $user->activate();
        }

        // 2. Seed Example Data Relative to Today (WITA)
        $today = Carbon::now('Asia/Makassar')->startOfDay();

        // A. Konsultasi Examples
        $k1 = Konsultasi::updateOrCreate(
            ['nama_pemohon' => 'Drs. H. Arifin Rachman, M.Si'],
            [
                'instansi' => 'Badan Pendapatan Daerah',
                'no_telepon' => '081234567890',
                'email' => 'arifin@palukota.go.id',
                'perihal' => 'Koordinasi Evaluasi Realisasi PAD Triwulan III',
                'tanggal_konsultasi' => $today->copy()->addDays(3)->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '10:30:00',
                'status' => 'Disetujui',
                'catatan' => 'Disetujui oleh Kepala Badan untuk pembahasan teknis di ruang rapat pimpinan.',
            ]
        );

        Konsultasi::updateOrCreate(
            ['nama_pemohon' => 'Ir. Hj. Siti Nurhaliza'],
            [
                'instansi' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'no_telepon' => '081345678901',
                'email' => 'siti.pu@palukota.go.id',
                'perihal' => 'Konsultasi Perencanaan Infrastruktur Strategis',
                'tanggal_konsultasi' => $today->copy()->addDays(5)->format('Y-m-d'),
                'waktu_mulai' => '13:30:00',
                'waktu_selesai' => '15:00:00',
                'status' => 'Menunggu',
                'catatan' => 'Menunggu telaah dan disposisi dari Pimpinan.',
            ]
        );

        Konsultasi::updateOrCreate(
            ['nama_pemohon' => 'Bambang Trianto, S.STP'],
            [
                'instansi' => 'Kecamatan Palu Barat',
                'no_telepon' => '085298765432',
                'email' => 'bambang.palubarat@palukota.go.id',
                'perihal' => 'Konsultasi Percepatan Pelayanan Administrasi Wilayah',
                'tanggal_konsultasi' => $today->copy()->subDays(4)->format('Y-m-d'),
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '11:30:00',
                'status' => 'Selesai',
                'catatan' => 'Konsultasi telah terlaksana dengan baik, notulensi telah diarsipkan.',
            ]
        );

        Konsultasi::updateOrCreate(
            ['nama_pemohon' => 'Zulkifli Mansyur, SE'],
            [
                'instansi' => 'Forum Komunikasi Pengusaha Muda Palu',
                'no_telepon' => '082187654321',
                'email' => 'zulkifli@fkpmpalu.org',
                'perihal' => 'Permohonan Audiensi Kemitraan Program Pemberdayaan',
                'tanggal_konsultasi' => $today->copy()->subDays(2)->format('Y-m-d'),
                'waktu_mulai' => '14:00:00',
                'waktu_selesai' => '15:00:00',
                'status' => 'Ditolak',
                'catatan' => 'Agenda berbenturan dengan agenda dinas luar kota Kepala Badan.',
            ]
        );

        // B. Reschedule Example
        PenjadwalanUlang::updateOrCreate(
            ['konsultasi_id' => $k1->id],
            [
                'tanggal_lama' => $k1->tanggal_konsultasi->format('Y-m-d'),
                'tanggal_baru' => $today->copy()->addDays(7)->format('Y-m-d'),
                'waktu_mulai_baru' => '10:00:00',
                'waktu_selesai_baru' => '11:30:00',
                'alasan' => 'Permintaan pergeseran waktu karena pemohon ada agenda mendadak rapat koordinasi provinsi.',
                'status' => 'Menunggu',
            ]
        );

        // C. Jadwal Kegiatan Resmi Examples
        JadwalKegiatan::updateOrCreate(
            ['nama_kegiatan' => 'Rapat Koordinasi Forkopimda Tingkat Kota Palu'],
            [
                'tanggal' => $today->copy()->addDays(2)->format('Y-m-d'),
                'waktu_mulai' => '08:30:00',
                'waktu_selesai' => '11:30:00',
                'lokasi' => 'Ruang Polibu Kantor Walikota Palu',
                'keterangan' => 'Menghadiri pembahasan stabilitas dan keamanan daerah bersama Forkopimda.',
                'status' => 'Terjadwal',
            ]
        );

        JadwalKegiatan::updateOrCreate(
            ['nama_kegiatan' => 'Monitoring Lapangan Proyek Strategis Daerah'],
            [
                'tanggal' => $today->copy()->format('Y-m-d'),
                'waktu_mulai' => '13:00:00',
                'waktu_selesai' => '16:00:00',
                'lokasi' => 'Kawasan Pesisir Teluk Palu',
                'keterangan' => 'Peninjauan progres pembangunan tanggul pengaman pantai.',
                'status' => 'Berlangsung',
            ]
        );

        JadwalKegiatan::updateOrCreate(
            ['nama_kegiatan' => 'Sosialisasi Program Transformasi Digital SIMONKA'],
            [
                'tanggal' => $today->copy()->subDays(3)->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '12:00:00',
                'lokasi' => 'Aula Pertemuan Lantai 3 Bappeda',
                'keterangan' => 'Pengenalan dan uji coba alur konsultasi online kepada seluruh OPD.',
                'status' => 'Selesai',
            ]
        );
    }
}
