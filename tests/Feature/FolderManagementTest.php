<?php

use App\Models\Folder;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
});

test('administrator can create root folder and nested subfolders', function () {
    // 1. Create root folder (parent_id = null)
    $response = $this->actingAs($this->admin)->post(route('folders.store'), [
        'name' => 'Corporate Governance',
        'parent_id' => null,
        'description' => 'Top level folder',
    ]);

    $response->assertSessionHasNoErrors();
    $rootFolder = Folder::where('name', 'Corporate Governance')->firstOrFail();
    expect($rootFolder->parent_id)->toBeNull();

    // 2. Create subfolder
    $subResponse = $this->actingAs($this->admin)->post(route('folders.store'), [
        'name' => 'Board Decisions',
        'parent_id' => $rootFolder->id,
        'description' => 'Subfolder',
    ]);

    $subResponse->assertSessionHasNoErrors();
    $subFolder = Folder::where('name', 'Board Decisions')->firstOrFail();
    expect($subFolder->parent_id)->toBe($rootFolder->id);

    // 3. Create level 3 folder
    $subSubResponse = $this->actingAs($this->admin)->post(route('folders.store'), [
        'name' => 'Quarter 1 2026',
        'parent_id' => $subFolder->id,
    ]);

    $subSubFolder = Folder::where('name', 'Quarter 1 2026')->firstOrFail();
    expect($subSubFolder->parent_id)->toBe($subFolder->id);

    // Breadcrumbs check
    $breadcrumbs = $subSubFolder->getBreadcrumbs();
    expect($breadcrumbs)->toHaveCount(3);
    expect($breadcrumbs[0]['name'])->toBe('Corporate Governance');
    expect($breadcrumbs[1]['name'])->toBe('Board Decisions');
    expect($breadcrumbs[2]['name'])->toBe('Quarter 1 2026');
});

test('administrator can rename and update folder', function () {
    $folder = Folder::create([
        'name' => 'Old Folder Name',
        'user_id' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->put(route('folders.update', $folder->id), [
        'name' => 'New Renamed Folder',
        'description' => 'Updated description',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('folders', [
        'id' => $folder->id,
        'name' => 'New Renamed Folder',
        'description' => 'Updated description',
    ]);
});

test('administrator can delete folder with cascade logic', function () {
    $parent = Folder::create(['name' => 'Parent Folder', 'user_id' => $this->admin->id]);
    $child = Folder::create(['name' => 'Child Folder', 'parent_id' => $parent->id, 'user_id' => $this->admin->id]);

    $response = $this->actingAs($this->admin)->delete(route('folders.destroy', $parent->id));
    $response->assertSessionHasNoErrors();

    // Soft deleted
    $this->assertSoftDeleted('folders', ['id' => $parent->id]);
    $this->assertSoftDeleted('folders', ['id' => $child->id]);
});
