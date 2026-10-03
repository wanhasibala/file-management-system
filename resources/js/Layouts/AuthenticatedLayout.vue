<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';
import Badge from '@/Components/UI/Badge.vue';
import {
    LayoutDashboard,
    Folder,
    Files,
    Building2,
    History,
    Sun,
    Moon,
    User as UserIcon,
    Shield,
    Eye,
} from 'lucide-vue-next';

const page = usePage();
const showingNavigationDropdown = ref(false);
const isDark = ref(false);

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors">
        <ToastNotification />

        <!-- Top Navigation Bar -->
        <nav class="sticky top-0 z-40 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md border-b border-gray-200/80 dark:border-gray-700">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo />
                            </Link>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-1">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                <span class="flex items-center gap-1.5">
                                    <LayoutDashboard class="w-4 h-4" />
                                    <span>Dashboard</span>
                                </span>
                            </NavLink>

                            <NavLink :href="route('folders.index')" :active="route().current('folders.*')">
                                <span class="flex items-center gap-1.5">
                                    <Folder class="w-4 h-4" />
                                    <span>Folders</span>
                                </span>
                            </NavLink>

                            <NavLink :href="route('files.index')" :active="route().current('files.*')">
                                <span class="flex items-center gap-1.5">
                                    <Files class="w-4 h-4" />
                                    <span>All Files</span>
                                </span>
                            </NavLink>

                            <NavLink :href="route('departments.index')" :active="route().current('departments.*')">
                                <span class="flex items-center gap-1.5">
                                    <Building2 class="w-4 h-4" />
                                    <span>Departments</span>
                                </span>
                            </NavLink>

                            <NavLink
                                v-if="$page.props.auth.user?.is_admin"
                                :href="route('activity-logs.index')"
                                :active="route().current('activity-logs.*')"
                            >
                                <span class="flex items-center gap-1.5">
                                    <History class="w-4 h-4" />
                                    <span>Audit Logs</span>
                                </span>
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Bar: Dark Mode & User Profile -->
                    <div class="hidden sm:flex sm:items-center sm:gap-3">
                        <!-- Dark Mode Toggle Button -->
                        <button
                            type="button"
                            @click="toggleDarkMode"
                            class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                            :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        >
                            <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
                            <Moon v-else class="w-5 h-5 text-gray-600" />
                        </button>

                        <!-- User Profile & Role Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="56">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="flex items-center gap-2.5 p-1.5 pl-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
                                    >
                                        <div class="flex flex-col text-right">
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white leading-tight">
                                                {{ $page.props.auth.user.name }}
                                            </span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ $page.props.auth.user.email }}
                                            </span>
                                        </div>

                                        <Badge
                                            :variant="$page.props.auth.user?.is_admin ? 'rose' : 'blue'"
                                            size="xs"
                                        >
                                            <Shield v-if="$page.props.auth.user?.is_admin" class="w-3 h-3 mr-0.5" />
                                            <Eye v-else class="w-3 h-3 mr-0.5" />
                                            {{ $page.props.auth.user?.is_admin ? 'Admin' : 'Viewer' }}
                                        </Badge>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 text-xs border-b border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                        Role: <span class="font-semibold uppercase">{{ $page.props.auth.user?.role }}</span>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile Settings
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile Hamburger -->
                    <div class="flex items-center gap-2 sm:hidden">
                        <button
                            type="button"
                            @click="toggleDarkMode"
                            class="p-2 rounded-lg text-gray-500 dark:text-gray-400"
                        >
                            <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
                            <Moon v-else class="w-5 h-5" />
                        </button>

                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Mobile Navigation Menu -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-1 py-2 px-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('folders.index')" :active="route().current('folders.*')">
                        Folders
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('files.index')" :active="route().current('files.*')">
                        All Files
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('departments.index')" :active="route().current('departments.*')">
                        Departments
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        v-if="$page.props.auth.user?.is_admin"
                        :href="route('activity-logs.index')"
                        :active="route().current('activity-logs.*')"
                    >
                        Audit Logs
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pb-3 pt-4 px-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                        <Badge :variant="$page.props.auth.user?.is_admin ? 'rose' : 'blue'" size="xs">
                            {{ $page.props.auth.user?.is_admin ? 'Admin' : 'Viewer' }}
                        </Badge>
                    </div>

                    <div class="space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header (Optional) -->
        <header v-if="$slots.header" class="bg-white dark:bg-gray-800 border-b border-gray-200/80 dark:border-gray-700/80 py-4 shadow-xs">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Page Content -->
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <slot />
        </main>
    </div>
</template>
