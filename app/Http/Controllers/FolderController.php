<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folder\StoreFolderRequest;
use App\Http\Requests\Folder\UpdateFolderRequest;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FolderController extends Controller
{
    public function __construct(
        protected FolderService $folderService
    ) {}

    /**
     * Display root folder with folders and root-level files.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorize('viewAny', Folder::class);

        $folders = Folder::whereNull('parent_id')
            ->withCount(['children', 'files'])
            ->with('user')
            ->orderBy('name')
            ->get();

        $files = File::whereNull('folder_id')
            ->with(['department', 'user'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $folderTree = $this->folderService->getFolderTree();

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'currentFolder' => null,
                'breadcrumbs' => [],
                'folders' => $folders,
                'files' => $files,
                'folderTree' => $folderTree,
            ]);
        }

        return Inertia::render('Folders/Index', [
            'currentFolder' => null,
            'breadcrumbs' => [
                ['id' => null, 'name' => 'Root / All Documents'],
            ],
            'folders' => $folders,
            'files' => $files,
            'departments' => $departments,
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Display a specific folder and its contents.
     */
    public function show(Request $request, Folder $folder): Response|JsonResponse
    {
        $this->authorize('view', $folder);

        $folder->load(['parent', 'user']);

        $subfolders = Folder::where('parent_id', $folder->id)
            ->withCount(['children', 'files'])
            ->with('user')
            ->orderBy('name')
            ->get();

        $files = File::where('folder_id', $folder->id)
            ->with(['department', 'user'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $folderTree = $this->folderService->getFolderTree();
        $breadcrumbs = array_merge(
            [['id' => null, 'name' => 'Root']],
            $folder->getBreadcrumbs()
        );

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'currentFolder' => $folder,
                'breadcrumbs' => $breadcrumbs,
                'folders' => $subfolders,
                'files' => $files,
                'folderTree' => $folderTree,
            ]);
        }

        return Inertia::render('Folders/Index', [
            'currentFolder' => $folder,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $subfolders,
            'files' => $files,
            'departments' => $departments,
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Store a newly created folder.
     */
    public function store(StoreFolderRequest $request): RedirectResponse|JsonResponse
    {
        $folder = $this->folderService->createFolder($request->validated(), $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Folder created successfully.',
                'folder' => $folder,
            ], 201);
        }

        return back()->with('success', "Folder '{$folder->name}' created successfully.");
    }

    /**
     * Update the specified folder.
     */
    public function update(UpdateFolderRequest $request, Folder $folder): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $folder);

        $updated = $this->folderService->updateFolder($folder, $request->validated(), $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Folder updated successfully.',
                'folder' => $updated,
            ]);
        }

        return back()->with('success', "Folder '{$updated->name}' updated successfully.");
    }

    /**
     * Remove the specified folder.
     */
    public function destroy(Request $request, Folder $folder): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $folder);

        $parentFolderId = $folder->parent_id;
        $name = $folder->name;

        $this->folderService->deleteFolder($folder, $request->user());

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Folder deleted successfully.',
            ]);
        }

        if ($parentFolderId) {
            return redirect()->route('folders.show', $parentFolderId)->with('success', "Folder '{$name}' deleted successfully.");
        }

        return redirect()->route('folders.index')->with('success', "Folder '{$name}' deleted successfully.");
    }
}
