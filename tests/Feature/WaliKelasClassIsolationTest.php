<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

test('wali kelas can only view students from assigned homeroom classes', function () {
    // Roles & permissions
    $viewPerm = Permission::firstOrCreate(['name' => 'student.view'], ['group' => 'student', 'display_name' => 'Lihat Siswa']);
    $waliRole = Role::firstOrCreate(['name' => 'Wali Kelas'], ['display_name' => 'Wali Kelas']);
    $waliRole->permissions()->syncWithoutDetaching([$viewPerm->id]);

    // Classes
    $classA = SchoolClass::create(['name' => '7A', 'grade' => '7']);
    $classB = SchoolClass::create(['name' => '7B', 'grade' => '7']);

    // Students
    $studentA = Student::create(['name' => 'Student A', 'nis' => '1001', 'class_id' => $classA->id, 'gender' => 'L']);
    $studentB = Student::create(['name' => 'Student B', 'nis' => '1002', 'class_id' => $classB->id, 'gender' => 'P']);

    // Wali Kelas User assigned ONLY to classA
    $waliUser = User::factory()->create();
    $waliUser->roles()->attach($waliRole);
    $teacher = Teacher::create([
        'user_id' => $waliUser->id,
        'name' => $waliUser->name,
        'position' => 'Wali Kelas',
    ]);
    $teacher->homeroomClasses()->attach($classA->id);

    // Act as Wali Kelas
    $response = $this->actingAs($waliUser)->get(route('students.index'));
    $response->assertOk();

    // Student A detail should be accessible
    $responseA = $this->actingAs($waliUser)->get(route('students.show', $studentA));
    $responseA->assertOk();

    // Student B detail should be forbidden (403)
    $responseB = $this->actingAs($waliUser)->get(route('students.show', $studentB));
    $responseB->assertForbidden();
});

test('wali kelas can be assigned multiple classes and access students from all of them', function () {
    $viewPerm = Permission::firstOrCreate(['name' => 'student.view'], ['group' => 'student', 'display_name' => 'Lihat Siswa']);
    $waliRole = Role::firstOrCreate(['name' => 'Wali Kelas'], ['display_name' => 'Wali Kelas']);
    $waliRole->permissions()->syncWithoutDetaching([$viewPerm->id]);

    $class1 = SchoolClass::create(['name' => '1A', 'grade' => '1']); // SD
    $class2 = SchoolClass::create(['name' => '2B', 'grade' => '2']); // SD
    $class3 = SchoolClass::create(['name' => '3C', 'grade' => '3']); // SD

    $student1 = Student::create(['name' => 'Anak 1', 'nis' => '2001', 'class_id' => $class1->id, 'gender' => 'L']);
    $student2 = Student::create(['name' => 'Anak 2', 'nis' => '2002', 'class_id' => $class2->id, 'gender' => 'P']);
    $student3 = Student::create(['name' => 'Anak 3', 'nis' => '2003', 'class_id' => $class3->id, 'gender' => 'L']);

    $waliUser = User::factory()->create();
    $waliUser->roles()->attach($waliRole);
    $teacher = Teacher::create([
        'user_id' => $waliUser->id,
        'name' => $waliUser->name,
        'position' => 'Wali Kelas',
    ]);
    // Assign 2 classes to this 1 teacher
    $teacher->homeroomClasses()->attach([$class1->id, $class2->id]);

    // Student 1 & 2 accessible
    $this->actingAs($waliUser)->get(route('students.show', $student1))->assertOk();
    $this->actingAs($waliUser)->get(route('students.show', $student2))->assertOk();

    // Student 3 forbidden
    $this->actingAs($waliUser)->get(route('students.show', $student3))->assertForbidden();
});

test('superadmin can create customizable classes including SD and SMA', function () {
    $superadmin = User::factory()->create(['is_system_user' => true]);

    // Create SD Class
    $responseSd = $this->actingAs($superadmin)->post(route('classes.store'), [
        'name' => '1 SD Teladan',
        'grade' => '1',
    ]);
    $responseSd->assertSessionHas('success');
    $this->assertDatabaseHas('classes', ['name' => '1 SD Teladan', 'grade' => '1']);

    // Create SMA Class
    $responseSma = $this->actingAs($superadmin)->post(route('classes.store'), [
        'name' => 'XII MIPA 3',
        'grade' => '12',
    ]);
    $responseSma->assertSessionHas('success');
    $this->assertDatabaseHas('classes', ['name' => 'XII MIPA 3', 'grade' => '12']);
});

test('creating user with wali kelas role and class_ids synchronizes teacher homeroom classes', function () {
    $superadmin = User::factory()->create(['is_system_user' => true]);
    $waliRole = Role::firstOrCreate(['name' => 'Wali Kelas'], ['display_name' => 'Wali Kelas']);

    $classX = SchoolClass::create(['name' => 'X-A', 'grade' => '10']);
    $classY = SchoolClass::create(['name' => 'X-B', 'grade' => '10']);

    $response = $this->actingAs($superadmin)->post(route('users.store'), [
        'name' => 'Guru Wali Baru',
        'email' => 'wali.baru@sekolah.sch.id',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'role_ids' => [$waliRole->id],
        'class_ids' => [$classX->id, $classY->id],
    ]);

    $response->assertSessionHas('success');

    $newUser = User::where('email', 'wali.baru@sekolah.sch.id')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->teacher)->not->toBeNull();

    $assignedIds = $newUser->getHomeroomClassIds();
    expect($assignedIds)->toContain($classX->id);
    expect($assignedIds)->toContain($classY->id);
});

test('creating user with siswa role creates and links student profile with class', function () {
    $superadmin = User::factory()->create(['is_system_user' => true]);
    $siswaRole = Role::firstOrCreate(['name' => 'Siswa'], ['display_name' => 'Siswa']);
    $class7A = SchoolClass::create(['name' => '7A SMP', 'grade' => '7']);

    $response = $this->actingAs($superadmin)->post(route('users.store'), [
        'name' => 'Siswa Baru Cerdas',
        'email' => 'siswa.cerdas@sekolah.sch.id',
        'identifier_number' => '242507999',
        'gender' => 'P',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'role_ids' => [$siswaRole->id],
        'student_class_id' => $class7A->id,
    ]);

    $response->assertSessionHas('success');

    $newUser = User::where('email', 'siswa.cerdas@sekolah.sch.id')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->student)->not->toBeNull();
    expect($newUser->student->class_id)->toBe($class7A->id);
    expect($newUser->student->gender)->toBe('P');
    expect($newUser->student->nis)->toBe('242507999');
});

test('creating user with guru bk role creates teacher profile', function () {
    $superadmin = User::factory()->create(['is_system_user' => true]);
    $bkRole = Role::firstOrCreate(['name' => 'Guru BK'], ['display_name' => 'Guru BK']);

    $response = $this->actingAs($superadmin)->post(route('users.store'), [
        'name' => 'Konselor BK Baru',
        'email' => 'bk.baru@sekolah.sch.id',
        'identifier_number' => '198501012010011005',
        'gender' => 'L',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'role_ids' => [$bkRole->id],
    ]);

    $response->assertSessionHas('success');

    $newUser = User::where('email', 'bk.baru@sekolah.sch.id')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->teacher)->not->toBeNull();
    expect($newUser->teacher->position)->toBe('Guru BK');
});
