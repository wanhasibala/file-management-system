<?php

use App\Models\Department;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);
});

test('administrator can create department', function () {
    $response = $this->actingAs($this->admin)->post(route('departments.store'), [
        'name' => 'Corporate Legal',
        'code' => 'LEG-CORP',
        'description' => 'Legal and regulatory affairs',
        'color' => 'purple',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('departments', [
        'name' => 'Corporate Legal',
        'code' => 'LEG-CORP',
    ]);
});

test('administrator can update department', function () {
    $department = Department::create([
        'name' => 'Human Capital',
        'code' => 'HC',
        'color' => 'emerald',
    ]);

    $response = $this->actingAs($this->admin)->put(route('departments.update', $department->id), [
        'name' => 'People & Culture',
        'code' => 'PNC',
        'color' => 'rose',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'People & Culture',
        'code' => 'PNC',
    ]);
});

test('administrator can delete department', function () {
    $department = Department::create([
        'name' => 'Temporary Dept',
        'code' => 'TEMP',
    ]);

    $response = $this->actingAs($this->admin)->delete(route('departments.destroy', $department->id));
    $response->assertSessionHasNoErrors();
    $this->assertSoftDeleted('departments', ['id' => $department->id]);
});

test('viewer cannot create department and receives 403', function () {
    $response = $this->actingAs($this->viewer)->post(route('departments.store'), [
        'name' => 'Unauthorized Dept',
        'code' => 'UNAUTH',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('departments', ['code' => 'UNAUTH']);
});
