<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/UI/Breadcrumb.vue';
import Button from '@/Components/UI/Button.vue';
import Card from '@/Components/UI/Card.vue';
import FolderCard from '@/Components/UI/FolderCard.vue';
import FileCard from '@/Components/UI/FileCard.vue';
import FileRow from '@/Components/UI/FileRow.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import SearchBar from '@/Components/UI/SearchBar.vue';
import FolderFormModal from '@/Components/Folder/FolderFormModal.vue';
import FileUploadModal from '@/Components/File/FileUploadModal.vue';
import FileEditModal from '@/Components/File/FileEditModal.vue';
import FilePreviewModal from '@/Components/UI/FilePreviewModal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import {
    Folder as FolderIcon,
    FolderPlus,
    UploadCloud,
    LayoutGrid,
    List,
    Files,
    Trash2,
    Edit2,
    ChevronLeft,
} from 'lucide-vue-next';

const props = defineProps({
    currentFolder: {
        type: Object,
        default: null,
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    folders: {
        type: Array,
        default: () => [],
    },
    files: {
        type: Object,
        required: true,
    },
    departments: {
        type: Array,
        default: () => [],
    },
    folderTree: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isAdmin = page.props.auth.user?.is_admin;

// View Mode: grid or table
const viewMode = ref('grid');
const searchQuery = ref('');

// Modals State
const showFolderModal = ref(false);
const editingFolder = ref(null);

const showUploadModal = ref(false);
const showEditFileModal = ref(false);
const editingFile = ref(null);

const showPreviewModal = ref(false);
const selectedPreviewFile = ref(null);

const showDeleteFolderModal = ref(false);
const folderToDelete = ref(null);

const showDeleteFileModal = ref(false);
const fileToDelete = ref(null);
const isDeleting = ref(false);

// Filter folders & files locally by quick search if provided
const filteredFolders = computed(() => {
    if (!searchQuery.value) return props.folders;
    const q = searchQuery.value.toLowerCase();
    return props.folders.filter(f => f.name.toLowerCase().includes(q));
});

const filteredFiles = computed(() => {
    if (!searchQuery.value) return props.files.data || [];
    const q = searchQuery.value.toLowerCase();
    return (props.files.data || []).filter(f =>
        f.title.toLowerCase().includes(q) ||
        f.file_name.toLowerCase().includes(q) ||
        (f.department?.name || '').toLowerCase().includes(q)
    );
});

// Folder Actions
const openCreateFolder = () => {
    editingFolder.value = null;
    showFolderModal.value = true;
};

const openEditFolder = (folder) => {
    editingFolder.value = folder;
    showFolderModal.value = true;
};

const confirmDeleteFolder = (folder) => {
    folderToDelete.value = folder;
    showDeleteFolderModal.value = true;
};

const executeDeleteFolder = () => {
    if (!folderToDelete.value) return;
    isDeleting.value = true;
    router.delete(route('folders.destroy', folderToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteFolderModal.value = false;
            folderToDelete.value = null;
        },
    });
};

// File Actions
const openUpload = () => {
    showUploadModal.value = true;
};

const openPreview = (file) => {
    selectedPreviewFile.value = file;
    showPreviewModal.value = true;
};

const openEditFile = (file) => {
    editingFile.value = file;
    showEditFileModal.value = true;
};

const confirmDeleteFile = (file) => {
    fileToDelete.value = file;
    showDeleteFileModal.value = true;
};

const executeDeleteFile = () => {
    if (!fileToDelete.value) return;
    isDeleting.value = true;
    router.delete(route('files.destroy', fileToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteFileModal.value = false;
            fileToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head :title="(currentFolder ? currentFolder.name : 'All Documents') + ' - File Management System'" />

    <AuthenticatedLayout>
        <!-- Breadcrumb & Top Bar -->
        <div class="mb-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <Breadcrumb :items="breadcrumbs" />

                <!-- Search inside current folder -->
                <div class="w-full sm:w-72">
                    <SearchBar
                        v-model="searchQuery"
                        placeholder="Filter current folder..."
                    />
                </div>
            </div>

            <!-- Folder Title & Action Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 shrink-0">
                        <FolderIcon class="w-7 h-7 fill-amber-500/20 text-amber-500" />
                    </div>

                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ currentFolder ? currentFolder.name : 'Root Directory' }}
                            </h1>
                            <button
                                v-if="isAdmin && currentFolder"
                                type="button"
                                @click="openEditFolder(currentFolder)"
                                class="p-1 text-gray-400 hover:text-blue-600 rounded-md transition-colors"
                                title="Rename Folder"
                            >
                                <Edit2 class="w-4 h-4" />
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ currentFolder?.description || (currentFolder ? 'Folder contents and subfolders' : 'Top-level enterprise folder repository') }}
                        </p>
                    </div>
                </div>

                <!-- Admin Action Buttons & View Mode Toggle -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Grid / Table view toggle -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700/80 p-1 rounded-xl border border-gray-200 dark:border-gray-600">
                        <button
                            type="button"
                            @click="viewMode = 'grid'"
                            class="p-1.5 rounded-lg transition-colors"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
                            title="Grid View"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            @click="viewMode = 'table'"
                            class="p-1.5 rounded-lg transition-colors"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
                            title="Table View"
                        >
                            <List class="w-4 h-4" />
                        </button>
                    </div>

                    <template v-if="isAdmin">
                        <Button variant="secondary" size="sm" @click="openCreateFolder">
                            <template #icon>
                                <FolderPlus class="w-4 h-4 text-amber-500" />
                            </template>
                            New Subfolder
                        </Button>

                        <Button variant="primary" size="sm" @click="openUpload">
                            <template #icon>
                                <UploadCloud class="w-4 h-4" />
                            </template>
                            Upload File
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Section 1: Subfolders Grid -->
        <div v-if="filteredFolders.length > 0" class="mb-8 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Folders ({{ filteredFolders.length }})
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <FolderCard
                    v-for="folder in filteredFolders"
                    :key="folder.id"
                    :folder="folder"
                    :is-admin="isAdmin"
                    @edit="openEditFolder"
                    @delete="confirmDeleteFolder"
                />
            </div>
        </div>

        <!-- Section 2: Files in Current Folder -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Files & Documents ({{ files.total || filteredFiles.length }})
                </h2>
            </div>

            <!-- If files exist -->
            <div v-if="filteredFiles.length > 0">
                <!-- Grid View -->
                <div
                    v-if="viewMode === 'grid'"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                >
                    <FileCard
                        v-for="file in filteredFiles"
                        :key="file.id"
                        :file="file"
                        :is-admin="isAdmin"
                        @preview="openPreview"
                        @edit="openEditFile"
                        @delete="confirmDeleteFile"
                    />
                </div>

                <!-- Table View -->
                <Card v-else padding="none">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/70 dark:bg-gray-800/80 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Document</th>
                                    <th scope="col" class="px-4 py-3">Department</th>
                                    <th scope="col" class="px-4 py-3">Folder</th>
                                    <th scope="col" class="px-4 py-3">Size</th>
                                    <th scope="col" class="px-4 py-3">Date</th>
                                    <th scope="col" class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <FileRow
                                    v-for="file in filteredFiles"
                                    :key="file.id"
                                    :file="file"
                                    :is-admin="isAdmin"
                                    @preview="openPreview"
                                    @edit="openEditFile"
                                    @delete="confirmDeleteFile"
                                />
                            </tbody>
                        </table>
                    </div>
                </Card>

                <!-- Pagination -->
                <div class="mt-4">
                    <Pagination
                        :links="files.links"
                        :from="files.from"
                        :to="files.to"
                        :total="files.total"
                    />
                </div>
            </div>

            <!-- Empty state when no folders and no files -->
            <div v-else-if="filteredFolders.length === 0" class="py-6">
                <EmptyState
                    title="This folder is empty"
                    description="Upload documents or create subfolders to get organized."
                    :action-text="isAdmin ? 'Upload Document' : ''"
                    @action="openUpload"
                />
            </div>
        </div>

        <!-- Modals -->
        <FolderFormModal
            v-if="isAdmin"
            :show="showFolderModal"
            :folder="editingFolder"
            :parent-folder-id="currentFolder?.id || null"
            :folder-tree="folderTree"
            @close="showFolderModal = false"
        />

        <FileUploadModal
            v-if="isAdmin"
            :show="showUploadModal"
            :folder-id="currentFolder?.id || null"
            :departments="departments"
            :folder-tree="folderTree"
            @close="showUploadModal = false"
        />

        <FileEditModal
            v-if="isAdmin"
            :show="showEditFileModal"
            :file="editingFile"
            :departments="departments"
            :folder-tree="folderTree"
            @close="showEditFileModal = false"
        />

        <FilePreviewModal
            :show="showPreviewModal"
            :file="selectedPreviewFile"
            @close="showPreviewModal = false"
        />

        <ConfirmModal
            :show="showDeleteFolderModal"
            title="Delete Folder"
            :message="`Are you sure you want to delete '${folderToDelete?.name}'? All subfolders and nested files will also be removed.`"
            confirm-text="Delete Folder"
            :loading="isDeleting"
            @close="showDeleteFolderModal = false"
            @confirm="executeDeleteFolder"
        />

        <ConfirmModal
            :show="showDeleteFileModal"
            title="Delete Document"
            :message="`Are you sure you want to delete '${fileToDelete?.title}' (${fileToDelete?.file_name})?`"
            confirm-text="Delete File"
            :loading="isDeleting"
            @close="showDeleteFileModal = false"
            @confirm="executeDeleteFile"
        />
    </AuthenticatedLayout>
</template>
