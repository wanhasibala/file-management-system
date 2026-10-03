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
import { Folder } from 'lucide-vue-next';

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

const selectedParentInfo = computed(() => {
    if (!form.parent_id) return null;
    return props.folderTree.find((f) => String(f.id) === String(form.parent_id));
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
    <Modal :show="show" max-width="lg" :title="folder ? 'Ubah Informasi Folder' : 'Buat Folder Baru'" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Folder Name -->
            <div>
                <InputLabel for="folder_name" value="Nama Folder *" />
                <TextInput
                    id="folder_name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full rounded-xl focus:border-red-500 focus:ring-red-500"
                    placeholder="Contoh: Dokumen Operasional Penerbangan 2026"
                    autofocus
                    required
                />
                <InputError :message="form.errors.name" class="mt-1.5" />
            </div>

            <!-- Parent Folder Selector (Hierarchical) -->
            <div>
                <InputLabel for="parent_folder" value="Induk Folder / Parent (Hierarki)" />
                <SelectInput
                    id="parent_folder"
                    v-model="form.parent_id"
                    class="mt-1 font-mono text-xs rounded-xl"
                >
                    <option :value="null">[Root] Direktori Utama (Tanpa Induk)</option>
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
                <div v-if="selectedParentInfo" class="mt-1.5 flex items-center gap-1.5 text-[11px] text-red-600 dark:text-red-400 font-medium">
                    <Folder class="w-3.5 h-3.5 shrink-0" />
                    <span class="truncate">Lokasi Parent: {{ selectedParentInfo.path }}</span>
                </div>
                <p v-else class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    Mendukung struktur hirarki folder bersarang tanpa batas kedalaman.
                </p>
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="folder_description" value="Keterangan / Deskripsi (Opsional)" />
                <TextareaInput
                    id="folder_description"
                    v-model="form.description"
                    class="mt-1 rounded-xl"
                    rows="2"
                    placeholder="Penjelasan singkat mengenai berkas yang disimpan pada folder ini..."
                />
                <InputError :message="form.errors.description" class="mt-1.5" />
            </div>

            <!-- Color Palette Selector -->
            <div>
                <InputLabel value="Label Warna Folder" />
                <div class="mt-2 flex items-center gap-2.5">
                    <button
                        v-for="c in colors"
                        :key="c.name"
                        type="button"
                        @click="form.color = c.name"
                        class="w-7 h-7 rounded-full transition-transform"
                        :class="[
                            c.class,
                            form.color === c.name ? 'ring-2 ring-offset-2 ring-red-500 scale-110 shadow-xs' : 'hover:scale-105 opacity-80 hover:opacity-100'
                        ]"
                    />
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <Button variant="secondary" :disabled="form.processing" @click="$emit('close')">
                    Batal
                </Button>
                <Button type="submit" variant="primary" :loading="form.processing">
                    {{ folder ? 'Simpan Perubahan' : 'Buat Folder' }}
                </Button>
            </div>
        </form>
    </Modal>
</template>
