<script setup>
import { computed } from 'vue';
import { Loader2 } from 'lucide-vue-next';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => [
            'primary',
            'secondary',
            'danger',
            'warning',
            'success',
            'outline',
            'ghost',
        ].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-gradient-to-r from-red-600 via-rose-600 to-red-600 hover:from-red-700 hover:via-rose-700 hover:to-red-700 text-white shadow-sm shadow-red-500/25 border border-transparent focus:ring-red-500 dark:shadow-red-950/50';
        case 'lion':
            return 'bg-gradient-to-r from-red-600 via-rose-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white shadow-md shadow-red-500/30 border border-transparent focus:ring-red-500';
        case 'secondary':
            return 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-200/90 shadow-xs focus:ring-red-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-750';
        case 'danger':
            return 'bg-red-600 hover:bg-red-700 text-white shadow-sm shadow-red-600/20 border border-transparent focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-500';
        case 'warning':
            return 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20 border border-transparent focus:ring-amber-500 dark:bg-amber-600 dark:hover:bg-amber-500';
        case 'success':
            return 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-600/20 border border-transparent focus:ring-emerald-500 dark:bg-emerald-600 dark:hover:bg-emerald-500';
        case 'outline':
            return 'bg-transparent hover:bg-red-50 text-red-600 border border-red-500/80 focus:ring-red-500 dark:text-red-400 dark:border-red-500/60 dark:hover:bg-red-950/30';
        case 'ghost':
            return 'bg-transparent hover:bg-gray-100 text-gray-700 border border-transparent focus:ring-gray-400 dark:text-gray-300 dark:hover:bg-gray-800';
        default:
            return 'bg-red-600 text-white';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'px-2 py-1 text-xs gap-1';
        case 'sm':
            return 'px-2.5 py-1.5 text-xs font-medium gap-1.5';
        case 'lg':
            return 'px-5 py-2.5 text-base font-medium gap-2.5';
        case 'md':
        default:
            return 'px-3.5 py-2 text-sm font-medium gap-2';
    }
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer select-none"
        :class="[variantClasses, sizeClasses]"
    >
        <Loader2 v-if="loading" class="w-4 h-4 animate-spin shrink-0" />
        <slot name="icon" v-else-if="$slots.icon" />
        <slot />
    </button>
</template>
