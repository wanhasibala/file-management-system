<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Home, Folder } from 'lucide-vue-next';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav class="flex items-center space-x-1.5 text-sm text-gray-500 dark:text-gray-400 overflow-x-auto py-1 scrollbar-none">
        <!-- Root link -->
        <Link
            :href="route('folders.index')"
            class="flex items-center gap-1.5 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors shrink-0"
        >
            <Home class="w-4 h-4" />
            <span>Root</span>
        </Link>

        <!-- Dynamic segments -->
        <template v-for="(item, index) in items" :key="item.id || index">
            <template v-if="item.id !== null">
                <ChevronRight class="w-4 h-4 text-gray-400 dark:text-gray-600 shrink-0" />

                <Link
                    v-if="index < items.length - 1"
                    :href="route('folders.show', item.id)"
                    class="flex items-center gap-1.5 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0 max-w-[200px] truncate"
                >
                    <Folder class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    <span class="truncate">{{ item.name }}</span>
                </Link>

                <div
                    v-else
                    class="flex items-center gap-1.5 font-semibold text-gray-900 dark:text-white shrink-0 max-w-[240px] truncate"
                >
                    <Folder class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    <span class="truncate">{{ item.name }}</span>
                </div>
            </template>
        </template>
    </nav>
</template>
