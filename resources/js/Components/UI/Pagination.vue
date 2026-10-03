<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps({
    links: {
        type: Array,
        required: true,
    },
    from: {
        type: Number,
        default: 0,
    },
    to: {
        type: Number,
        default: 0,
    },
    total: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            Showing <span class="font-medium text-gray-900 dark:text-gray-200">{{ from || 0 }}</span> to <span class="font-medium text-gray-900 dark:text-gray-200">{{ to || 0 }}</span> of <span class="font-medium text-gray-900 dark:text-gray-200">{{ total || 0 }}</span> results
        </div>

        <div class="flex items-center space-x-1">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="px-3 py-1.5 text-xs text-gray-400 dark:text-gray-600 rounded-lg select-none"
                    v-html="link.label"
                />

                <Link
                    v-else
                    :href="link.url"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                    :class="[
                        link.active
                            ? 'bg-blue-600 text-white shadow-xs'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                    ]"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
