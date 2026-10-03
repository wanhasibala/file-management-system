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
        case 'lion':
        case 'red':
            return {
                bg: 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400',
                border: 'border-red-100 dark:border-red-900/50',
                glow: 'group-hover:border-red-300 dark:group-hover:border-red-700',
                gradient: 'from-red-500/10 to-transparent',
            };
        case 'emerald':
            return {
                bg: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400',
                border: 'border-emerald-100 dark:border-emerald-900/50',
                glow: 'group-hover:border-emerald-300 dark:group-hover:border-emerald-700',
                gradient: 'from-emerald-500/10 to-transparent',
            };
        case 'amber':
            return {
                bg: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400',
                border: 'border-amber-100 dark:border-amber-900/50',
                glow: 'group-hover:border-amber-300 dark:group-hover:border-amber-700',
                gradient: 'from-amber-500/10 to-transparent',
            };
        case 'purple':
            return {
                bg: 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400',
                border: 'border-purple-100 dark:border-purple-900/50',
                glow: 'group-hover:border-purple-300 dark:group-hover:border-purple-700',
                gradient: 'from-purple-500/10 to-transparent',
            };
        case 'rose':
            return {
                bg: 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400',
                border: 'border-rose-100 dark:border-rose-900/50',
                glow: 'group-hover:border-rose-300 dark:group-hover:border-rose-700',
                gradient: 'from-rose-500/10 to-transparent',
            };
        case 'sky':
            return {
                bg: 'bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400',
                border: 'border-sky-100 dark:border-sky-900/50',
                glow: 'group-hover:border-sky-300 dark:group-hover:border-sky-700',
                gradient: 'from-sky-500/10 to-transparent',
            };
        case 'blue':
        default:
            return {
                bg: 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400',
                border: 'border-blue-100 dark:border-blue-900/50',
                glow: 'group-hover:border-blue-300 dark:group-hover:border-blue-700',
                gradient: 'from-blue-500/10 to-transparent',
            };
    }
});
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-2xl border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800 p-5 shadow-xs transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5"
        :class="colorClasses.glow"
    >
        <!-- Subtle Top Accent Gradient -->
        <div
            class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r opacity-80"
            :class="[
                color === 'lion' || color === 'red' ? 'from-red-600 via-rose-500 to-amber-500' :
                color === 'emerald' ? 'from-emerald-500 to-teal-400' :
                color === 'amber' ? 'from-amber-500 to-yellow-400' :
                color === 'purple' ? 'from-purple-500 to-indigo-400' :
                color === 'sky' ? 'from-sky-500 to-cyan-400' :
                'from-blue-600 to-cyan-500'
            ]"
        />

        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ title }}
                </p>
                <div class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    {{ value }}
                </div>
                <p v-if="subtitle" class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ subtitle }}
                </p>
            </div>

            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl transition-transform duration-300 group-hover:scale-110 shadow-xs"
                :class="colorClasses.bg"
            >
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>
