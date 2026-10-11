<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'group' => 'dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Akses halaman utama dashboard'],

            // Students
            ['name' => 'student.view', 'group' => 'student', 'display_name' => 'Lihat Data Siswa', 'description' => 'Melihat daftar dan detail siswa'],
            ['name' => 'student.create', 'group' => 'student', 'display_name' => 'Tambah Siswa', 'description' => 'Menambahkan data siswa baru'],
            ['name' => 'student.update', 'group' => 'student', 'display_name' => 'Ubah Siswa', 'description' => 'Mengubah data siswa'],
            ['name' => 'student.delete', 'group' => 'student', 'display_name' => 'Hapus Siswa', 'description' => 'Menghapus data siswa'],

            // Counseling
            ['name' => 'counseling.view', 'group' => 'counseling', 'display_name' => 'Lihat Konseling', 'description' => 'Melihat daftar dan status pengajuan konseling'],
            ['name' => 'counseling.create', 'group' => 'counseling', 'display_name' => 'Ajukan Konseling', 'description' => 'Mengajukan sesi bimbingan konseling'],
            ['name' => 'counseling.update', 'group' => 'counseling', 'display_name' => 'Ubah Konseling', 'description' => 'Memperbarui data pengajuan konseling'],
            ['name' => 'counseling.approve', 'group' => 'counseling', 'display_name' => 'Setujui Konseling', 'description' => 'Menyetujui jadwal pengajuan konseling'],
            ['name' => 'counseling.reschedule', 'group' => 'counseling', 'display_name' => 'Jadwal Ulang Konseling', 'description' => 'Mengatur ulang jadwal konseling'],
            ['name' => 'counseling.record', 'group' => 'counseling', 'display_name' => 'Catat Hasil Konseling', 'description' => 'Mengisi rekam hasil sesi konseling'],
            ['name' => 'counseling.view_private', 'group' => 'counseling', 'display_name' => 'Lihat Rekam Rahasia', 'description' => 'Melihat catatan rahasia dan diagnosis konseling'],
            ['name' => 'counseling.delete', 'group' => 'counseling', 'display_name' => 'Hapus Konseling', 'description' => 'Menghapus permohonan atau sesi konseling'],

            // Violations
            ['name' => 'violation.view', 'group' => 'violation', 'display_name' => 'Lihat Pelanggaran', 'description' => 'Melihat daftar pelanggaran tata tertib'],
            ['name' => 'violation.create', 'group' => 'violation', 'display_name' => 'Catat Pelanggaran', 'description' => 'Mencatat pelanggaran siswa'],
            ['name' => 'violation.update', 'group' => 'violation', 'display_name' => 'Ubah Pelanggaran', 'description' => 'Memperbarui data pelanggaran'],
            ['name' => 'violation.delete', 'group' => 'violation', 'display_name' => 'Hapus Pelanggaran', 'description' => 'Menghapus catatan pelanggaran'],

            // Achievements
            ['name' => 'achievement.view', 'group' => 'achievement', 'display_name' => 'Lihat Prestasi', 'description' => 'Melihat daftar prestasi siswa'],
            ['name' => 'achievement.create', 'group' => 'achievement', 'display_name' => 'Catat Prestasi', 'description' => 'Mencatat prestasi siswa'],
            ['name' => 'achievement.update', 'group' => 'achievement', 'display_name' => 'Ubah Prestasi', 'description' => 'Memperbarui data prestasi'],
            ['name' => 'achievement.delete', 'group' => 'achievement', 'display_name' => 'Hapus Prestasi', 'description' => 'Menghapus catatan prestasi'],

            // Reports
            ['name' => 'report.view', 'group' => 'report', 'display_name' => 'Lihat Laporan', 'description' => 'Melihat modul laporan'],
            ['name' => 'report.generate', 'group' => 'report', 'display_name' => 'Generate Laporan', 'description' => 'Menghasilkan rekapan laporan'],
            ['name' => 'report.export', 'group' => 'report', 'display_name' => 'Cetak / Unduh PDF', 'description' => 'Mengunduh laporan dalam format PDF'],

            // Administration
            ['name' => 'user.view', 'group' => 'user', 'display_name' => 'Lihat Pengguna', 'description' => 'Melihat daftar akun pengguna'],
            ['name' => 'user.create', 'group' => 'user', 'display_name' => 'Tambah Pengguna', 'description' => 'Membuat akun pengguna baru'],
            ['name' => 'user.update', 'group' => 'user', 'display_name' => 'Ubah Pengguna', 'description' => 'Memperbarui data pengguna'],
            ['name' => 'user.delete', 'group' => 'user', 'display_name' => 'Hapus Pengguna', 'description' => 'Menghapus akun pengguna'],

            ['name' => 'role.view', 'group' => 'role', 'display_name' => 'Lihat Peran', 'description' => 'Melihat daftar role pengguna'],
            ['name' => 'role.create', 'group' => 'role', 'display_name' => 'Tambah Peran', 'description' => 'Membuat role baru'],
            ['name' => 'role.update', 'group' => 'role', 'display_name' => 'Ubah Peran & Hak Akses', 'description' => 'Mengubah hak akses role'],
            ['name' => 'role.delete', 'group' => 'role', 'display_name' => 'Hapus Peran', 'description' => 'Menghapus role yang tidak terproteksi'],

            ['name' => 'permission.view', 'group' => 'permission', 'display_name' => 'Lihat Izin Akses', 'description' => 'Melihat matriks permission'],
            ['name' => 'permission.manage', 'group' => 'permission', 'display_name' => 'Kelola Izin Akses', 'description' => 'Mengatur relasi permission dan role'],

            ['name' => 'audit_log.view', 'group' => 'audit_log', 'display_name' => 'Lihat Audit Log', 'description' => 'Melihat rekam jejak aktivitas sistem'],

            // Monitoring scopes
            ['name' => 'monitoring.class', 'group' => 'monitoring', 'display_name' => 'Monitoring Kelas', 'description' => 'Memantau perkembangan siswa di kelas binaan'],
            ['name' => 'monitoring.school', 'group' => 'monitoring', 'display_name' => 'Monitoring Sekolah', 'description' => 'Memantau statistik dan tren keseluruhan sekolah'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // Roles
        $superadmin = Role::updateOrCreate(
            ['name' => 'Superadmin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Akses penuh ke seluruh konfigurasi sistem, peran, dan audit',
                'is_protected' => true,
            ]
        );
        $superadmin->permissions()->sync(Permission::all());

        $guruBk = Role::updateOrCreate(
            ['name' => 'Guru BK'],
            [
                'display_name' => 'Guru Bimbingan Konseling',
                'description' => 'Pengelolaan layanan konseling, rekam rahasia, pelanggaran, prestasi, dan laporan',
                'is_protected' => true,
            ]
        );
        $guruBkPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'student.view', 'student.create', 'student.update',
            'counseling.view', 'counseling.create', 'counseling.update', 'counseling.approve', 'counseling.reschedule', 'counseling.record', 'counseling.view_private', 'counseling.delete',
            'violation.view', 'violation.create', 'violation.update', 'violation.delete',
            'achievement.view', 'achievement.create', 'achievement.update', 'achievement.delete',
            'report.view', 'report.generate', 'report.export',
            'monitoring.school',
        ])->get();
        $guruBk->permissions()->sync($guruBkPermissions);

        $kepalaSekolah = Role::updateOrCreate(
            ['name' => 'Kepala Sekolah'],
            [
                'display_name' => 'Kepala Sekolah',
                'description' => 'Monitoring agregat sekolah, grafik perkembangan siswa, dan rekapitulasi laporan',
                'is_protected' => true,
            ]
        );
        $kepalaPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'student.view',
            'counseling.view', // Note: NO counseling.view_private
            'violation.view',
            'achievement.view',
            'report.view', 'report.generate', 'report.export',
            'monitoring.school',
        ])->get();
        $kepalaSekolah->permissions()->sync($kepalaPermissions);

        $waliKelas = Role::updateOrCreate(
            ['name' => 'Wali Kelas'],
            [
                'display_name' => 'Wali Kelas',
                'description' => 'Monitoring perkembangan siswa kelas binaan, input pelanggaran dan prestasi kelas',
                'is_protected' => true,
            ]
        );
        $waliKelasPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'student.view',
            'counseling.view', // Note: strictly NO counseling.view_private
            'violation.view', 'violation.create',
            'achievement.view', 'achievement.create',
            'report.view',
            'monitoring.class',
        ])->get();
        $waliKelas->permissions()->sync($waliKelasPermissions);

        $siswa = Role::updateOrCreate(
            ['name' => 'Siswa'],
            [
                'display_name' => 'Siswa / Murid',
                'description' => 'Pengajuan konseling daring, melihat riwayat konseling pribadi, catatan prestasi, dan poin pelanggaran',
                'is_protected' => true,
            ]
        );
        $siswaPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'counseling.view', 'counseling.create',
            'achievement.view', 'achievement.create',
            'violation.view',
        ])->get();
        $siswa->permissions()->sync($siswaPermissions);
    }
}
