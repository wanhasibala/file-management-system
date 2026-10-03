<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
let timer = null;

const showToast = (msg, toastType = 'success') => {
    message.value = msg;
    type.value = toastType;
    visible.value = true;

    clearTimeout(timer);
    timer = setTimeout(() => {
        visible.value = false;
    }, 4500);
};

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;

        if (flash.success) {
            showToast(flash.success, 'success');
        } else if (flash.error) {
            showToast(flash.error, 'error');
        } else if (flash.warning) {
            showToast(flash.warning, 'warning');
        } else if (flash.info) {
            showToast(flash.info, 'info');
        }
    },
    { immediate: true, deep: true }
);

const close = () => {
    visible.value = false;
    clearTimeout(timer);
};
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            class="fixed bottom-5 right-5 z-50 max-w-md w-full shadow-lg rounded-xl pointer-events-auto border overflow-hidden p-4 flex items-start gap-3 transition-all"
            :class="{
                'bg-emerald-50 border-emerald-200 text-emerald-900 dark:bg-emerald-950/90 dark:border-emerald-800 dark:text-emerald-100': type === 'success',
                'bg-red-50 border-red-200 text-red-900 dark:bg-red-950/90 dark:border-red-800 dark:text-red-100': type === 'error',
                'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-950/90 dark:border-amber-800 dark:text-amber-100': type === 'warning',
                'bg-blue-50 border-blue-200 text-blue-900 dark:bg-blue-950/90 dark:border-blue-800 dark:text-blue-100': type === 'info',
            }"
        >
            <div class="shrink-0 mt-0.5">
                <CheckCircle v-if="type === 'success'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <AlertCircle v-else-if="type === 'error'" class="w-5 h-5 text-red-600 dark:text-red-400" />
                <AlertTriangle v-else-if="type === 'warning'" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                <Info v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>

            <div class="flex-1 text-sm font-medium">
                {{ message }}
            </div>

            <button
                type="button"
                @click="close"
                class="shrink-0 p-1 rounded-md opacity-70 hover:opacity-100 transition-opacity"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </Transition>
</template>
