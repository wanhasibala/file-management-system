<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FolderService
{
    /**
     * Get root folders with nested children counts and files counts.
     */
    public function getRootFolders()
    {
        return Folder::whereNull('parent_id')
            ->withCount(['children', 'files'])
            ->with(['children' => function ($query) {
                $query->withCount(['children', 'files']);
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get folder tree structure for selectors and navigations.
     */
    public function getFolderTree(?int $excludeId = null): array
    {
        $query = Folder::whereNull('parent_id')->with(['children' => function ($q) {
            $q->orderBy('name');
        }])->orderBy('name');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $folders = $query->get();

        return $this->formatTree($folders, $excludeId);
    }

    private function formatTree(Collection $folders, ?int $excludeId = null, string $prefix = ''): array
    {
        $result = [];

        foreach ($folders as $folder) {
            if ($excludeId && $folder->id === $excludeId) {
                continue;
            }

            $result[] = [
                'id' => $folder->id,
                'name' => $folder->name,
                'label' => $prefix ? "{$prefix} / {$folder->name}" : $folder->name,
                'parent_id' => $folder->parent_id,
            ];

            if ($folder->children->isNotEmpty()) {
                $subPrefix = $prefix ? "{$prefix} / {$folder->name}" : $folder->name;
                $result = array_merge($result, $this->formatTree($folder->children, $excludeId, $subPrefix));
            }
        }

        return $result;
    }

    /**
     * Create a new folder.
     */
    public function createFolder(array $data, User $user): Folder
    {
        return DB::transaction(function () use ($data, $user) {
            $folder = Folder::create([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
                'user_id' => $user->id,
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? 'amber',
            ]);

            ActivityLog::record(
                action: 'created',
                entityType: 'Folder',
                entityId: $folder->id,
                description: "Created folder '{$folder->name}'",
                properties: [
                    'name' => $folder->name,
                    'parent_id' => $folder->parent_id,
                ]
            );

            return $folder;
        });
    }

    /**
     * Update an existing folder.
     */
    public function updateFolder(Folder $folder, array $data, User $user): Folder
    {
        return DB::transaction(function () use ($folder, $data, $user) {
            $oldName = $folder->name;

            $folder->update([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? $folder->color,
            ]);

            ActivityLog::record(
                action: 'updated',
                entityType: 'Folder',
                entityId: $folder->id,
                description: "Renamed/Updated folder '{$oldName}' to '{$folder->name}'",
                properties: [
                    'old_name' => $oldName,
                    'new_name' => $folder->name,
                    'parent_id' => $folder->parent_id,
                ]
            );

            return $folder;
        });
    }

    /**
     * Delete a folder and handle its children and files.
     */
    public function deleteFolder(Folder $folder, User $user): bool
    {
        return DB::transaction(function () use ($folder, $user) {
            $folderName = $folder->name;
            $folderId = $folder->id;

            // Soft delete descendant folders and files
            $descendantIds = $folder->getAllDescendantIds();
            if (! empty($descendantIds)) {
                Folder::whereIn('id', $descendantIds)->delete();
            }

            $deleted = $folder->delete();

            ActivityLog::record(
                action: 'deleted',
                entityType: 'Folder',
                entityId: $folderId,
                description: "Deleted folder '{$folderName}'",
                properties: [
                    'deleted_name' => $folderName,
                    'descendant_folders_count' => count($descendantIds),
                ]
            );

            return $deleted;
        });
    }
}
