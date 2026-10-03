<script setup>
import { ref, computed } from 'vue';
import { UploadCloud, File as FileIcon, X, CheckCircle, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: [Object, File, null],
        default: null,
    },
    accept: {
        type: String,
        default: '*/*',
    },
    maxSizeMb: {
        type: Number,
        default: 50,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isDragging = ref(false);
const fileInput = ref(null);
const localError = ref('');

const selectedFile = computed(() => props.modelValue);

const formattedSize = computed(() => {
    if (!selectedFile.value) return '';
    const bytes = selectedFile.value.size;
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
});

const triggerBrowse = () => {
    fileInput.value?.click();
};

const handleFileSelect = (file) => {
    localError.value = '';

    if (!file) return;

    if (file.size > props.maxSizeMb * 1024 * 1024) {
        localError.value = `File size (${(file.size / (1024 * 1024)).toFixed(1)} MB) exceeds limit of ${props.maxSizeMb} MB.`;
        return;
    }

    emit('update:modelValue', file);
    emit('change', file);
};

const onFileInputChange = (e) => {
    const file = e.target.files[0];
    handleFileSelect(file);
};

const onDragOver = (e) => {
    e.preventDefault();
    isDragging.value = true;
};

const onDragLeave = (e) => {
    e.preventDefault();
    isDragging.value = false;
};

const onDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    handleFileSelect(file);
};

const removeFile = () => {
    emit('update:modelValue', null);
    emit('change', null);
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};
</script>

<template>
    <div class="space-y-2">
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            class="hidden"
            @change="onFileInputChange"
        />

        <!-- Drop Zone (when no file selected) -->
        <div
            v-if="!selectedFile"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onDrop"
            @click="triggerBrowse"
            class="relative border-2 border-dashed rounded-3xl p-6 sm:p-8 text-center cursor-pointer transition-all duration-300 group"
            :class="[
                isDragging
                    ? 'border-red-500 bg-red-50/60 dark:bg-red-950/30 scale-[1.01]'
                    : 'border-gray-300 dark:border-gray-700 hover:border-red-500 dark:hover:border-red-500 bg-gray-50/60 dark:bg-gray-800/40 hover:bg-red-50/20 dark:hover:bg-gray-800'
            ]"
        >
            <div class="flex flex-col items-center justify-center space-y-3">
                <div
                    class="p-4 rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 group-hover:scale-110 shadow-xs transition-transform"
                >
                    <UploadCloud class="w-8 h-8" />
                </div>

                <div class="space-y-1 text-center">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <span class="text-red-600 dark:text-red-400 hover:underline">Pilih berkas dokumen</span> atau tarik & lepas di sini
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Format PDF, DOCX, XLSX, Gambar, atau ZIP (Maksimal {{ maxSizeMb }}MB)
                    </p>
                </div>
            </div>
        </div>

        <!-- Selected File Preview Card -->
        <div
            v-else
            class="flex items-center justify-between p-4 bg-red-50/50 dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-gray-700 shadow-xs"
        >
            <div class="flex items-center gap-3.5 overflow-hidden">
                <div class="p-3 bg-gradient-to-br from-red-600 to-rose-600 text-white rounded-xl shadow-xs shrink-0">
                    <FileIcon class="w-5 h-5" />
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        {{ selectedFile.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                        {{ formattedSize }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800 mr-1">
                    <CheckCircle class="w-3.5 h-3.5" /> Siap Diunggah
                </span>
                <button
                    type="button"
                    @click="removeFile"
                    class="p-2 text-gray-400 hover:text-red-600 rounded-xl hover:bg-white dark:hover:bg-gray-700 transition-colors"
                    title="Ganti Berkas"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Error Message -->
        <div v-if="localError || error" class="flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400 pt-1">
            <AlertCircle class="w-3.5 h-3.5 shrink-0" />
            <span>{{ localError || error }}</span>
        </div>
    </div>
</template>
