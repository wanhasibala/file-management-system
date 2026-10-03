<?php

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $this->viewer = User::factory()->create([
        'role' => User::ROLE_VIEWER,
    ]);

    $this->department = Department::create([
        'name' => 'IT Operations',
        'code' => 'IT-OPS',
        'color' => 'blue',
    ]);
});

test('administrator has full access to create folders', function () {
    $response = $this->actingAs($this->admin)->post(route('folders.store'), [
        'name' => 'Admin Secret Vault',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('folders', ['name' => 'Admin Secret Vault']);
});

test('viewer cannot create folders and receives 403 forbidden', function () {
    $response = $this->actingAs($this->viewer)->post(route('folders.store'), [
        'name' => 'Unauthorized Folder',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('folders', ['name' => 'Unauthorized Folder']);
});

test('viewer cannot upload documents and receives 403 forbidden', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->viewer)->post(route('files.store'), [
        'title' => 'Unauthorized Document',
        'department_id' => $this->department->id,
        'file' => UploadedFile::fake()->create('hacked.pdf', 100),
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('files', ['title' => 'Unauthorized Document']);
});

test('viewer cannot delete files and receives 403 forbidden', function () {
    Storage::fake('public');
    $file = File::create([
        'title' => 'Confidential Doc',
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,
        'file_name' => 'confidential.pdf',
        'file_path' => 'documents/confidential.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'extension' => 'pdf',
    ]);

    $response = $this->actingAs($this->viewer)->delete(route('files.destroy', $file->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('files', ['id' => $file->id]);
});

test('viewer can download and preview files', function () {
    Storage::fake('public');
    Storage::disk('public')->put('documents/test.pdf', 'dummy pdf content');

    $file = File::create([
        'title' => 'Public Policy',
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,
        'file_name' => 'test.pdf',
        'file_path' => 'documents/test.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'extension' => 'pdf',
    ]);

    $response = $this->actingAs($this->viewer)->get(route('files.download', $file->id));
    $response->assertSuccessful();

    $previewResponse = $this->actingAs($this->viewer)->get(route('files.preview', $file->id));
    $previewResponse->assertSuccessful();
});
