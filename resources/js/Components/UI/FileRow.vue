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
    Folder as FolderIcon,
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
    if (ext === 'pdf') return 'text-red-500';
    if (['png', 'jpg', 'jpeg', 'svg'].includes(ext)) return 'text-emerald-500';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'text-green-600';
    return 'text-blue-500';
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
    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-750/50 transition-colors">
        <!-- Title & Icon -->
        <td class="px-5 py-3.5 whitespace-nowrap">
            <div class="flex items-center gap-3">
                <component :is="fileIcon" class="w-5 h-5 shrink-0" :class="iconColorClasses" />
                <div class="max-w-xs md:max-w-md truncate">
                    <Link
                        :href="route('files.show', file.id)"
                        class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block"
                    >
                        {{ file.title }}
                    </Link>
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate block">
                        {{ file.file_name }}
                    </span>
                </div>
            </div>
        </td>

        <!-- Department -->
        <td class="px-4 py-3.5 whitespace-nowrap text-xs">
            <Badge :variant="file.department?.color || 'blue'" size="xs" dot>
                {{ file.department?.name || 'General' }}
            </Badge>
        </td>

        <!-- Folder -->
        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
            <Link
                v-if="file.folder"
                :href="route('folders.show', file.folder.id)"
                class="inline-flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400"
            >
                <FolderIcon class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                <span class="truncate max-w-[140px]">{{ file.folder.name }}</span>
            </Link>
            <span v-else class="text-gray-400 dark:text-gray-600 italic">Root</span>
        </td>

        <!-- Size -->
        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
            {{ file.formatted_size }}
        </td>

        <!-- Upload Date -->
        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
            {{ formattedDate }}
        </td>

        <!-- Actions -->
        <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs">
            <div class="flex items-center justify-end gap-1">
                <button
                    type="button"
                    @click="$emit('preview', file)"
                    class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="Preview"
                >
                    <Eye class="w-4 h-4" />
                </button>

                <a
                    :href="route('files.download', file.id)"
                    download
                    class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="Download"
                >
                    <Download class="w-4 h-4" />
                </a>

                <!-- Dropdown for more actions -->
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button
                            type="button"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
                            <span>View Details</span>
                        </Link>

                        <template v-if="isAdmin">
                            <button
                                type="button"
                                @click="$emit('edit', file)"
                                class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <Edit2 class="w-3.5 h-3.5 text-amber-500" />
                                <span>Edit Metadata</span>
                            </button>
                            <button
                                type="button"
                                @click="$emit('delete', file)"
                                class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                                <span>Delete File</span>
                            </button>
                        </template>
                    </template>
                </Dropdown>
            </div>
        </td>
    </tr>
</template>
