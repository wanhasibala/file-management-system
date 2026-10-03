<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/UI/Button.vue';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import DepartmentFormModal from '@/Components/Department/DepartmentFormModal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import {
    Building2,
    Plus,
    Edit2,
    Trash2,
    Files,
} from 'lucide-vue-next';

const props = defineProps({
    departments: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isAdmin = page.props.auth.user?.is_admin;

const showFormModal = ref(false);
const editingDepartment = ref(null);
const showDeleteModal = ref(false);
const departmentToDelete = ref(null);
const isDeleting = ref(false);

const openCreate = () => {
    editingDepartment.value = null;
    showFormModal.value = true;
};

const openEdit = (dept) => {
    editingDepartment.value = dept;
    showFormModal.value = true;
};

const confirmDelete = (dept) => {
    departmentToDelete.value = dept;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!departmentToDelete.value) return;
    isDeleting.value = true;
    router.delete(route('departments.destroy', departmentToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            departmentToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Department Management - File Management System" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <Building2 class="w-6 h-6 text-blue-600" />
                    <span>Department Management</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Manage corporate departments and metadata taxonomies for categorizing files.
                </p>
            </div>

            <div v-if="isAdmin">
                <Button variant="primary" @click="openCreate">
                    <template #icon>
                        <Plus class="w-4 h-4" />
                    </template>
                    Add Department
                </Button>
            </div>
        </div>

        <!-- Department Cards Grid -->
        <div v-if="departments && departments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <Card
                v-for="dept in departments"
                :key="dept.id"
                hoverable
                class="flex flex-col justify-between"
            >
                <template #header>
                    <div class="flex items-center justify-between w-full">
                        <Badge :variant="dept.color || 'blue'" size="sm" dot>
                            {{ dept.code }}
                        </Badge>

                        <div v-if="isAdmin" class="flex items-center gap-1">
                            <button
                                type="button"
                                @click="openEdit(dept)"
                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Edit Department"
                            >
                                <Edit2 class="w-3.5 h-3.5 text-amber-500" />
                            </button>
                            <button
                                type="button"
                                @click="confirmDelete(dept)"
                                class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Delete Department"
                            >
                                <Trash2 class="w-3.5 h-3.5 text-red-500" />
                            </button>
                        </div>
                    </div>
                </template>

                <div class="space-y-2 py-1">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        {{ dept.name }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ dept.description || 'No description provided for this department.' }}
                    </p>
                </div>

                <template #footer>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <Files class="w-4 h-4 text-blue-500" />
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ dept.files_count || 0 }}</span> documents
                        </span>
                        <span class="text-[11px] font-mono text-gray-400">ID #{{ dept.id }}</span>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Empty state -->
        <div v-else class="py-12">
            <EmptyState
                title="No departments configured"
                description="Create your first corporate department to classify uploaded files."
                action-text="Add Department"
                @action="openCreate"
            />
        </div>

        <!-- Modals -->
        <DepartmentFormModal
            v-if="isAdmin"
            :show="showFormModal"
            :department="editingDepartment"
            @close="showFormModal = false"
        />

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Department"
            :message="`Are you sure you want to delete department '${departmentToDelete?.name}'?`"
            confirm-text="Delete Department"
            :loading="isDeleting"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />
    </AuthenticatedLayout>
</template>
