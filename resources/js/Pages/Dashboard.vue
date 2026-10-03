<script setup>
import { ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import Card from '@/Components/UI/Card.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import FilePreviewModal from '@/Components/UI/FilePreviewModal.vue';
import FileUploadModal from '@/Components/File/FileUploadModal.vue';
import FolderFormModal from '@/Components/Folder/FolderFormModal.vue';
import {
    Folder,
    Files,
    Building2,
    HardDrive,
    UploadCloud,
    FolderPlus,
    FileText,
    Image as ImageIcon,
    FileCode,
    FileSpreadsheet,
    File as GenericFileIcon,
    Download,
    Eye,
    ChevronRight,
    ArrowUpRight,
    Activity,
    Clock,
    User as UserIcon,
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    latestFiles: {
        type: Array,
        default: () => [],
    },
    departmentBreakdown: {
        type: Array,
        default: () => [],
    },
    recentActivities: {
        type: Array,
        default: () => [],
    },
    topDownloaded: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isAdmin = page.props.auth.user?.is_admin;

// Modals
const showUploadModal = ref(false);
const showFolderModal = ref(false);
const showPreviewModal = ref(false);
const selectedPreviewFile = ref(null);

const openPreview = (file) => {
    selectedPreviewFile.value = file;
    showPreviewModal.value = true;
};

const getFileIcon = (ext, mime) => {
    ext = (ext || '').toLowerCase();
    mime = (mime || '').toLowerCase();
    if (ext === 'pdf' || mime === 'application/pdf') return FileText;
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/')) return ImageIcon;
    if (['xls', 'xlsx', 'csv'].includes(ext)) return FileSpreadsheet;
    if (['txt', 'json', 'md', 'html', 'xml'].includes(ext) || mime.startsWith('text/')) return FileCode;
    return GenericFileIcon;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const formatTimeAgo = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const diffSec = Math.floor((now - date) / 1000);

    if (diffSec < 60) return 'Just now';
    if (diffSec < 3600) return `${Math.floor(diffSec / 60)}m ago`;
    if (diffSec < 86400) return `${Math.floor(diffSec / 3600)}h ago`;
    return `${Math.floor(diffSec / 86400)}d ago`;
};
</script>

<template>
    <Head title="Dashboard - File Management System" />

    <AuthenticatedLayout>
        <!-- Page Greeting & Quick Action Bar -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Document Center Dashboard
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Welcome back, <span class="font-medium text-gray-800 dark:text-gray-200">{{ $page.props.auth.user.name }}</span>! Manage and monitor enterprise documents and archives.
                </p>
            </div>

            <!-- Quick Action Buttons for Administrator -->
            <div v-if="isAdmin" class="flex items-center gap-2.5">
                <Button variant="secondary" @click="showFolderModal = true">
                    <template #icon>
                        <FolderPlus class="w-4 h-4 text-amber-500" />
                    </template>
                    New Folder
                </Button>
                <Button variant="primary" @click="showUploadModal = true">
                    <template #icon>
                        <UploadCloud class="w-4 h-4" />
                    </template>
                    Upload Document
                </Button>
            </div>
        </div>

        <!-- 4 Top Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <StatCard
                title="Total Documents"
                :value="stats.total_files"
                subtitle="Stored in system"
                color="blue"
            >
                <template #icon>
                    <Files class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Total Folders"
                :value="stats.total_folders"
                subtitle="Hierarchical structure"
                color="amber"
            >
                <template #icon>
                    <Folder class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Departments"
                :value="stats.total_departments"
                subtitle="Metadata categories"
                color="purple"
            >
                <template #icon>
                    <Building2 class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Total Storage"
                :value="stats.formatted_storage"
                subtitle="Cloud storage utilized"
                color="emerald"
            >
                <template #icon>
                    <HardDrive class="w-6 h-6" />
                </template>
            </StatCard>
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: 10 Latest Files -->
            <div class="lg:col-span-2 space-y-6">
                <Card padding="none">
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    10 Latest Files
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Recently added documents across all departments
                                </p>
                            </div>
                            <Link
                                :href="route('files.index')"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400"
                            >
                                <span>View all</span>
                                <ChevronRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </template>

                    <div v-if="latestFiles && latestFiles.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/70 dark:bg-gray-800/80 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Document</th>
                                    <th scope="col" class="px-4 py-3">Department</th>
                                    <th scope="col" class="px-4 py-3">Size</th>
                                    <th scope="col" class="px-4 py-3">Date</th>
                                    <th scope="col" class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr
                                    v-for="file in latestFiles"
                                    :key="file.id"
                                    class="hover:bg-gray-50/80 dark:hover:bg-gray-750/50 transition-colors"
                                >
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <component
                                                :is="getFileIcon(file.extension, file.mime_type)"
                                                class="w-5 h-5 text-blue-500 shrink-0"
                                            />
                                            <div class="max-w-[200px] sm:max-w-xs truncate">
                                                <Link
                                                    :href="route('files.show', file.id)"
                                                    class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block text-xs sm:text-sm"
                                                >
                                                    {{ file.title }}
                                                </Link>
                                                <span class="text-[11px] text-gray-400 truncate block">
                                                    {{ file.file_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <Badge :variant="file.department?.color || 'blue'" size="xs" dot>
                                            {{ file.department?.name || 'N/A' }}
                                        </Badge>
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ file.formatted_size }}
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(file.created_at) }}
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                @click="openPreview(file)"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                                title="Preview Document"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <a
                                                :href="route('files.download', file.id)"
                                                download
                                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                                title="Download File"
                                            >
                                                <Download class="w-4 h-4" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-8">
                        <EmptyState
                            title="No documents uploaded yet"
                            description="Upload your first corporate document to start organizing files."
                            action-text="Upload Document"
                            @action="showUploadModal = true"
                        />
                    </div>
                </Card>
            </div>

            <!-- Right Col: Department Distribution & Audit Activity -->
            <div class="space-y-6">
                <!-- Department Distribution -->
                <Card>
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                Department Breakdown
                            </h3>
                            <Link
                                :href="route('departments.index')"
                                class="text-xs text-blue-600 hover:underline dark:text-blue-400"
                            >
                                Manage
                            </Link>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <div
                            v-for="dept in departmentBreakdown"
                            :key="dept.id"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/60"
                        >
                            <div class="flex items-center gap-2.5">
                                <Badge :variant="dept.color || 'blue'" size="xs" dot>
                                    {{ dept.code }}
                                </Badge>
                                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">
                                    {{ dept.name }}
                                </span>
                            </div>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                                {{ dept.files_count }} files
                            </span>
                        </div>
                    </div>
                </Card>

                <!-- Recent Activities (Audit Log) -->
                <Card>
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                <Activity class="w-4 h-4 text-blue-500" />
                                <span>Recent Activities</span>
                            </h3>
                            <Link
                                v-if="isAdmin"
                                :href="route('activity-logs.index')"
                                class="text-xs text-blue-600 hover:underline dark:text-blue-400"
                            >
                                All logs
                            </Link>
                        </div>
                    </template>

                    <div v-if="recentActivities && recentActivities.length > 0" class="space-y-3.5">
                        <div
                            v-for="log in recentActivities"
                            :key="log.id"
                            class="flex items-start gap-3 text-xs"
                        >
                            <div class="mt-0.5 p-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                                <Clock class="w-3.5 h-3.5" />
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-gray-800 dark:text-gray-200 leading-snug">
                                    <span class="font-semibold">{{ log.user?.name || 'System' }}</span>
                                    {{ log.description }}
                                </p>
                                <span class="text-[10px] text-gray-400 mt-0.5 block">
                                    {{ formatTimeAgo(log.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <p v-else class="text-xs text-gray-400 text-center py-4">
                        No recent activity recorded.
                    </p>
                </Card>
            </div>
        </div>

        <!-- Modals -->
        <FilePreviewModal
            :show="showPreviewModal"
            :file="selectedPreviewFile"
            @close="showPreviewModal = false"
        />

        <FileUploadModal
            v-if="isAdmin"
            :show="showUploadModal"
            :departments="departmentBreakdown"
            @close="showUploadModal = false"
        />

        <FolderFormModal
            v-if="isAdmin"
            :show="showFolderModal"
            @close="showFolderModal = false"
        />
    </AuthenticatedLayout>
</template>
