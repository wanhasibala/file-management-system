<?php

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->department = Department::create([
        'name' => 'Information Technology',
        'code' => 'IT',
        'color' => 'indigo',
    ]);

    $this->folder = Folder::create([
        'name' => 'Security Documentation',
        'user_id' => $this->user->id,
    ]);

    File::create([
        'title' => 'Cybersecurity Guidelines 2026',
        'department_id' => $this->department->id,
        'folder_id' => $this->folder->id,
        'user_id' => $this->user->id,
        'file_name' => 'cybersecurity.pdf',
        'file_path' => 'documents/cybersecurity.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 2048,
        'extension' => 'pdf',
    ]);
});

test('dashboard renders stats, latest files, and department breakdown', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->where('stats.total_files', 1)
        ->where('stats.total_folders', 1)
        ->where('stats.total_departments', 1)
        ->has('latestFiles', 1)
        ->has('departmentBreakdown', 1)
    );
});
