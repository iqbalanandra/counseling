<?php

use App\Models\CounselingRequest;
use App\Models\CounselingType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

test('student can submit counseling request', function () {
    $user = User::factory()->create();
    $studentRole = Role::firstOrCreate(['name' => 'Siswa'], ['display_name' => 'Siswa']);
    $user->roles()->attach($studentRole);

    $perm = Permission::firstOrCreate(
        ['name' => 'counseling.create'],
        ['display_name' => 'Ajukan Konseling', 'group' => 'counseling']
    );
    $studentRole->permissions()->syncWithoutDetaching([$perm->id]);

    $student = Student::create([
        'user_id' => $user->id,
        'nis' => 'TESTNIS001',
        'name' => $user->name,
        'gender' => 'L',
        'status' => 'active',
    ]);

    $type = CounselingType::firstOrCreate(['name' => 'Pribadi']);

    $response = $this->actingAs($user)->post(route('counseling.requests.store'), [
        'counseling_type_id' => $type->id,
        'preferred_date' => now()->addDays(2)->toDateString(),
        'preferred_time' => 'Istirahat Pertama',
        'complaint_summary' => 'Saya merasa cemas menghadapi ujian semester dan butuh arahan belajar.',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('counseling_requests', [
        'student_id' => $student->id,
        'counseling_type_id' => $type->id,
        'status' => 'pending',
    ]);
});

test('bk counselor can approve counseling request and session is created', function () {
    $bkUser = User::factory()->create(['is_system_user' => true]);

    $type = CounselingType::firstOrCreate(['name' => 'Belajar']);
    $student = Student::create([
        'nis' => 'TESTNIS002',
        'name' => 'Test Siswa',
        'gender' => 'P',
        'status' => 'active',
    ]);

    $counselingRequest = CounselingRequest::create([
        'student_id' => $student->id,
        'counseling_type_id' => $type->id,
        'preferred_date' => now()->addDays(2)->toDateString(),
        'complaint_summary' => 'Konseling kesulitan belajar kelompok.',
        'status' => 'pending',
    ]);

    $schedule = now()->addDays(2)->setTime(10, 0)->toDateTimeString();

    $response = $this->actingAs($bkUser)->patch(route('counseling.requests.approve', $counselingRequest), [
        'scheduled_datetime' => $schedule,
        'venue' => 'Ruang Konseling BK',
        'notes' => 'Silakan hadir tepat waktu.',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('counseling_requests', [
        'id' => $counselingRequest->id,
        'status' => 'approved',
    ]);
    $this->assertDatabaseHas('counseling_sessions', [
        'counseling_request_id' => $counselingRequest->id,
        'student_id' => $student->id,
        'counselor_id' => $bkUser->id,
    ]);
});

test('bk counselor can view counseling request detail page', function () {
    $bkUser = User::factory()->create(['is_system_user' => true]);

    $type = CounselingType::firstOrCreate(['name' => 'Karier']);
    $student = Student::create([
        'nis' => 'TESTNIS003',
        'name' => 'Test Siswa 3',
        'gender' => 'L',
        'status' => 'active',
    ]);

    $counselingRequest = CounselingRequest::create([
        'student_id' => $student->id,
        'counseling_type_id' => $type->id,
        'preferred_date' => now()->addDays(2)->toDateString(),
        'complaint_summary' => 'Konseling pemilihan jurusan SMA/SMK.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($bkUser)->get(route('counseling.requests.show', $counselingRequest));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Counseling/Requests/Show')
        ->has('request')
        ->has('counselors')
    );
});
