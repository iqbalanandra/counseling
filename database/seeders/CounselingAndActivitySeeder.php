<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\CounselingRecord;
use App\Models\CounselingRequest;
use App\Models\CounselingSession;
use App\Models\CounselingType;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;

class CounselingAndActivitySeeder extends Seeder
{
    public function run(): void
    {
        $studentAyu = Student::where('nis', '242507001')->first();
        $studentBudi = Student::where('nis', '242507002')->first();
        $studentCitra = Student::where('nis', '242507003')->first();
        $studentDoni = Student::where('nis', '242508001')->first();
        $studentEka = Student::where('nis', '242509001')->first();
        $bkUser = User::where('email', 'bk@example.com')->first();
        $waliUser = User::where('email', 'walikelas@example.com')->first();

        $counselingTypeBelajar = CounselingType::where('name', 'Belajar')->first();
        $counselingTypePribadi = CounselingType::where('name', 'Pribadi')->first();
        $counselingTypeSosial = CounselingType::where('name', 'Sosial')->first();
        $counselingTypeKarier = CounselingType::where('name', 'Karier')->first();

        if ($studentAyu && $bkUser && $counselingTypeBelajar) {
            // 1. Completed counseling with confidential record
            $request1 = CounselingRequest::updateOrCreate(
                ['student_id' => $studentAyu->id, 'counseling_type_id' => $counselingTypeBelajar->id],
                [
                    'counselor_id' => $bkUser->id,
                    'preferred_date' => now()->subDays(5)->toDateString(),
                    'preferred_time' => '10:00 - 11:00',
                    'scheduled_datetime' => now()->subDays(5)->setTime(10, 0),
                    'complaint_summary' => 'Merasa kesulitan membagi waktu antara kegiatan ekstrakurikuler tari dan persiapan ujian tengah semester.',
                    'status' => 'completed',
                    'notes' => 'Sesi bimbingan telah dilaksanakan dengan lancar.',
                ]
            );

            $session1 = CounselingSession::updateOrCreate(
                ['counseling_request_id' => $request1->id],
                [
                    'student_id' => $studentAyu->id,
                    'counselor_id' => $bkUser->id,
                    'counseling_type_id' => $counselingTypeBelajar->id,
                    'session_date' => now()->subDays(5)->setTime(10, 0),
                    'venue' => 'Ruang Konseling BK 1',
                    'status' => 'completed',
                ]
            );

            CounselingRecord::updateOrCreate(
                ['counseling_session_id' => $session1->id],
                [
                    'student_id' => $studentAyu->id,
                    'counselor_id' => $bkUser->id,
                    'topic' => 'Manajemen Waktu Belajar dan Ekstrakurikuler',
                    'problem_description' => 'Konseli mengalami ketegangan akibat padatnya jadwal latihan tari menjelang perlombaan tingkat kota yang bersamaan dengan ulangan harian matematika.',
                    'counseling_notes' => 'Konseli bersikap terbuka dan kooperatif. Mengidentifikasi prioritas harian dan membuat jadwal belajar terstruktur 45 menit per sesi (metode Pomodoro).',
                    'assessment_diagnosis' => 'Stres ringan adaptif karena manajemen waktu yang belum terstruktur optimal.',
                    'recommendations' => 'Menerapkan jadwal belajar harian, membatasi screen time malam hari, dan mengomunikasikan jadwal latihan dengan pelatih.',
                    'follow_up_plan' => 'Evaluasi mingguan pada tanggal ' . now()->addDays(7)->format('d F Y'),
                    'is_confidential' => true,
                ]
            );

            // Scheduled session today for Ayu (Class 7A)
            CounselingSession::updateOrCreate(
                [
                    'student_id' => $studentAyu->id,
                    'session_date' => today()->setTime(10, 0),
                ],
                [
                    'counselor_id' => $bkUser->id,
                    'counseling_type_id' => $counselingTypeBelajar->id,
                    'venue' => 'Ruang Konseling BK 1',
                    'status' => 'scheduled',
                ]
            );

            // Scheduled session in 2 days for Budi (Class 7A)
            if ($studentBudi && $counselingTypePribadi) {
                CounselingSession::updateOrCreate(
                    [
                        'student_id' => $studentBudi->id,
                        'session_date' => today()->addDays(2)->setTime(9, 30),
                    ],
                    [
                        'counselor_id' => $bkUser->id,
                        'counseling_type_id' => $counselingTypePribadi->id,
                        'venue' => 'Ruang Konseling BK 2',
                        'status' => 'scheduled',
                    ]
                );

                CounselingRequest::updateOrCreate(
                    ['student_id' => $studentBudi->id, 'counseling_type_id' => $counselingTypePribadi->id],
                    [
                        'counselor_id' => $bkUser->id,
                        'preferred_date' => now()->addDays(2)->toDateString(),
                        'preferred_time' => '09:00 - 10:00',
                        'scheduled_datetime' => today()->addDays(2)->setTime(9, 30),
                        'complaint_summary' => 'Ingin berkonsultasi mengenai penyesuaian diri di kelas dan rasa cemas saat berbicara di depan umum.',
                        'status' => 'approved',
                    ]
                );
            }

            // Scheduled session tomorrow for Citra (Class 7B)
            if ($studentCitra && $counselingTypeSosial) {
                CounselingSession::updateOrCreate(
                    [
                        'student_id' => $studentCitra->id,
                        'session_date' => today()->addDays(1)->setTime(13, 0),
                    ],
                    [
                        'counselor_id' => $bkUser->id,
                        'counseling_type_id' => $counselingTypeSosial->id,
                        'venue' => 'Ruang Konseling BK 1',
                        'status' => 'scheduled',
                    ]
                );
            }

            // Scheduled session in 3 days for Doni (Class 8A)
            if ($studentDoni && $counselingTypeBelajar) {
                CounselingSession::updateOrCreate(
                    [
                        'student_id' => $studentDoni->id,
                        'session_date' => today()->addDays(3)->setTime(11, 0),
                    ],
                    [
                        'counselor_id' => $bkUser->id,
                        'counseling_type_id' => $counselingTypeBelajar->id,
                        'venue' => 'Ruang Diskusi BK',
                        'status' => 'scheduled',
                    ]
                );
            }

            // Scheduled session in 5 days for Eka (Class 9A)
            if ($studentEka && $counselingTypeKarier) {
                CounselingSession::updateOrCreate(
                    [
                        'student_id' => $studentEka->id,
                        'session_date' => today()->addDays(5)->setTime(8, 30),
                    ],
                    [
                        'counselor_id' => $bkUser->id,
                        'counseling_type_id' => $counselingTypeKarier->id,
                        'venue' => 'Ruang Konseling Utama',
                        'status' => 'scheduled',
                    ]
                );
            }
        }

        // Violations
        $violationLate = ViolationCategory::where('name', 'Keterlambatan & Kehadiran')->first();
        if ($studentBudi && $violationLate && $waliUser) {
            Violation::updateOrCreate(
                ['student_id' => $studentBudi->id, 'violation_date' => now()->subDays(3)->toDateString()],
                [
                    'violation_category_id' => $violationLate->id,
                    'recorded_by_user_id' => $waliUser->id,
                    'points' => 10,
                    'description' => 'Terlambat masuk sekolah lebih dari 20 menit pada jam pelajaran pertama.',
                    'sanction_given' => 'Peringatan lisan dan merapikan perpustakaan sekolah selama 15 menit.',
                    'status' => 'resolved',
                    'follow_up_notes' => 'Siswa berjanji tidak mengulangi dan hadir tepat waktu.',
                ]
            );
        }

        // Achievements
        $achievArt = AchievementCategory::where('name', 'Seni & Budaya')->first();
        if ($studentAyu && $achievArt && $bkUser) {
            Achievement::updateOrCreate(
                ['student_id' => $studentAyu->id, 'title' => 'Juara 1 Lomba Tari Tradisional Kreasi'],
                [
                    'achievement_category_id' => $achievArt->id,
                    'recorded_by_user_id' => $bkUser->id,
                    'level' => 'Provinsi',
                    'rank' => 'Juara 1',
                    'points' => 35,
                    'event_date' => now()->subDays(14)->toDateString(),
                    'organizer' => 'Dinas Kebudayaan Provinsi',
                    'description' => 'Meraih medali emas dan piagam penghargaan dalam festival seni pelajar tingkat provinsi.',
                    'verification_status' => 'verified',
                ]
            );
        }

        // Notification
        if ($bkUser) {
            AppNotification::updateOrCreate(
                ['user_id' => $bkUser->id, 'title' => 'Permohonan Konseling Baru'],
                [
                    'message' => 'Siswa Budi Santoso (7A) mengajukan permohonan konseling Pribadi.',
                    'type' => 'counseling',
                    'link' => '/counseling',
                    'created_at' => now(),
                ]
            );
        }

        // Initial Audit Log
        AuditLog::create([
            'user_name' => 'System Seeder',
            'action' => 'seed',
            'module' => 'system',
            'description' => 'Inisialisasi data master sekolah, role RBAC, dan contoh transaksi',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);
    }
}
