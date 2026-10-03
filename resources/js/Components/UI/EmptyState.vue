<script setup>
import { FolderOpen } from 'lucide-vue-next';
import Button from './Button.vue';

defineProps({
    title: {
        type: String,
        default: 'No items found',
    },
    description: {
        type: String,
        default: 'There are no records to display at this moment.',
    },
    actionText: {
        type: String,
        default: '',
    },
});

defineEmits(['action']);
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-850/40">
        <div class="p-4 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400 mb-3.5">
            <slot name="icon">
                <FolderOpen class="w-8 h-8" />
            </slot>
        </div>

        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ title }}
        </h3>

        <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-sm">
            {{ description }}
        </p>

        <div v-if="actionText || $slots.action" class="mt-5">
            <slot name="action">
                <Button variant="primary" size="sm" @click="$emit('action')">
                    {{ actionText }}
                </Button>
            </slot>
        </div>
    </div>
</template>
