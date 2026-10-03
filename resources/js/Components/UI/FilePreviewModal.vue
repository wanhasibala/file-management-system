<script setup>
import { computed } from 'vue';
import Modal from './Modal.vue';
import Button from './Button.vue';
import Badge from './Badge.vue';
import {
    Download,
    ExternalLink,
    FileText,
    Image as ImageIcon,
    FileCode,
    FileSpreadsheet,
    File as GenericFileIcon,
} from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    file: {
        type: Object,
        default: null,
    },
});

defineEmits(['close']);

const previewUrl = computed(() => {
    return props.file ? route('files.preview', props.file.id) : '';
});

const downloadUrl = computed(() => {
    return props.file ? route('files.download', props.file.id) : '';
});

const isPdf = computed(() => {
    if (!props.file) return false;
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ext === 'pdf' || mime === 'application/pdf';
});

const isImage = computed(() => {
    if (!props.file) return false;
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext) || mime.startsWith('image/');
});

const isText = computed(() => {
    if (!props.file) return false;
    const ext = (props.file.extension || '').toLowerCase();
    const mime = (props.file.mime_type || '').toLowerCase();
    return ['txt', 'csv', 'json', 'md', 'log', 'xml'].includes(ext) || mime.startsWith('text/');
});
</script>

<template>
    <Modal :show="show" max-width="5xl" @close="$emit('close')">
        <template #header>
            <div class="flex items-center gap-3 overflow-hidden pr-6">
                <div
                    class="p-2 rounded-lg shrink-0"
                    :class="isPdf ? 'bg-red-100 text-red-600 dark:bg-red-950/60' : (isImage ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60' : 'bg-blue-100 text-blue-600 dark:bg-blue-950/60')"
                >
                    <FileText v-if="isPdf" class="w-5 h-5" />
                    <ImageIcon v-else-if="isImage" class="w-5 h-5" />
                    <FileCode v-else-if="isText" class="w-5 h-5" />
                    <GenericFileIcon v-else class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                        {{ file?.title || file?.file_name }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ file?.file_name }} &bull; {{ file?.formatted_size }}
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <!-- PDF Viewer -->
            <div v-if="isPdf" class="w-full h-[65vh] bg-gray-100 dark:bg-gray-900 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                <iframe
                    :src="previewUrl"
                    class="w-full h-full border-none"
                    title="PDF Preview"
                ></iframe>
            </div>

            <!-- Image Viewer -->
            <div v-else-if="isImage" class="flex items-center justify-center p-4 bg-gray-900/5 dark:bg-gray-900 rounded-xl min-h-[40vh] max-h-[65vh] overflow-auto">
                <img
                    :src="previewUrl"
                    :alt="file?.title"
                    class="max-h-[60vh] max-w-full object-contain rounded-lg shadow-sm"
                />
            </div>

            <!-- Text / Code Viewer -->
            <div v-else-if="isText" class="w-full h-[60vh] bg-gray-900 rounded-xl overflow-hidden border border-gray-800">
                <iframe
                    :src="previewUrl"
                    class="w-full h-full bg-transparent text-gray-100 font-mono text-xs border-none p-2"
                    title="Text Preview"
                ></iframe>
            </div>

            <!-- Unsupported format fallback -->
            <div v-else class="text-center py-12 px-4 space-y-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <div class="w-14 h-14 mx-auto rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <GenericFileIcon class="w-7 h-7" />
                </div>
                <div class="space-y-1">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                        Direct Preview Unavailable
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        This file format ({{ file?.extension?.toUpperCase() || 'Binary' }}) cannot be directly previewed in the browser. You can download the file to view it on your device.
                    </p>
                </div>
            </div>

            <!-- Metadata info footer inside modal -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 bg-gray-50 dark:bg-gray-800/70 rounded-xl text-xs">
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block">Department</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ file?.department?.name || 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block">Folder</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ file?.folder?.name || 'Root' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block">Uploaded By</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ file?.user?.name || 'System' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block">Downloads</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ file?.download_count || 0 }} times</span>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex items-center justify-between w-full">
                <a
                    :href="previewUrl"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    <ExternalLink class="w-3.5 h-3.5" />
                    <span>Open in new tab</span>
                </a>

                <div class="flex items-center gap-2">
                    <Button variant="secondary" @click="$emit('close')">
                        Close
                    </Button>
                    <a :href="downloadUrl" download>
                        <Button variant="primary">
                            <template #icon>
                                <Download class="w-4 h-4" />
                            </template>
                            Download File
                        </Button>
                    </a>
                </div>
            </div>
        </template>
    </Modal>
</template>
