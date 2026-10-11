<?php

use App\Models\User;

test('authorized users can view reports center', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->get(route('reports.index'));
    $response->assertOk();
});

test('unauthorized users cannot view reports center', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.index'));
    $response->assertForbidden();
});

test('authorized users can download counseling pdf report', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->get(route('reports.export-pdf', [
        'type' => 'counseling',
        'start_date' => now()->startOfMonth()->toDateString(),
        'end_date' => now()->toDateString(),
    ]));

    $response->assertOk();
    $this->assertTrue(str_contains($response->headers->get('content-type') ?? '', 'application/pdf'));
});
