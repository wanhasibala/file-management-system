<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/UI/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Shield, Eye, Lock } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const fillDemo = (role) => {
    if (role === 'admin') {
        form.email = 'admin@example.com';
        form.password = 'password';
    } else {
        form.email = 'viewer@example.com';
        form.password = 'password';
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Log In - Lion Group FMS" />

        <div class="mb-5 text-center">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Sign in to your account
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Lion Group File Management System
            </p>
        </div>

        <!-- Quick 1-Click Demo Login Bar -->
        <div class="mb-5 p-3.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-900/50">
            <p class="text-xs font-semibold text-blue-900 dark:text-blue-300 mb-2">
                Quick Demo Credentials:
            </p>
            <div class="grid grid-cols-2 gap-2">
                <button
                    type="button"
                    @click="fillDemo('admin')"
                    class="flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-rose-300 shadow-2xs transition-all cursor-pointer"
                >
                    <Shield class="w-3.5 h-3.5" />
                    <span>Administrator</span>
                </button>
                <button
                    type="button"
                    @click="fillDemo('viewer')"
                    class="flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 shadow-2xs transition-all cursor-pointer"
                >
                    <Eye class="w-3.5 h-3.5" />
                    <span>Viewer</span>
                </button>
            </div>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-emerald-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    placeholder="e.g. admin@example.com"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-2.5"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Sign in to Portal
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
