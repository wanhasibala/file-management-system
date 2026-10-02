<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    /**
     * Get all departments with file counts.
     */
    public function getAllWithCounts(): Collection
    {
        return Department::withCount('files')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create department.
     */
    public function createDepartment(array $data, User $user): Department
    {
        return DB::transaction(function () use ($data) {
            $department = Department::create([
                'name' => $data['name'],
                'code' => strtoupper($data['code']),
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? 'blue',
            ]);

            ActivityLog::record(
                action: 'created',
                entityType: 'Department',
                entityId: $department->id,
                description: "Created department '{$department->name}' ({$department->code})",
                properties: [
                    'name' => $department->name,
                    'code' => $department->code,
                ]
            );

            return $department;
        });
    }

    /**
     * Update department.
     */
    public function updateDepartment(Department $department, array $data, User $user): Department
    {
        return DB::transaction(function () use ($department, $data) {
            $oldName = $department->name;

            $department->update([
                'name' => $data['name'],
                'code' => strtoupper($data['code']),
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? $department->color,
            ]);

            ActivityLog::record(
                action: 'updated',
                entityType: 'Department',
                entityId: $department->id,
                description: "Updated department '{$oldName}' to '{$department->name}'",
                properties: [
                    'name' => $department->name,
                    'code' => $department->code,
                ]
            );

            return $department;
        });
    }

    /**
     * Delete department.
     */
    public function deleteDepartment(Department $department, User $user): bool
    {
        return DB::transaction(function () use ($department) {
            $name = $department->name;
            $id = $department->id;

            $deleted = $department->delete();

            ActivityLog::record(
                action: 'deleted',
                entityType: 'Department',
                entityId: $id,
                description: "Deleted department '{$name}'"
            );

            return $deleted;
        });
    }
}
