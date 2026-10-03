<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/UI/Breadcrumb.vue';
import Button from '@/Components/UI/Button.vue';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import FileEditModal from '@/Components/File/FileEditModal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import {
    FileText,
    Image as ImageIcon,
    FileCode,
    FileSpreadsheet,
    File as GenericFileIcon,
    Download,
    ExternalLink,
    Edit2,
    Trash2,
    Folder as FolderIcon,
    Building2,
    User as UserIcon,
    Calendar,
    HardDrive,
    Eye,
    Share2,
    Check,
} from 'lucide-vue-next';

const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
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

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const isDeleting = ref(false);
const copied = ref(false);

const isPdf = computed(() => {
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ext === 'pdf' || mime === 'application/pdf';
});

const isImage = computed(() => {
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/');
});

const isText = computed(() => {
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ['txt', 'csv', 'json', 'md', 'log', 'xml'].includes(ext) || mime.startsWith('text/');
});

const fileIcon = computed(() => {
    if (isPdf.value) return FileText;
    if (isImage.value) return ImageIcon;
    if (['xls', 'xlsx', 'csv'].includes(props.file.extension)) return FileSpreadsheet;
    if (isText.value) return FileCode;
    return GenericFileIcon;
});

const formattedDate = computed(() => {
    if (!props.file.created_at) return 'N/A';
    return new Date(props.file.created_at).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2500);
};

const executeDelete = () => {
    isDeleting.value = true;
    router.delete(route('files.destroy', props.file.id), {
        onSuccess: () => {
            if (props.file.folder_id) {
                router.visit(route('folders.show', props.file.folder_id));
            } else {
                router.visit(route('files.index'));
            }
        },
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
    });
};
</script>

