<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\File;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    /**
     * Search and filter files with pagination.
     */
    public function searchAndFilter(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = File::with(['department', 'folder', 'user']);

        // Search by file_name, title, description
        if (! empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(file_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        }

        // Filter by Department
        if (! empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Filter by Folder
        if (isset($filters['folder_id'])) {
            if ($filters['folder_id'] === 'root' || $filters['folder_id'] === null) {
                $query->whereNull('folder_id');
            } elseif ($filters['folder_id'] !== 'all' && $filters['folder_id'] !== '') {
                $query->where('folder_id', $filters['folder_id']);
            }
        }

        // Filter by Extension category (pdf, doc, img, spreadsheet, other)
        if (! empty($filters['type'])) {
            $type = strtolower($filters['type']);
            if ($type === 'pdf') {
                $query->where('extension', 'pdf');
            } elseif ($type === 'image') {
                $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            } elseif ($type === 'document') {
                $query->whereIn('extension', ['doc', 'docx', 'txt', 'rtf', 'odt', 'pdf']);
            } elseif ($type === 'spreadsheet') {
                $query->whereIn('extension', ['xls', 'xlsx', 'csv', 'ods']);
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, ['title', 'file_name', 'file_size', 'created_at', 'download_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Upload and store a new file.
     */
    public function uploadFile(UploadedFile $uploadedFile, array $data, User $user): File
    {
        return DB::transaction(function () use ($uploadedFile, $data, $user) {
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
            $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
            $fileSize = $uploadedFile->getSize();

            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            $path = $uploadedFile->storeAs('documents', $safeFileName, 'public');

            $file = File::create([
                'title' => $data['title'],
                'folder_id' => $data['folder_id'] ?? null,
                'department_id' => $data['department_id'],
                'user_id' => $user->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'extension' => $extension,
                'description' => $data['description'] ?? null,
                'download_count' => 0,
            ]);

            ActivityLog::record(
                action: 'uploaded',
                entityType: 'File',
                entityId: $file->id,
                description: "Uploaded document '{$file->title}' ({$file->file_name})",
                properties: [
                    'title' => $file->title,
                    'file_name' => $file->file_name,
                    'size' => $file->file_size,
                    'department_id' => $file->department_id,
                    'folder_id' => $file->folder_id,
                ]
            );

            return $file;
        });
    }

    /**
     * Update file metadata or replace file binary.
     */
    public function updateFile(File $file, array $data, ?UploadedFile $newBinary, User $user): File
    {
        return DB::transaction(function () use ($file, $data, $newBinary, $user) {
            $updates = [
                'title' => $data['title'],
                'department_id' => $data['department_id'],
                'folder_id' => $data['folder_id'] ?? null,
                'description' => $data['description'] ?? null,
            ];

            if ($newBinary) {
                // Delete old physical file if exists
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                $originalName = $newBinary->getClientOriginalName();
                $extension = strtolower($newBinary->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                $mimeType = $newBinary->getMimeType() ?: 'application/octet-stream';
                $fileSize = $newBinary->getSize();

                $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
                $path = $newBinary->storeAs('documents', $safeFileName, 'public');

                $updates['file_name'] = $originalName;
                $updates['file_path'] = $path;
                $updates['mime_type'] = $mimeType;
                $updates['file_size'] = $fileSize;
                $updates['extension'] = $extension;
            }

            $file->update($updates);

            ActivityLog::record(
                action: 'updated',
                entityType: 'File',
                entityId: $file->id,
                description: "Updated document '{$file->title}' metadata",
                properties: [
                    'title' => $file->title,
                    'department_id' => $file->department_id,
                    'folder_id' => $file->folder_id,
                ]
            );

            return $file;
        });
    }

    /**
     * Delete a file (soft delete).
     */
    public function deleteFile(File $file, User $user): bool
    {
        return DB::transaction(function () use ($file, $user) {
            $fileTitle = $file->title;
            $fileId = $file->id;

            $deleted = $file->delete();

            ActivityLog::record(
                action: 'deleted',
                entityType: 'File',
                entityId: $fileId,
                description: "Deleted document '{$fileTitle}' ({$file->file_name})",
                properties: [
                    'title' => $fileTitle,
                    'file_name' => $file->file_name,
                ]
            );

            return $deleted;
        });
    }

    /**
     * Download a file and increment download count.
     */
    public function downloadFile(File $file, ?User $user): StreamedResponse|BinaryFileResponse
    {
        $file->increment('download_count');

        if ($user) {
            ActivityLog::record(
                action: 'downloaded',
                entityType: 'File',
                entityId: $file->id,
                description: "Downloaded document '{$file->title}' ({$file->file_name})"
            );
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found on storage disk.');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Stream file inline for browser preview.
     */
    public function previewFile(File $file): StreamedResponse|BinaryFileResponse
    {
        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found on storage disk.');
        }

        return Storage::disk('public')->response($file->file_path, $file->file_name, [
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
        ]);
    }
}
