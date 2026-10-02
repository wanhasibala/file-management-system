<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Services\FileService;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
        protected FolderService $folderService
    ) {}

    /**
     * Display a listing of files with search and filter capabilities.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorize('viewAny', File::class);

        $filters = $request->only(['search', 'department_id', 'type', 'sort_by', 'sort_order']);
        $files = $this->fileService->searchAndFilter($filters, 15);
        $departments = Department::orderBy('name')->get();
        $folderTree = $this->folderService->getFolderTree();

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'files' => $files,
                'filters' => $filters,
                'departments' => $departments,
            ]);
        }

        return Inertia::render('Files/Index', [
            'files' => $files,
            'filters' => $filters,
            'departments' => $departments,
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Display the specified file details.
     */
    public function show(Request $request, File $file): Response|JsonResponse
    {
        $this->authorize('view', $file);

        $file->load(['department', 'folder', 'user']);

        $breadcrumbs = [];
        if ($file->folder) {
            $breadcrumbs = array_merge(
                [['id' => null, 'name' => 'Root']],
                $file->folder->getBreadcrumbs()
            );
        } else {
            $breadcrumbs = [['id' => null, 'name' => 'Root Documents']];
        }

        $departments = Department::orderBy('name')->get();
        $folderTree = $this->folderService->getFolderTree();

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'file' => $file,
                'breadcrumbs' => $breadcrumbs,
            ]);
        }

        return Inertia::render('Files/Show', [
            'file' => $file,
            'breadcrumbs' => $breadcrumbs,
            'departments' => $departments,
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Store a newly created/uploaded file.
     */
    public function store(StoreFileRequest $request): RedirectResponse|JsonResponse
    {
        $file = $this->fileService->uploadFile(
            $request->file('file'),
            $request->validated(),
            $request->user()
        );

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'File uploaded successfully.',
                'file' => $file,
            ], 201);
        }

        return back()->with('success', "File '{$file->title}' uploaded successfully.");
    }

    /**
     * Update the specified file's metadata or binary.
     */
    public function update(UpdateFileRequest $request, File $file): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $file);

        $updated = $this->fileService->updateFile(
            $file,
            $request->validated(),
            $request->file('file'),
            $request->user()
        );

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'File updated successfully.',
                'file' => $updated,
            ]);
        }

        return back()->with('success', "File '{$updated->title}' updated successfully.");
    }

    /**
     * Remove the specified file.
     */
    public function destroy(Request $request, File $file): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $file);

        $title = $file->title;
        $folderId = $file->folder_id;

        $this->fileService->deleteFile($file, $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'File deleted successfully.',
            ]);
        }

        return back()->with('success', "File '{$title}' deleted successfully.");
    }

    /**
     * Download the file.
     */
    public function download(Request $request, File $file): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('download', $file);

        return $this->fileService->downloadFile($file, $request->user());
    }

    /**
     * Stream file for inline preview.
     */
    public function preview(Request $request, File $file): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('view', $file);

        return $this->fileService->previewFile($file);
    }
}
