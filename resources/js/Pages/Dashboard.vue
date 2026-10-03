<script setup>
import { ref, computed } from 'vue';
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
    Sparkles,
    CheckCircle2,
    Shield,
    Layers,
    FileCheck,
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

// Modals
const showUploadModal = ref(false);
const showFolderModal = ref(false);
const showPreviewModal = ref(false);
const selectedPreviewFile = ref(null);

const openPreview = (file) => {
    selectedPreviewFile.value = file;
    showPreviewModal.value = true;
};

// Dynamic greeting based on current local hour
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat Pagi ☀️';
    if (hour < 15) return 'Selamat Siang 🌤️';
    if (hour < 18) return 'Selamat Sore 🌇';
    return 'Selamat Malam 🌙';
});

const getFileIcon = (ext, mime) => {
    ext = (ext || '').toLowerCase();
    mime = (mime || '').toLowerCase();
    if (ext === 'pdf' || mime === 'application/pdf') return FileText;
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/')) return ImageIcon;
    if (['xls', 'xlsx', 'csv'].includes(ext)) return FileSpreadsheet;
    if (['txt', 'json', 'md', 'html', 'xml'].includes(ext) || mime.startsWith('text/')) return FileCode;
    return GenericFileIcon;
};

const getFileIconColor = (ext, mime) => {
    ext = (ext || '').toLowerCase();
    mime = (mime || '').toLowerCase();
    if (ext === 'pdf' || mime === 'application/pdf') return 'text-red-500 bg-red-50 dark:bg-red-950/50';
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/')) return 'text-purple-500 bg-purple-50 dark:bg-purple-950/50';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/50';
    if (['txt', 'json', 'md', 'html', 'xml'].includes(ext) || mime.startsWith('text/')) return 'text-sky-500 bg-sky-50 dark:bg-sky-950/50';
    return 'text-amber-500 bg-amber-50 dark:bg-amber-950/50';
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatTimeAgo = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const diffSec = Math.floor((now - date) / 1000);

    if (diffSec < 60) return 'Baru saja';
    if (diffSec < 3600) return `${Math.floor(diffSec / 60)} menit lalu`;
    if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} jam lalu`;
    return `${Math.floor(diffSec / 86400)} hari lalu`;
};

// Calculate distribution totals
const totalDepartmentFiles = computed(() => {
    return props.departmentBreakdown.reduce((sum, d) => sum + (d.files_count || 0), 0) || 1;
});
</script>

<template>
    <Head title="Dashboard - Lion Group File Management System" />

    <AuthenticatedLayout>
        <!-- Hero Banner with Lion Red Branding & Quick Greetings -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-rose-600 to-amber-600 p-6 sm:p-8 text-white shadow-xl shadow-red-500/15 mb-8">
            <!-- Decorative Background Patterns -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none" />
            <div class="absolute top-0 right-1/4 w-32 h-32 bg-amber-400/20 rounded-full blur-xl pointer-events-none" />

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-white border border-white/20">
                        <Sparkles class="w-3.5 h-3.5 text-amber-300 animate-spin" style="animation-duration: 4s;" />
                        <span>Lion Group Document Management Center</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        {{ greeting }}, {{ $page.props.auth.user.name }}! 👋
                    </h1>
                    <p class="text-sm sm:text-base text-red-50/90 leading-relaxed">
                        Sistem arsip dan pengelolaan dokumen digital terpadu Lion Group. Akses seluruh berkas departemen secara aman, cepat, dan terorganisir.
                    </p>
                </div>

                <!-- Action Button in Hero -->
                <div v-if="isAdmin" class="flex flex-wrap items-center gap-3 shrink-0">
                    <button
                        type="button"
                        @click="showFolderModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-sm backdrop-blur-md border border-white/20 shadow-xs transition-all active:scale-95"
                    >
                        <FolderPlus class="w-4 h-4 text-amber-200" />
                        <span>Buat Folder</span>
                    </button>
                    <button
                        type="button"
                        @click="showUploadModal = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-red-600 hover:bg-red-50 font-bold text-sm shadow-lg shadow-black/10 transition-all active:scale-95"
                    >
                        <UploadCloud class="w-4 h-4" />
                        <span>Unggah Dokumen</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 Top Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <StatCard
                title="Total Dokumen"
                :value="stats.total_files"
                subtitle="Berkas tersimpan aman"
                color="lion"
            >
                <template #icon>
                    <Files class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Struktur Folder"
                :value="stats.total_folders"
                subtitle="Hierarki terorganisir"
                color="amber"
            >
                <template #icon>
                    <Folder class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Departemen"
                :value="stats.total_departments"
                subtitle="Kategori divisi operasional"
                color="purple"
            >
                <template #icon>
                    <Building2 class="w-6 h-6" />
                </template>
            </StatCard>

            <StatCard
                title="Kapasitas Digunakan"
                :value="stats.formatted_storage"
                subtitle="Penyimpanan cloud aktif"
                color="emerald"
            >
                <template #icon>
                    <HardDrive class="w-6 h-6" />
                </template>
            </StatCard>
        </div>

        <!-- Quick Access Navigation Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <Link
                :href="route('files.index')"
                class="group p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md hover:border-red-300 dark:hover:border-red-900 transition-all duration-200 flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <Files class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-bold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                        Jelajahi Berkas
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block">
                        Semua file & filter
                    </span>
                </div>
            </Link>

            <Link
                :href="route('folders.index')"
                class="group p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md hover:border-amber-300 dark:hover:border-amber-900 transition-all duration-200 flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <Folder class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Folder & Hierarki
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block">
                        Pohon direktori berkas
                    </span>
                </div>
            </Link>

            <Link
                :href="route('departments.index')"
                class="group p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md hover:border-purple-300 dark:hover:border-purple-900 transition-all duration-200 flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <Building2 class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                        Departemen
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block">
                        Kategori divisi & kode
                    </span>
                </div>
            </Link>

            <Link
                v-if="isAdmin"
                :href="route('activity-logs.index')"
                class="group p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md hover:border-blue-300 dark:hover:border-blue-900 transition-all duration-200 flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <Activity class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        Audit Log
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block">
                        Riwayat & keamanan
                    </span>
                </div>
            </Link>

            <div
                v-else
                class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex items-center gap-3.5"
            >
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <Shield class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-bold text-gray-900 dark:text-white">
                        Akses Viewer
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block">
                        Hak baca & unduh aktif
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: 10 Latest Files -->
            <div class="lg:col-span-2 space-y-6">
                <Card padding="none">
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <FileCheck class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span>10 Dokumen Terbaru</span>
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Dokumen yang baru saja diunggah dan diperbarui di seluruh departemen
                                </p>
                            </div>
                            <Link
                                :href="route('files.index')"
                                class="inline-flex items-center gap-1 text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 transition-colors"
                            >
                                <span>Lihat Semua</span>
                                <ChevronRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </template>

                    <div v-if="latestFiles && latestFiles.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/80 dark:bg-gray-800/80 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-5 py-3.5">Dokumen</th>
                                    <th scope="col" class="px-4 py-3.5">Departemen</th>
                                    <th scope="col" class="px-4 py-3.5">Ukuran</th>
                                    <th scope="col" class="px-4 py-3.5">Tanggal</th>
                                    <th scope="col" class="px-4 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr
                                    v-for="file in latestFiles"
                                    :key="file.id"
                                    class="hover:bg-red-50/40 dark:hover:bg-gray-750/50 transition-colors group"
                                >
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-xs"
                                                :class="getFileIconColor(file.extension, file.mime_type)"
                                            >
                                                <component
                                                    :is="getFileIcon(file.extension, file.mime_type)"
                                                    class="w-4 h-4"
                                                />
                                            </div>
                                            <div class="max-w-[200px] sm:max-w-xs truncate">
                                                <Link
                                                    :href="route('files.show', file.id)"
                                                    class="font-bold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 truncate block text-xs sm:text-sm transition-colors"
                                                >
                                                    {{ file.title }}
                                                </Link>
                                                <span class="text-[11px] text-gray-400 truncate block font-mono">
                                                    {{ file.file_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <Badge :variant="file.department?.color || 'blue'" size="xs" dot>
                                            {{ file.department?.name || 'Umum' }}
                                        </Badge>
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">
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
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors"
                                                title="Preview Dokumen"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <a
                                                :href="route('files.download', file.id)"
                                                download
                                                class="p-2 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                                title="Unduh File"
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
                            title="Belum ada dokumen yang diunggah"
                            description="Mulai unggah dokumen dan SOP perusahaan untuk mengelola arsip digital secara rapi."
                            action-text="Unggah Dokumen Sekarang"
                            @action="showUploadModal = true"
                        />
                    </div>
                </Card>
            </div>

            <!-- Right Col: Department Distribution & Audit Activity -->
            <div class="space-y-6">
                <!-- Department Distribution Card -->
                <Card>
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                <Layers class="w-4 h-4 text-purple-500" />
                                <span>Distribusi Departemen</span>
                            </h3>
                            <Link
                                :href="route('departments.index')"
                                class="text-xs font-semibold text-red-600 hover:underline dark:text-red-400"
                            >
                                Kelola
                            </Link>
                        </div>
                    </template>

                    <div class="space-y-3.5">
                        <div
                            v-for="dept in departmentBreakdown"
                            :key="dept.id"
                            class="p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/60 transition-all hover:bg-gray-100/70"
                        >
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <Badge :variant="dept.color || 'blue'" size="xs" dot>
                                        {{ dept.code }}
                                    </Badge>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                        {{ dept.name }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ dept.files_count }} berkas
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r"
                                    :class="[
                                        dept.color === 'rose' || dept.color === 'red' ? 'from-red-500 to-rose-600' :
                                        dept.color === 'amber' ? 'from-amber-400 to-amber-600' :
                                        dept.color === 'emerald' ? 'from-emerald-400 to-emerald-600' :
                                        dept.color === 'purple' ? 'from-purple-400 to-purple-600' :
                                        dept.color === 'sky' ? 'from-sky-400 to-sky-600' :
                                        'from-blue-500 to-indigo-600'
                                    ]"
                                    :style="{ width: `${Math.min(100, Math.round(((dept.files_count || 0) / totalDepartmentFiles) * 100))}%` }"
                                />
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Recent Activities (Audit Log) -->
                <Card>
                    <template #header>
                        <div class="flex items-center justify-between w-full">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                <Activity class="w-4 h-4 text-red-500" />
                                <span>Aktivitas Terkini</span>
                            </h3>
                            <Link
                                v-if="isAdmin"
                                :href="route('activity-logs.index')"
                                class="text-xs font-semibold text-red-600 hover:underline dark:text-red-400"
                            >
                                Semua log
                            </Link>
                        </div>
                    </template>

                    <div v-if="recentActivities && recentActivities.length > 0" class="space-y-3.5">
                        <div
                            v-for="log in recentActivities"
                            :key="log.id"
                            class="flex items-start gap-3 text-xs"
                        >
                            <div class="mt-0.5 p-1.5 rounded-xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 shrink-0">
                                <Clock class="w-3.5 h-3.5" />
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-gray-800 dark:text-gray-200 leading-snug">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ log.user?.name || 'Sistem' }}</span>
                                    <span class="text-gray-600 dark:text-gray-300 ml-1">{{ log.description }}</span>
                                </p>
                                <span class="text-[10px] font-medium text-gray-400 mt-0.5 block">
                                    {{ formatTimeAgo(log.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <p v-else class="text-xs text-gray-400 text-center py-4">
                        Belum ada rekaman aktivitas audit terbaru.
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
            :departments="departments.length > 0 ? departments : departmentBreakdown"
            :folder-tree="folderTree"
            @close="showUploadModal = false"
        />

        <FolderFormModal
            v-if="isAdmin"
            :show="showFolderModal"
            :folder-tree="folderTree"
            @close="showFolderModal = false"
        />
    </AuthenticatedLayout>
</template>
