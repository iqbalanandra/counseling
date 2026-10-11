<?php

use App\Models\Role;
use App\Models\User;

test('superadmin can view roles list', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->get(route('roles.index'));
    $response->assertOk();
});

test('unauthorized user cannot view roles list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('roles.index'));
    $response->assertForbidden();
});

test('superadmin can create a new dynamic role', function () {
    $admin = User::factory()->create(['is_system_user' => true]);

    $response = $this->actingAs($admin)->post(route('roles.store'), [
        'name' => 'pembina_osis',
        'display_name' => 'Pembina OSIS',
        'description' => 'Membina aktivitas organisasi kesiswaan',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('roles', [
        'name' => 'pembina_osis',
        'display_name' => 'Pembina OSIS',
    ]);
});

test('protected system roles cannot be deleted', function () {
    $admin = User::factory()->create(['is_system_user' => true]);
    $role = Role::create([
        'name' => 'ProtectedRole',
        'display_name' => 'Protected Role',
        'is_protected' => true,
    ]);

    $response = $this->actingAs($admin)->delete(route('roles.destroy', $role));

    // Protected role delete will throw validation exception or redirect with error
    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});
