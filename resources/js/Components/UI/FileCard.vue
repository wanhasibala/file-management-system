<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    FileText,
    Image as ImageIcon,
    FileCode,
    FileSpreadsheet,
    File as GenericFileIcon,
    Download,
    Eye,
    MoreVertical,
    Edit2,
    Trash2,
    Info,
    Calendar,
    User as UserIcon,
} from 'lucide-vue-next';
import Badge from './Badge.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['preview', 'edit', 'delete']);

const fileIcon = computed(() => {
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();

    if (ext === 'pdf' || mime === 'application/pdf') return FileText;
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/')) return ImageIcon;
    if (['xls', 'xlsx', 'csv'].includes(ext)) return FileSpreadsheet;
    if (['txt', 'json', 'md', 'html', 'xml'].includes(ext) || mime.startsWith('text/')) return FileCode;
    return GenericFileIcon;
});

const iconColorClasses = computed(() => {
    const ext = (props.file.extension || '').toLowerCase();
    if (ext === 'pdf') return 'bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400';
    if (['png', 'jpg', 'jpeg', 'svg'].includes(ext)) return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'bg-green-50 text-green-700 dark:bg-green-950/50 dark:text-green-400';
    return 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400';
});

const formattedDate = computed(() => {
    if (!props.file.created_at) return '';
    return new Date(props.file.created_at).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
});
</script>

<template>
    <div
        class="group relative flex flex-col justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 hover:border-red-400 dark:hover:border-red-500 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
    >
        <!-- Top Section -->
        <div>
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-3 overflow-hidden flex-1">
                    <div
                        class="p-2.5 rounded-2xl shrink-0 transition-transform group-hover:scale-110 shadow-xs"
                        :class="iconColorClasses"
                    >
                        <component :is="fileIcon" class="w-6 h-6" />
                    </div>

                    <div class="overflow-hidden flex-1">
                        <Link
                            :href="route('files.show', file.id)"
                            class="text-sm font-bold text-gray-900 dark:text-white truncate block hover:text-red-600 dark:hover:text-red-400 transition-colors"
                        >
                            {{ file.title }}
                        </Link>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5 font-mono">
                            {{ file.file_name }}
                        </p>
                    </div>
                </div>

                <!-- Dropdown actions -->
                <div class="relative shrink-0">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                <MoreVertical class="w-4 h-4" />
                            </button>
                        </template>

                        <template #content>
                            <Link
                                :href="route('files.show', file.id)"
                                class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <Info class="w-3.5 h-3.5 text-blue-500" />
                                <span>Detail Dokumen</span>
                            </Link>

                            <button
                                type="button"
                                @click="$emit('preview', file)"
                                class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <Eye class="w-3.5 h-3.5 text-emerald-500" />
                                <span>Lihat Pratinjau</span>
                            </button>

                            <a
                                :href="route('files.download', file.id)"
                                download
                                class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <Download class="w-3.5 h-3.5 text-purple-500" />
                                <span>Unduh Berkas</span>
                            </a>

                            <template v-if="isAdmin">
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1" />
                                <button
                                    type="button"
                                    @click="$emit('edit', file)"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <Edit2 class="w-3.5 h-3.5 text-amber-500" />
                                    <span>Ubah Metadata</span>
                                </button>
                                <button
                                    type="button"
                                    @click="$emit('delete', file)"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                    <span>Hapus Dokumen</span>
                                </button>
                            </template>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <!-- Department badge -->
            <div class="mt-3 flex items-center justify-between">
                <Badge :variant="file.department?.color || 'blue'" size="xs" dot>
                    {{ file.department?.name || 'Umum' }}
                </Badge>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    {{ file.formatted_size }}
                </span>
            </div>
        </div>

        <!-- Bottom Metadata & Quick Actions -->
        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
            <span class="flex items-center gap-1.5 truncate max-w-[130px]">
                <Calendar class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                <span class="truncate">{{ formattedDate }}</span>
            </span>

            <div class="flex items-center gap-1">
                <button
                    type="button"
                    @click="$emit('preview', file)"
                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors"
                    title="Pratinjau Dokumen"
                >
                    <Eye class="w-4 h-4" />
                </button>
                <a
                    :href="route('files.download', file.id)"
                    download
                    class="p-1.5 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                    title="Unduh Berkas"
                >
                    <Download class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</template>
