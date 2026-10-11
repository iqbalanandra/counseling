<?php

use App\Models\AuditLog;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('superadmin can access school profile page', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->get(route('admin.school-profile.edit'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Admin/SchoolProfile')->has('school'));
});

test('unauthorized user cannot access school profile page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.school-profile.edit'));
    $response->assertForbidden();
});

test('superadmin can update school profile data with audit log', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->post(route('admin.school-profile.update'), [
        'school_name' => 'SMP Cendekia Bangsa',
        'portal_name' => 'Konseling-BK',
        'tagline' => 'Bersama Meniti Cita',
        'npsn' => '20199988',
        'address' => 'Jl. Kebangsaan No. 10, Bandung',
        'phone' => '(022) 1234567',
        'whatsapp' => '081299998888',
        'email' => 'admin@cendekiabangsa.sch.id',
        'website' => 'https://cendekiabangsa.sch.id',
        'principal_name' => 'Dr. Suwandi, M.Pd',
        'welcome_message' => 'Selamat datang di layanan konseling sekolah kami.',
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('school_settings', [
        'school_name' => 'SMP Cendekia Bangsa',
        'portal_name' => 'Konseling-BK',
        'npsn' => '20199988',
        'phone' => '(022) 1234567',
        'email' => 'admin@cendekiabangsa.sch.id',
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'update',
        'module' => 'school_setting',
    ]);
});

test('superadmin can upload a new school logo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['is_system_user' => true]);
    $logoFile = UploadedFile::fake()->image('school_logo.png', 200, 200);

    $response = $this->actingAs($admin)->post(route('admin.school-profile.update'), [
        'school_name' => 'SMP Cendekia Bangsa',
        'portal_name' => 'Konseling-BK',
        'logo' => $logoFile,
    ]);

    $response->assertSessionHas('success');

    $setting = SchoolSetting::first();
    expect($setting->logo_url)->not->toBeNull();
    expect($setting->logo_url)->toContain('/storage/school/');
});

test('welcome page renders dynamic school settings data', function () {
    SchoolSetting::updateOrCreate(
        ['id' => 1],
        [
            'school_name' => 'SMP Prestasi Unggul',
            'portal_name' => 'Konseling-Hebat',
            'address' => 'Jl. Pendidikan Mandiri No. 8',
        ]
    );
    SchoolSetting::clearCache();

    $response = $this->get(route('home'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->where('school.school_name', 'SMP Prestasi Unggul')
        ->where('school.portal_name', 'Konseling-Hebat')
    );
});
