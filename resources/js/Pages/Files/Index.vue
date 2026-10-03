<script setup>
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/UI/Button.vue';
import Card from '@/Components/UI/Card.vue';
import FileCard from '@/Components/UI/FileCard.vue';
import FileRow from '@/Components/UI/FileRow.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import SearchBar from '@/Components/UI/SearchBar.vue';
import SelectInput from '@/Components/UI/SelectInput.vue';
import FileUploadModal from '@/Components/File/FileUploadModal.vue';
import FileEditModal from '@/Components/File/FileEditModal.vue';
import FilePreviewModal from '@/Components/UI/FilePreviewModal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import {
    Files,
    UploadCloud,
    Filter,
    LayoutGrid,
    List,
    RotateCcw,
} from 'lucide-vue-next';

const props = defineProps({
    files: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
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

// View Mode
const viewMode = ref('table');

// Filter values
const search = ref(props.filters.search || '');
const selectedDepartment = ref(props.filters.department_id || '');
const selectedType = ref(props.filters.type || '');
const sortBy = ref(props.filters.sort_by || 'created_at');
const sortOrder = ref(props.filters.sort_order || 'desc');

// Modals
const showUploadModal = ref(false);
const showEditModal = ref(false);
const editingFile = ref(null);
const showPreviewModal = ref(false);
const selectedPreviewFile = ref(null);
const showDeleteModal = ref(false);
const fileToDelete = ref(null);
const isDeleting = ref(false);

const applyFilters = () => {
    router.get(
        route('files.index'),
        {
            search: search.value || undefined,
            department_id: selectedDepartment.value || undefined,
            type: selectedType.value || undefined,
            sort_by: sortBy.value || undefined,
            sort_order: sortOrder.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const resetFilters = () => {
    search.value = '';
    selectedDepartment.value = '';
    selectedType.value = '';
    sortBy.value = 'created_at';
    sortOrder.value = 'desc';
    applyFilters();
};

const openPreview = (file) => {
    selectedPreviewFile.value = file;
    showPreviewModal.value = true;
};

const openEdit = (file) => {
    editingFile.value = file;
    showEditModal.value = true;
};

const confirmDelete = (file) => {
    fileToDelete.value = file;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!fileToDelete.value) return;
    isDeleting.value = true;
    router.delete(route('files.destroy', fileToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            fileToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Document Explorer & Search - File Management System" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <Files class="w-6 h-6 text-blue-600" />
                    <span>Document Explorer & Search</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Search and filter all corporate documents by name, title, department, or file type.
                </p>
            </div>

            <div v-if="isAdmin" class="flex items-center gap-2">
                <Button variant="primary" @click="showUploadModal = true">
                    <template #icon>
                        <UploadCloud class="w-4 h-4" />
                    </template>
                    Upload Document
                </Button>
            </div>
        </div>

        <!-- Filter & Search Controls Bar -->
        <Card class="mb-6" padding="sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <SearchBar
                        v-model="search"
                        placeholder="Search by file name, title, notes..."
                        @search="applyFilters"
                    />
                </div>

                <!-- Department Filter -->
                <div>
                    <SelectInput
                        v-model="selectedDepartment"
                        @update:model-value="applyFilters"
                    >
                        <option value="">All Departments</option>
                        <option
                            v-for="dept in departments"
                            :key="dept.id"
                            :value="dept.id"
                        >
                            {{ dept.name }}
                        </option>
                    </SelectInput>
                </div>

                <!-- Type Filter -->
                <div>
                    <SelectInput
                        v-model="selectedType"
                        @update:model-value="applyFilters"
                    >
                        <option value="">All File Types</option>
                        <option value="pdf">PDF Documents</option>
                        <option value="image">Images</option>
                        <option value="spreadsheet">Spreadsheets (CSV, XLS)</option>
                        <option value="document">Text & Docs</option>
                    </SelectInput>
                </div>

                <!-- Sort & Reset -->
                <div class="flex items-center gap-2">
                    <SelectInput
                        v-model="sortBy"
                        @update:model-value="applyFilters"
                        class="flex-1"
                    >
                        <option value="created_at">Latest Added</option>
                        <option value="title">Title (A-Z)</option>
                        <option value="file_size">File Size</option>
                        <option value="download_count">Most Downloaded</option>
                    </SelectInput>

                    <Button
                        variant="secondary"
                        size="md"
                        @click="resetFilters"
                        title="Reset Filters"
                        class="shrink-0"
                    >
                        <RotateCcw class="w-4 h-4 text-gray-500" />
                    </Button>
                </div>
            </div>
        </Card>

        <!-- View Controls & Counter -->
        <div class="flex items-center justify-between mb-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Found <span class="font-bold text-gray-900 dark:text-white">{{ files.total }}</span> document(s)
            </div>

            <!-- View Switcher -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    @click="viewMode = 'table'"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="viewMode === 'table' ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-xs' : 'text-gray-500 dark:text-gray-400'"
                    title="Table View"
                >
                    <List class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    @click="viewMode = 'grid'"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-xs' : 'text-gray-500 dark:text-gray-400'"
                    title="Grid View"
                >
                    <LayoutGrid class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Files Content Area -->
        <div v-if="files.data && files.data.length > 0">
            <!-- Table View -->
            <Card v-if="viewMode === 'table'" padding="none">
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
                                v-for="file in files.data"
                                :key="file.id"
                                :file="file"
                                :is-admin="isAdmin"
                                @preview="openPreview"
                                @edit="openEdit"
                                @delete="confirmDelete"
                            />
                        </tbody>
                    </table>
                </div>
            </Card>

            <!-- Grid View -->
            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
            >
                <FileCard
                    v-for="file in files.data"
                    :key="file.id"
                    :file="file"
                    :is-admin="isAdmin"
                    @preview="openPreview"
                    @edit="openEdit"
                    @delete="confirmDelete"
                />
            </div>

            <!-- Pagination -->
            <div class="mt-5">
                <Pagination
                    :links="files.links"
                    :from="files.from"
                    :to="files.to"
                    :total="files.total"
                />
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-8">
            <EmptyState
                title="No matching documents found"
                description="Try modifying your search criteria or resetting filters to see more results."
                action-text="Reset Filters"
                @action="resetFilters"
            />
        </div>

        <!-- Modals -->
        <FileUploadModal
            v-if="isAdmin"
            :show="showUploadModal"
            :departments="departments"
            :folder-tree="folderTree"
            @close="showUploadModal = false"
        />

        <FileEditModal
            v-if="isAdmin"
            :show="showEditModal"
            :file="editingFile"
            :departments="departments"
            :folder-tree="folderTree"
            @close="showEditModal = false"
        />

        <FilePreviewModal
            :show="showPreviewModal"
            :file="selectedPreviewFile"
            @close="showPreviewModal = false"
        />

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Document"
            :message="`Are you sure you want to delete '${fileToDelete?.title}' (${fileToDelete?.file_name})?`"
            confirm-text="Delete File"
            :loading="isDeleting"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />
    </AuthenticatedLayout>
</template>
