<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/UI/SelectInput.vue';
import TextareaInput from '@/Components/UI/TextareaInput.vue';
import DragDropUpload from '@/Components/UI/DragDropUpload.vue';
import { Folder } from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    file: {
        type: Object,
        default: null,
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

const emit = defineEmits(['close']);

const form = useForm({
    title: '',
    department_id: '',
    folder_id: null,
    description: '',
    file: null, // optional replacement
});

const selectedFolderInfo = computed(() => {
    if (!form.folder_id) return null;
    return props.folderTree.find((f) => String(f.id) === String(form.folder_id));
});

watch(
    () => props.show,
    (isShown) => {
        if (isShown && props.file) {
            form.title = props.file.title || '';
            form.department_id = props.file.department_id || '';
            form.folder_id = props.file.folder_id ?? null;
            form.description = props.file.description || '';
            form.file = null;
            form.clearErrors();
        }
    }
);

const submit = () => {
    if (!props.file) return;

    // Use POST with _method spoofing or standard update endpoint for multipart form
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('files.update.post', props.file.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="show" max-width="xl" title="Edit Document Information" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Current File Banner -->
            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700 flex items-center justify-between text-xs">
                <div>
                    <span class="text-gray-400 block">Current File</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ file?.file_name }}</span>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 block">Size</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ file?.formatted_size }}</span>
                </div>
            </div>

            <!-- Title -->
            <div>
                <InputLabel for="edit_title" value="Judul Dokumen *" />
                <TextInput
                    id="edit_title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full rounded-xl focus:border-red-500 focus:ring-red-500"
                    required
                />
                <InputError :message="form.errors.title" class="mt-1.5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Department -->
                <div>
                    <InputLabel for="edit_department" value="Departemen / Divisi *" />
                    <SelectInput
                        id="edit_department"
                        v-model="form.department_id"
                        class="mt-1 rounded-xl"
                        required
                    >
                        <option
                            v-for="dept in departments"
                            :key="dept.id"
                            :value="dept.id"
                        >
                            {{ dept.name }} ({{ dept.code }})
                        </option>
                    </SelectInput>
                    <InputError :message="form.errors.department_id" class="mt-1.5" />
                </div>

                <!-- Folder Location -->
                <div>
                    <InputLabel for="edit_folder" value="Lokasi Folder (Hierarki)" />
                    <SelectInput
                        id="edit_folder"
                        v-model="form.folder_id"
                        class="mt-1 font-mono text-xs rounded-xl"
                    >
                        <option :value="null">📁 [Root] (Direktori Utama / Tanpa Folder)</option>
                        <option
                            v-for="item in folderTree"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.label }}
                        </option>
                    </SelectInput>
                    <InputError :message="form.errors.folder_id" class="mt-1.5" />
                    <div v-if="selectedFolderInfo" class="mt-1.5 flex items-center gap-1.5 text-[11px] text-red-600 dark:text-red-400 font-medium">
                        <Folder class="w-3.5 h-3.5 shrink-0" />
                        <span class="truncate">Path: {{ selectedFolderInfo.path }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="edit_description" value="Keterangan / Catatan Dokumen" />
                <TextareaInput
                    id="edit_description"
                    v-model="form.description"
                    class="mt-1 rounded-xl"
                    rows="2"
                    placeholder="Catatan versi atau revisi dokumen..."
                />
                <InputError :message="form.errors.description" class="mt-1.5" />
            </div>

            <!-- Replace File (Optional) -->
            <div>
                <InputLabel value="Ganti Berkas File (Opsional)" />
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                    Biarkan kosong jika tetap menggunakan file yang sekarang. Mengunggah file baru akan menggantikan fisik berkas lama.
                </p>
                <DragDropUpload
                    v-model="form.file"
                    :error="form.errors.file"
                />
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <Button variant="secondary" :disabled="form.processing" @click="$emit('close')">
                    Batal
                </Button>
                <Button type="submit" variant="primary" :loading="form.processing">
                    Simpan Perubahan
                </Button>
            </div>
        </form>
    </Modal>
</template>
