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

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    folder: {
        type: Object,
        default: null,
    },
    parentFolderId: {
        type: [Number, String, null],
        default: null,
    },
    folderTree: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    parent_id: null,
    description: '',
    color: 'amber',
});

watch(
    () => props.show,
    (isShown) => {
        if (isShown) {
            if (props.folder) {
                form.name = props.folder.name || '';
                form.parent_id = props.folder.parent_id || null;
                form.description = props.folder.description || '';
                form.color = props.folder.color || 'amber';
            } else {
                form.reset();
                form.parent_id = props.parentFolderId || null;
                form.color = 'amber';
            }
            form.clearErrors();
        }
    }
);

const colors = [
    { name: 'amber', class: 'bg-amber-400' },
    { name: 'blue', class: 'bg-blue-500' },
    { name: 'emerald', class: 'bg-emerald-500' },
    { name: 'purple', class: 'bg-purple-500' },
    { name: 'rose', class: 'bg-rose-500' },
    { name: 'sky', class: 'bg-sky-500' },
    { name: 'indigo', class: 'bg-indigo-500' },
];

const submit = () => {
    if (props.folder) {
        form.put(route('folders.update', props.folder.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('folders.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>

<template>
    <Modal :show="show" max-width="lg" :title="folder ? 'Edit Folder' : 'Create New Folder'" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Folder Name -->
            <div>
                <InputLabel for="folder_name" value="Folder Name *" />
                <TextInput
                    id="folder_name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. Operations Manuals, Annual Reports"
                    autofocus
                    required
                />
                <InputError :message="form.errors.name" class="mt-1.5" />
            </div>

            <!-- Parent Folder Selector (Hierarchical) -->
            <div>
                <InputLabel for="parent_folder" value="Parent Folder (Hierarchical)" />
                <SelectInput
                    id="parent_folder"
                    v-model="form.parent_id"
                    class="mt-1"
                >
                    <option :value="null">None (Root Folder)</option>
                    <option
                        v-for="item in folderTree"
                        :key="item.id"
                        :value="item.id"
                        :disabled="folder && (item.id === folder.id)"
                    >
                        {{ item.label }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.parent_id" class="mt-1.5" />
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    Supports unlimited hierarchical folder nesting.
                </p>
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="folder_description" value="Description (Optional)" />
                <TextareaInput
                    id="folder_description"
                    v-model="form.description"
                    class="mt-1"
                    rows="2"
                    placeholder="Brief description of documents stored in this folder"
                />
                <InputError :message="form.errors.description" class="mt-1.5" />
            </div>

            <!-- Color Palette Selector -->
            <div>
                <InputLabel value="Folder Color Tag" />
                <div class="mt-2 flex items-center gap-2.5">
                    <button
                        v-for="c in colors"
                        :key="c.name"
                        type="button"
                        @click="form.color = c.name"
                        class="w-7 h-7 rounded-full transition-transform"
                        :class="[
                            c.class,
                            form.color === c.name ? 'ring-2 ring-offset-2 ring-blue-500 scale-110' : 'hover:scale-105 opacity-80 hover:opacity-100'
                        ]"
                    />
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <Button variant="secondary" :disabled="form.processing" @click="$emit('close')">
                    Cancel
                </Button>
                <Button type="submit" variant="primary" :loading="form.processing">
                    {{ folder ? 'Save Changes' : 'Create Folder' }}
                </Button>
            </div>
        </form>
    </Modal>
</template>