<template>
    <Head :title="file.title + ' - Document Details'" />

    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="mb-5">
            <Breadcrumb :items="breadcrumbs" />
        </div>

        <!-- Top Header Card -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-xs mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- File Icon & Title -->
                <div class="flex items-start gap-4">
                    <div
                        class="p-4 rounded-2xl shrink-0"
                        :class="isPdf ? 'bg-red-50 text-red-600 dark:bg-red-950/60' : (isImage ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/60')"
                    >
                        <component :is="fileIcon" class="w-8 h-8" />
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                {{ file.title }}
                            </h1>
                            <Badge :variant="file.department?.color || 'blue'" size="sm" dot>
                                {{ file.department?.name }}
                            </Badge>
                        </div>
                        <p class="text-sm font-mono text-gray-500 dark:text-gray-400">
                            {{ file.file_name }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <button
                        type="button"
                        @click="copyLink"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
                        title="Copy Share Link"
                    >
                        <Check v-if="copied" class="w-4 h-4 text-emerald-500" />
                        <Share2 v-else class="w-4 h-4" />
                    </button>

                    <a
                        :href="route('files.preview', file.id)"
                        target="_blank"
                        class="inline-flex items-center"
                    >
                        <Button variant="secondary" size="sm">
                            <template #icon>
                                <ExternalLink class="w-4 h-4" />
                            </template>
                            Open in Tab
                        </Button>
                    </a>

                    <a
                        :href="route('files.download', file.id)"
                        download
                        class="inline-flex items-center"
                    >
                        <Button variant="primary" size="sm">
                            <template #icon>
                                <Download class="w-4 h-4" />
                            </template>
                            Download
                        </Button>
                    </a>

                    <template v-if="isAdmin">
                        <Button variant="secondary" size="sm" @click="showEditModal = true">
                            <template #icon>
                                <Edit2 class="w-4 h-4 text-amber-500" />
                            </template>
                            Edit
                        </Button>

                        <Button variant="danger" size="sm" @click="showDeleteModal = true">
                            <template #icon>
                                <Trash2 class="w-4 h-4" />
                            </template>
                            Delete
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <!-- 2 Columns: Preview Window (Left) & Document Metadata Details (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Document Preview Window -->
            <div class="lg:col-span-2 space-y-6">
                <Card padding="none">
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <Eye class="w-4 h-4 text-blue-500" />
                                <span>Document Preview</span>
                            </h3>
                            <span class="text-xs text-gray-400 uppercase font-mono">
                                {{ file.extension?.toUpperCase() }} ({{ file.mime_type }})
                            </span>
                        </div>
                    </template>

                    <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 min-h-[450px] flex items-center justify-center">
                        <!-- PDF -->
                        <iframe
                            v-if="isPdf"
                            :src="route('files.preview', file.id)"
                            class="w-full h-[650px] rounded-xl border border-gray-200 dark:border-gray-800"
                            title="PDF Preview"
                        ></iframe>

                        <!-- Image -->
                        <div v-else-if="isImage" class="p-2 text-center max-h-[600px] overflow-auto">
                            <img
                                :src="route('files.preview', file.id)"
                                :alt="file.title"
                                class="max-h-[580px] max-w-full mx-auto object-contain rounded-xl shadow-xs"
                            />
                        </div>

                        <!-- Text / CSV -->
                        <iframe
                            v-else-if="isText"
                            :src="route('files.preview', file.id)"
                            class="w-full h-[500px] bg-gray-900 rounded-xl border border-gray-800 text-gray-100 p-3"
                            title="Text Preview"
                        ></iframe>

                        <!-- Fallback -->
                        <div v-else class="text-center py-16 space-y-4">
                            <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <GenericFileIcon class="w-8 h-8" />
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                    No Direct Inline Preview Available
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto mt-1">
                                    This file format requires dedicated desktop software. Please download the document to open it.
                                </p>
                            </div>
                            <a :href="route('files.download', file.id)" download>
                                <Button variant="primary" size="sm">
                                    <template #icon>
                                        <Download class="w-4 h-4" />
                                    </template>
                                    Download to View
                                </Button>
                            </a>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Right: Document Metadata & Details (Section 3.4 Requirements) -->
            <div class="space-y-6">
                <Card>
                    <template #header>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Document Metadata
                        </h3>
                    </template>

                    <div class="divide-y divide-gray-100 dark:divide-gray-750 text-sm">
                        <!-- Title -->
                        <div class="py-3 flex flex-col">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Title</span>
                            <span class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ file.title }}</span>
                        </div>

                        <!-- Nama File -->
                        <div class="py-3 flex flex-col">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">File Name</span>
                            <span class="font-mono text-xs text-gray-800 dark:text-gray-200 mt-0.5 break-all">{{ file.file_name }}</span>
                        </div>

                        <!-- Folder -->
                        <div class="py-3 flex flex-col">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Folder</span>
                            <div class="mt-0.5">
                                <Link
                                    v-if="file.folder"
                                    :href="route('folders.show', file.folder.id)"
                                    class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium text-xs"
                                >
                                    <FolderIcon class="w-3.5 h-3.5 text-amber-500" />
                                    <span>{{ file.folder.name }}</span>
                                </Link>
                                <span v-else class="text-xs text-gray-500 italic">Root Folder</span>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="py-3 flex flex-col">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Department</span>
                            <div class="mt-1">
                                <Badge :variant="file.department?.color || 'blue'" size="sm" dot>
                                    {{ file.department?.name }} ({{ file.department?.code }})
                                </Badge>
                            </div>
                        </div>

                        <!-- Uploaded By -->
                        <div class="py-3 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                                <UserIcon class="w-3.5 h-3.5" /> Uploaded By
                            </span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-xs">
                                {{ file.user?.name || 'Administrator' }}
                            </span>
                        </div>

                        <!-- Upload Date -->
                        <div class="py-3 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                                <Calendar class="w-3.5 h-3.5" /> Upload Date
                            </span>
                            <span class="text-gray-800 dark:text-gray-200 text-xs">
                                {{ formattedDate }}
                            </span>
                        </div>

                        <!-- File Size -->
                        <div class="py-3 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                                <HardDrive class="w-3.5 h-3.5" /> File Size
                            </span>
                            <span class="font-mono text-xs text-gray-800 dark:text-gray-200">
                                {{ file.formatted_size }}
                            </span>
                        </div>

                        <!-- Total Downloads -->
                        <div class="py-3 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                                <Download class="w-3.5 h-3.5" /> Download Count
                            </span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                {{ file.download_count || 0 }} times
                            </span>
                        </div>
                    </div>
                </Card>

                <!-- Description / Notes -->
                <Card v-if="file.description">
                    <template #header>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Notes & Description
                        </h3>
                    </template>
                    <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-300 whitespace-pre-line">
                        {{ file.description }}
                    </p>
                </Card>
            </div>
        </div>

        <!-- Modals -->
        <FileEditModal
            v-if="isAdmin"
            :show="showEditModal"
            :file="file"
            :departments="departments"
            :folder-tree="folderTree"
            @close="showEditModal = false"
        />

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Document"
            :message="`Are you sure you want to delete '${file.title}'?`"
            confirm-text="Delete File"
            :loading="isDeleting"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />
    </AuthenticatedLayout>
</template>
