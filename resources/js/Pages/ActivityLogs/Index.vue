<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { History, User, Activity, Clock, ShieldCheck } from 'lucide-vue-next';

defineProps({
    logs: {
        type: Object,
        required: true,
    },
});

const getActionVariant = (action) => {
    switch (action) {
        case 'uploaded':
        case 'created':
            return 'emerald';
        case 'updated':
            return 'amber';
        case 'deleted':
            return 'rose';
        case 'downloaded':
            return 'purple';
        default:
            return 'blue';
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};
</script>

<template>
    <Head title="System Audit Logs - File Management System" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <History class="w-6 h-6 text-blue-600" />
                    <span>System Audit Trail</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Immutable security log recording all document creations, downloads, updates, and removals.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 text-xs font-semibold">
                    <ShieldCheck class="w-4 h-4" />
                    <span>Admin Security Log</span>
                </span>
            </div>
        </div>

        <!-- Audit Table Card -->
        <Card padding="none">
            <div v-if="logs.data && logs.data.length > 0" class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/70 dark:bg-gray-800/80 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-5 py-3">Timestamp</th>
                            <th scope="col" class="px-4 py-3">User</th>
                            <th scope="col" class="px-4 py-3">Action</th>
                            <th scope="col" class="px-4 py-3">Entity</th>
                            <th scope="col" class="px-5 py-3">Description</th>
                            <th scope="col" class="px-4 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr
                            v-for="log in logs.data"
                            :key="log.id"
                            class="hover:bg-gray-50/80 dark:hover:bg-gray-750/50 transition-colors text-xs"
                        >
                            <!-- Timestamp -->
                            <td class="px-5 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400 font-mono">
                                {{ formatDate(log.created_at) }}
                            </td>

                            <!-- User -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                <div class="flex items-center gap-1.5">
                                    <User class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ log.user?.name || 'System' }}</span>
                                </div>
                            </td>

                            <!-- Action Badge -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <Badge :variant="getActionVariant(log.action)" size="xs">
                                    {{ log.action.toUpperCase() }}
                                </Badge>
                            </td>

                            <!-- Entity -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-gray-700 dark:text-gray-300">
                                {{ log.entity_type }} <span v-if="log.entity_id" class="text-[10px] text-gray-400 font-normal">#{{ log.entity_id }}</span>
                            </td>

                            <!-- Description -->
                            <td class="px-5 py-3.5 text-gray-800 dark:text-gray-200">
                                {{ log.description }}
                            </td>

                            <!-- IP Address -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-gray-400">
                                {{ log.ip_address || '127.0.0.1' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-12">
                <EmptyState
                    title="No activity recorded"
                    description="Actions performed in the system will automatically appear in this audit log."
                />
            </div>
        </Card>

        <!-- Pagination -->
        <div class="mt-5">
            <Pagination
                :links="logs.links"
                :from="logs.from"
                :to="logs.to"
                :total="logs.total"
            />
        </div>
    </AuthenticatedLayout>
</template>
