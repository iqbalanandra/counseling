<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\CounselingType;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Academic Year
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 'ganjil'],
            ['is_active' => true]
        );

        // 2. Counseling Types
        $counselingTypes = [
            ['name' => 'Pribadi', 'description' => 'Layanan bantuan masalah pribadi, emosi, penyesuaian diri, dan kepercayaan diri'],
            ['name' => 'Sosial', 'description' => 'Layanan bantuan hubungan sosial, interaksi teman sebaya, keluarga, dan lingkungan'],
            ['name' => 'Belajar', 'description' => 'Layanan peningkatan motivasi belajar, kesulitan memahami pelajaran, dan manajemen waktu'],
            ['name' => 'Karier', 'description' => 'Layanan bimbingan minat bakat, pemilihan jurusan SMA/SMK, dan eksplorasi masa depan'],
        ];
        foreach ($counselingTypes as $ct) {
            CounselingType::updateOrCreate(['name' => $ct['name']], $ct);
        }

        // 3. Violation Categories
        $violationCategories = [
            ['name' => 'Keterlambatan & Kehadiran', 'description' => 'Terlambat masuk sekolah, bolos pelajaran, alpa tanpa keterangan', 'points' => 10, 'sanction' => 'Peringatan lisan dan pembinaan wali kelas'],
            ['name' => 'Kerapian & Seragam', 'description' => 'Seragam tidak sesuai ketentuan, tidak memakai atribut lengkap, rambut tidak rapi', 'points' => 5, 'sanction' => 'Teguran dan perapian pakaian/atribut'],
            ['name' => 'Kedisiplinan & Ketertiban', 'description' => 'Membawa barang terlarang, membuat kegaduhan, menggunakan gawai saat jam pelajaran tanpa izin', 'points' => 15, 'sanction' => 'Penyitaan sementara dan pembinaan BK'],
            ['name' => 'Perilaku & Etika', 'description' => 'Berkata kasar, tidak menghormati guru/teman, perkelahian ringan, perundungan', 'points' => 25, 'sanction' => 'Panggilan orang tua dan bimbingan konseling intensif'],
        ];
        foreach ($violationCategories as $vc) {
            ViolationCategory::updateOrCreate(['name' => $vc['name']], $vc);
        }

        // 4. Achievement Categories
        $achievementCategories = [
            ['name' => 'Akademik', 'description' => 'Olimpiade sains, matematika, cerdas cermat, karya ilmiah remaja'],
            ['name' => 'Olahraga', 'description' => 'Futsal, basket, bulutangkis, atletik, bela diri'],
            ['name' => 'Seni & Budaya', 'description' => 'Menyanyi, tari tradisional, melukis, musik, teater'],
            ['name' => 'Keagamaan', 'description' => 'Musabaqah Tilawatil Quran, hafalan Al-Quran, cerdas cermat agama'],
            ['name' => 'Kepemimpinan & Organisasi', 'description' => 'OSIS, Pramuka, Palang Merah Remaja (PMR)'],
        ];
        foreach ($achievementCategories as $ac) {
            AchievementCategory::updateOrCreate(['name' => $ac['name']], $ac);
        }

        // 5. Seed Users & Profiles
        // Superadmin User
        $superadminUser = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Administrator Utama',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'identifier_number' => 'ADM-001',
                'status' => 'active',
                'is_system_user' => true,
                'email_verified_at' => now(),
            ]
        );
        $superadminUser->roles()->syncWithoutDetaching([1]);

        // Guru BK User & Profile
        $bkUser = User::updateOrCreate(
            ['email' => 'bk@example.com'],
            [
                'name' => 'Dra. Hj. Nurjanah, M.Pd',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'identifier_number' => '197505122000032001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $bkUser->roles()->syncWithoutDetaching([2]); // Guru BK
        Teacher::updateOrCreate(
            ['user_id' => $bkUser->id],
            [
                'nip' => '197505122000032001',
                'name' => 'Dra. Hj. Nurjanah, M.Pd',
                'gender' => 'P',
                'phone' => '081234567891',
                'position' => 'Koordinator Guru BK',
            ]
        );

        // Kepala Sekolah User & Profile
        $kepalaUser = User::updateOrCreate(
            ['email' => 'kepala@example.com'],
            [
                'name' => 'Drs. Bambang Sutrisno, M.Pd',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'identifier_number' => '196803151995121001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $kepalaUser->roles()->syncWithoutDetaching([3]); // Kepala Sekolah
        Teacher::updateOrCreate(
            ['user_id' => $kepalaUser->id],
            [
                'nip' => '196803151995121001',
                'name' => 'Drs. Bambang Sutrisno, M.Pd',
                'gender' => 'L',
                'phone' => '081234567892',
                'position' => 'Kepala Sekolah',
            ]
        );

        // Wali Kelas User & Profile
        $waliUser = User::updateOrCreate(
            ['email' => 'walikelas@example.com'],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'identifier_number' => '198802202015031002',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $waliUser->roles()->syncWithoutDetaching([4]); // Wali Kelas
        $waliTeacher = Teacher::updateOrCreate(
            ['user_id' => $waliUser->id],
            [
                'nip' => '198802202015031002',
                'name' => 'Ahmad Fauzi, S.Pd',
                'gender' => 'L',
                'phone' => '081234567893',
                'position' => 'Wali Kelas 7A',
            ]
        );

        // 6. Classes
        $class7A = SchoolClass::updateOrCreate(
            ['name' => '7A'],
            [
                'grade' => '7',
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $waliTeacher->id,
            ]
        );

        $class7B = SchoolClass::updateOrCreate(
            ['name' => '7B'],
            [
                'grade' => '7',
                'academic_year_id' => $academicYear->id,
            ]
        );

        $class8A = SchoolClass::updateOrCreate(
            ['name' => '8A'],
            [
                'grade' => '8',
                'academic_year_id' => $academicYear->id,
            ]
        );

        $class9A = SchoolClass::updateOrCreate(
            ['name' => '9A'],
            [
                'grade' => '9',
                'academic_year_id' => $academicYear->id,
            ]
        );

        // 7. Siswa User & Profile (Kelas 7A)
        $siswaUser = User::updateOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Ayu Lestari',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'identifier_number' => '242507001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $siswaUser->roles()->syncWithoutDetaching([5]); // Siswa
        $studentAyu = Student::updateOrCreate(
            ['user_id' => $siswaUser->id],
            [
                'nis' => '242507001',
                'nisn' => '0098765432',
                'name' => 'Ayu Lestari',
                'gender' => 'P',
                'birth_place' => 'Jakarta',
                'birth_date' => '2012-04-18',
                'address' => 'Jl. Mawar No. 12, Kelurahan Melati',
                'parent_name' => 'Suryadi',
                'parent_phone' => '081298765432',
                'class_id' => $class7A->id,
                'status' => 'active',
                'total_violation_points' => 0,
                'total_achievement_points' => 35,
            ]
        );

        // Add additional sample students in 7A and 7B
        $studentsData = [
            ['nis' => '242507002', 'nisn' => '0098765433', 'name' => 'Budi Santoso', 'gender' => 'L', 'class_id' => $class7A->id, 'v_pts' => 15, 'a_pts' => 0],
            ['nis' => '242507003', 'nisn' => '0098765434', 'name' => 'Citra Dewi', 'gender' => 'P', 'class_id' => $class7A->id, 'v_pts' => 0, 'a_pts' => 20],
            ['nis' => '242507004', 'nisn' => '0098765435', 'name' => 'Dimas Pratama', 'gender' => 'L', 'class_id' => $class7A->id, 'v_pts' => 10, 'a_pts' => 10],
            ['nis' => '242507005', 'nisn' => '0098765436', 'name' => 'Eka Putri Rahayu', 'gender' => 'P', 'class_id' => $class7B->id, 'v_pts' => 0, 'a_pts' => 15],
            ['nis' => '242507006', 'nisn' => '0098765437', 'name' => 'Fajar Nugraha', 'gender' => 'L', 'class_id' => $class7B->id, 'v_pts' => 25, 'a_pts' => 0],
        ];

        foreach ($studentsData as $sd) {
            Student::updateOrCreate(
                ['nis' => $sd['nis']],
                [
                    'nisn' => $sd['nisn'],
                    'name' => $sd['name'],
                    'gender' => $sd['gender'],
                    'birth_place' => 'Jakarta',
                    'birth_date' => '2012-06-10',
                    'class_id' => $sd['class_id'],
                    'status' => 'active',
                    'total_violation_points' => $sd['v_pts'],
                    'total_achievement_points' => $sd['a_pts'],
                ]
            );
        }
    }
}
