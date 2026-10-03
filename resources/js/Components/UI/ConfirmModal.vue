<script setup>
import Modal from './Modal.vue';
import Button from './Button.vue';
import { AlertTriangle, Info } from 'lucide-vue-next';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Are you sure?',
    },
    message: {
        type: String,
        default: 'This action cannot be undone.',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    danger: {
        type: Boolean,
        default: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :show="show" max-width="md" :closeable="!loading" @close="$emit('close')">
        <div class="flex items-start gap-4">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full"
                :class="danger ? 'bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400'"
            >
                <AlertTriangle v-if="danger" class="w-6 h-6" />
                <Info v-else class="w-6 h-6" />
            </div>

            <div class="space-y-1 text-left">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ title }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ message }}
                </p>
            </div>
        </div>

        <template #footer>
            <Button
                variant="secondary"
                :disabled="loading"
                @click="$emit('close')"
            >
                {{ cancelText }}
            </Button>
            <Button
                :variant="danger ? 'danger' : 'primary'"
                :loading="loading"
                @click="$emit('confirm')"
            >
                {{ confirmText }}
            </Button>
        </template>
    </Modal>
</template>
