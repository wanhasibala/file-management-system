<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorize('viewAny', Department::class);

        $departments = $this->departmentService->getAllWithCounts();

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'departments' => $departments,
            ]);
        }

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse|JsonResponse
    {
        $department = $this->departmentService->createDepartment($request->validated(), $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Department created successfully.',
                'department' => $department,
            ], 201);
        }

        return back()->with('success', "Department '{$department->name}' created successfully.");
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $department);

        $updated = $this->departmentService->updateDepartment($department, $request->validated(), $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Department updated successfully.',
                'department' => $updated,
            ]);
        }

        return back()->with('success', "Department '{$updated->name}' updated successfully.");
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Request $request, Department $department): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $department);

        $name = $department->name;
        $this->departmentService->deleteDepartment($department, $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Department deleted successfully.',
            ]);
        }

        return back()->with('success', "Department '{$name}' deleted successfully.");
    }
}
