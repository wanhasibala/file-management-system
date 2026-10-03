<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'blue',
    },
    size: {
        type: String,
        default: 'sm',
        validator: (v) => ['xs', 'sm', 'md'].includes(v),
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const colorClasses = computed(() => {
    switch (props.variant) {
        case 'lion':
            return 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-900/60';
        case 'indigo':
            return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800';
        case 'emerald':
        case 'green':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800';
        case 'amber':
        case 'yellow':
            return 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800';
        case 'rose':
        case 'red':
            return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800';
        case 'purple':
            return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800';
        case 'sky':
        case 'teal':
            return 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800';
        case 'gray':
            return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
        case 'blue':
        default:
            return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800';
    }
});

const dotColorClasses = computed(() => {
    switch (props.variant) {
        case 'lion': return 'bg-red-600 animate-pulse';
        case 'indigo': return 'bg-indigo-500';
        case 'emerald':
        case 'green': return 'bg-emerald-500';
        case 'amber':
        case 'yellow': return 'bg-amber-500';
        case 'rose':
        case 'red': return 'bg-rose-500';
        case 'purple': return 'bg-purple-500';
        case 'sky':
        case 'teal': return 'bg-sky-500';
        case 'gray': return 'bg-gray-400';
        case 'blue':
        default: return 'bg-blue-500';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'px-2 py-0.5 text-[11px] gap-1';
        case 'md': return 'px-3 py-1 text-sm gap-1.5';
        case 'sm':
        default: return 'px-2.5 py-0.5 text-xs gap-1.5';
    }
});
</script>

<template>
    <span
        class="inline-flex items-center font-medium rounded-full border shrink-0 transition-colors"
        :class="[colorClasses, sizeClasses]"
    >
        <span
            v-if="dot"
            class="w-1.5 h-1.5 rounded-full"
            :class="dotColorClasses"
        />
        <slot />
    </span>
</template>
