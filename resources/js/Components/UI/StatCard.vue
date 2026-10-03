<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    color: {
        type: String,
        default: 'blue',
    },
});

const colorClasses = computed(() => {
    switch (props.color) {
        case 'emerald':
            return {
                bg: 'bg-emerald-50 dark:bg-emerald-950/40',
                icon: 'text-emerald-600 dark:text-emerald-400',
                border: 'border-emerald-100 dark:border-emerald-900/50',
                accent: 'bg-emerald-500',
            };
        case 'amber':
            return {
                bg: 'bg-amber-50 dark:bg-amber-950/40',
                icon: 'text-amber-600 dark:text-amber-400',
                border: 'border-amber-100 dark:border-amber-900/50',
                accent: 'bg-amber-500',
            };
        case 'purple':
            return {
                bg: 'bg-purple-50 dark:bg-purple-950/40',
                icon: 'text-purple-600 dark:text-purple-400',
                border: 'border-purple-100 dark:border-purple-900/50',
                accent: 'bg-purple-500',
            };
        case 'rose':
            return {
                bg: 'bg-rose-50 dark:bg-rose-950/40',
                icon: 'text-rose-600 dark:text-rose-400',
                border: 'border-rose-100 dark:border-rose-900/50',
                accent: 'bg-rose-500',
            };
        case 'sky':
            return {
                bg: 'bg-sky-50 dark:bg-sky-950/40',
                icon: 'text-sky-600 dark:text-sky-400',
                border: 'border-sky-100 dark:border-sky-900/50',
                accent: 'bg-sky-500',
            };
        case 'blue':
        default:
            return {
                bg: 'bg-blue-50 dark:bg-blue-950/40',
                icon: 'text-blue-600 dark:text-blue-400',
                border: 'border-blue-100 dark:border-blue-900/50',
                accent: 'bg-blue-500',
            };
    }
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800 p-5 shadow-xs transition-all hover:shadow-md duration-200"
    >
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ title }}
                </p>
                <div class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ value }}
                </div>
                <p v-if="subtitle" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ subtitle }}
                </p>
            </div>

            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl transition-colors"
                :class="[colorClasses.bg, colorClasses.icon]"
            >
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>
