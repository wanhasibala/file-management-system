<?php

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->department = Department::create([
        'name' => 'Flight Operations',
        'code' => 'OPS',
        'color' => 'sky',
    ]);
});

test('administrator can upload file with required fields', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('flight_manual_2026.pdf', 500, 'application/pdf');

    $response = $this->actingAs($this->admin)->post(route('files.store'), [
        'title' => 'Flight Operations Standard Manual 2026',
        'department_id' => $this->department->id,
        'file' => $file,
        'description' => 'Important manual for all flight crew.',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('files', [
        'title' => 'Flight Operations Standard Manual 2026',
        'department_id' => $this->department->id,
        'file_name' => 'flight_manual_2026.pdf',
        'extension' => 'pdf',
    ]);

    $createdFile = File::where('title', 'Flight Operations Standard Manual 2026')->first();
    Storage::disk('public')->assertExists($createdFile->file_path);
});

test('file upload validates mandatory fields', function () {
    $response = $this->actingAs($this->admin)->post(route('files.store'), []);

    $response->assertSessionHasErrors(['title', 'department_id', 'file']);
});

test('file download increments download count', function () {
    Storage::fake('public');
    Storage::disk('public')->put('documents/test.pdf', 'dummy content');

    $file = File::create([
        'title' => 'Test Download Document',
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,
        'file_name' => 'test.pdf',
        'file_path' => 'documents/test.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'extension' => 'pdf',
        'download_count' => 0,
    ]);

    $this->actingAs($this->admin)->get(route('files.download', $file->id));

    $file->refresh();
    expect($file->download_count)->toBe(1);
});

test('administrator can update file metadata', function () {
    Storage::fake('public');
    $file = File::create([
        'title' => 'Initial Title',
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,
        'file_name' => 'initial.pdf',
        'file_path' => 'documents/initial.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'extension' => 'pdf',
    ]);

    $response = $this->actingAs($this->admin)->put(route('files.update', $file->id), [
        'title' => 'Updated Document Title',
        'department_id' => $this->department->id,
        'description' => 'Updated notes',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('files', [
        'id' => $file->id,
        'title' => 'Updated Document Title',
        'description' => 'Updated notes',
    ]);
});

test('administrator can delete file with soft delete', function () {
    Storage::fake('public');
    $file = File::create([
        'title' => 'File To Delete',
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,
        'file_name' => 'delete_me.pdf',
        'file_path' => 'documents/delete_me.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'extension' => 'pdf',
    ]);

    $response = $this->actingAs($this->admin)->delete(route('files.destroy', $file->id));
    $response->assertSessionHasNoErrors();

    $this->assertSoftDeleted('files', ['id' => $file->id]);
});
