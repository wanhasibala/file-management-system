<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/UI/SelectInput.vue';
import TextareaInput from '@/Components/UI/TextareaInput.vue';
import DragDropUpload from '@/Components/UI/DragDropUpload.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    folderId: {
        type: [Number, String, null],
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
    file: null,
    description: '',
});

watch(
    () => props.show,
    (isShown) => {
        if (isShown) {
            form.reset();
            form.folder_id = props.folderId || null;
            if (props.departments.length > 0 && !form.department_id) {
                form.department_id = props.departments[0].id;
            }
            form.clearErrors();
        }
    }
);

const onFileSelected = (file) => {
    form.file = file;
    // Auto-fill title if empty
    if (file && !form.title) {
        const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
        // Clean up underscores/hyphens to title case
        form.title = baseName.replace(/[-_]/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
    }
};

const submit = () => {
    form.post(route('files.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="show" max-width="xl" title="Upload Document" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Drag and Drop File Upload -->
            <div>
                <InputLabel value="Upload File *" />
                <DragDropUpload
                    v-model="form.file"
                    :error="form.errors.file"
                    @change="onFileSelected"
                    class="mt-1"
                />
            </div>

            <!-- Title -->
            <div>
                <InputLabel for="file_title" value="Document Title *" />
                <TextInput
                    id="file_title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. Flight Safety Checklist SOP 2026"
                    required
                />
                <InputError :message="form.errors.title" class="mt-1.5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Department (Mandatory) -->
                <div>
                    <InputLabel for="file_department" value="Department *" />
                    <SelectInput
                        id="file_department"
                        v-model="form.department_id"
                        class="mt-1"
                        required
                    >
                        <option value="" disabled>Select Department</option>
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

                <!-- Destination Folder -->
                <div>
                    <InputLabel for="destination_folder" value="Destination Folder" />
                    <SelectInput
                        id="destination_folder"
                        v-model="form.folder_id"
                        class="mt-1"
                    >
                        <option :value="null">Root Directory (No folder)</option>
                        <option
                            v-for="item in folderTree"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.label }}
                        </option>
                    </SelectInput>
                    <InputError :message="form.errors.folder_id" class="mt-1.5" />
                </div>
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="file_description" value="Description / Notes (Optional)" />
                <TextareaInput
                    id="file_description"
                    v-model="form.description"
                    class="mt-1"
                    rows="2"
                    placeholder="Summary of document purpose and version details..."
                />
                <InputError :message="form.errors.description" class="mt-1.5" />
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <Button variant="secondary" :disabled="form.processing" @click="$emit('close')">
                    Cancel
                </Button>
                <Button type="submit" variant="primary" :loading="form.processing" :disabled="!form.file">
                    Upload Document
                </Button>
            </div>
        </form>
    </Modal>
</template>
