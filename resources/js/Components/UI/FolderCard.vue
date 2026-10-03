<script setup>
import { Link } from '@inertiajs/vue3';
import { Folder, MoreVertical, Edit2, Trash2, FolderTree } from 'lucide-vue-next';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    folder: {
        type: Object,
        required: true,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['edit', 'delete']);
</script>

<template>
    <div
        class="group relative flex flex-col justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
    >
        <div class="flex items-start justify-between">
            <Link
                :href="route('folders.show', folder.id)"
                class="flex items-center gap-3.5 flex-1 overflow-hidden"
            >
                <div
                    class="p-3 rounded-2xl bg-gradient-to-br from-amber-400/10 to-amber-500/20 text-amber-500 dark:text-amber-400 group-hover:scale-110 shadow-xs transition-transform shrink-0"
                >
                    <Folder class="w-6 h-6 fill-amber-500/20 text-amber-500" />
                </div>

                <div class="overflow-hidden">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        {{ folder.name }}
                    </h4>
                    <p v-if="folder.description" class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                        {{ folder.description }}
                    </p>
                </div>
            </Link>

            <!-- Admin Dropdown Menu -->
            <div v-if="isAdmin" class="relative shrink-0 ml-2">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button
                            type="button"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <MoreVertical class="w-4 h-4" />
                        </button>
                    </template>

                    <template #content>
                        <button
                            type="button"
                            @click="$emit('edit', folder)"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <Edit2 class="w-3.5 h-3.5 text-blue-500" />
                            <span>Ubah / Pindahkan</span>
                        </button>
                        <button
                            type="button"
                            @click="$emit('delete', folder)"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30"
                        >
                            <Trash2 class="w-3.5 h-3.5" />
                            <span>Hapus Folder</span>
                        </button>
                    </template>
                </Dropdown>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] font-medium text-gray-400 dark:text-gray-500">
            <span class="flex items-center gap-1.5 text-amber-600/80 dark:text-amber-400/80">
                <FolderTree class="w-3.5 h-3.5" />
                <span>{{ folder.children_count || 0 }} subfolder</span>
            </span>
            <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-750 text-gray-600 dark:text-gray-300 font-semibold">
                {{ folder.files_count || 0 }} berkas
            </span>
        </div>
    </div>
</template>
