<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextareaInput from '@/Components/UI/TextareaInput.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    department: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    code: '',
    description: '',
    color: 'blue',
});

const colors = [
    { name: 'blue', class: 'bg-blue-500' },
    { name: 'indigo', class: 'bg-indigo-500' },
    { name: 'emerald', class: 'bg-emerald-500' },
    { name: 'amber', class: 'bg-amber-500' },
    { name: 'purple', class: 'bg-purple-500' },
    { name: 'rose', class: 'bg-rose-500' },
    { name: 'sky', class: 'bg-sky-500' },
];

watch(
    () => props.show,
    (isShown) => {
        if (isShown) {
            if (props.department) {
                form.name = props.department.name || '';
                form.code = props.department.code || '';
                form.description = props.department.description || '';
                form.color = props.department.color || 'blue';
            } else {
                form.reset();
                form.color = 'blue';
            }
            form.clearErrors();
        }
    }
);

const submit = () => {
    if (props.department) {
        form.put(route('departments.update', props.department.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('departments.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>

<template>
    <Modal :show="show" max-width="md" :title="department ? 'Edit Department' : 'Add Department'" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <div>
                <InputLabel for="dept_name" value="Department Name *" />
                <TextInput
                    id="dept_name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. Flight Operations, IT, Finance"
                    required
                    autofocus
                />
                <InputError :message="form.errors.name" class="mt-1.5" />
            </div>

            <!-- Code -->
            <div>
                <InputLabel for="dept_code" value="Code / Abbreviation *" />
                <TextInput
                    id="dept_code"
                    v-model="form.code"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    placeholder="e.g. OPS, IT, FIN, HR"
                    maxlength="20"
                    required
                />
                <InputError :message="form.errors.code" class="mt-1.5" />
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="dept_description" value="Description (Optional)" />
                <TextareaInput
                    id="dept_description"
                    v-model="form.description"
                    class="mt-1"
                    rows="2"
                    placeholder="Brief description of department scope..."
                />
                <InputError :message="form.errors.description" class="mt-1.5" />
            </div>

            <!-- Color -->
            <div>
                <InputLabel value="Badge Color Theme" />
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
                    {{ department ? 'Save Changes' : 'Create Department' }}
                </Button>
            </div>
        </form>
    </Modal>
</template>
