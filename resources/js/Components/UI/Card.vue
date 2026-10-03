<script setup>
defineProps({
    hoverable: {
        type: Boolean,
        default: false,
    },
    padding: {
        type: String,
        default: 'default', // 'none' | 'sm' | 'default' | 'lg'
    },
});
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs overflow-hidden transition-all duration-200"
        :class="{
            'hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600': hoverable,
        }"
    >
        <div
            v-if="$slots.header"
            class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/80 flex items-center justify-between"
        >
            <slot name="header" />
        </div>

        <div
            :class="{
                'p-0': padding === 'none',
                'p-3 sm:p-4': padding === 'sm',
                'p-5 sm:p-6': padding === 'default',
                'p-6 sm:p-8': padding === 'lg',
            }"
        >
            <slot />
        </div>

        <div
            v-if="$slots.footer"
            class="px-5 py-3.5 bg-gray-50/70 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/80"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
