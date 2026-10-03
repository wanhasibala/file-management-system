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
            return 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm border border-transparent focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-500';
        case 'secondary':
            return 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700';
        case 'danger':
            return 'bg-red-600 hover:bg-red-700 text-white shadow-sm border border-transparent focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-500';
        case 'warning':
            return 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm border border-transparent focus:ring-amber-500 dark:bg-amber-600 dark:hover:bg-amber-500';
        case 'success':
            return 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm border border-transparent focus:ring-emerald-500 dark:bg-emerald-600 dark:hover:bg-emerald-500';
        case 'outline':
            return 'bg-transparent hover:bg-blue-50 text-blue-600 border border-blue-600 focus:ring-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-950/40';
        case 'ghost':
            return 'bg-transparent hover:bg-gray-100 text-gray-700 border border-transparent focus:ring-gray-400 dark:text-gray-300 dark:hover:bg-gray-800';
        default:
            return 'bg-blue-600 text-white';
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
