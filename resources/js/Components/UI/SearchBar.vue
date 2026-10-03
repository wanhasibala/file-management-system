<script setup>
import { ref, watch } from 'vue';
import { Search, X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Search documents, files, folders...',
    },
    debounceMs: {
        type: Number,
        default: 300,
    },
});

const emit = defineEmits(['update:modelValue', 'search']);

const localValue = ref(props.modelValue);
let timer = null;

watch(
    () => props.modelValue,
    (newVal) => {
        localValue.value = newVal;
    }
);

const handleInput = (e) => {
    const val = e.target.value;
    localValue.value = val;
    emit('update:modelValue', val);

    clearTimeout(timer);
    timer = setTimeout(() => {
        emit('search', val);
    }, props.debounceMs);
};

const clear = () => {
    localValue.value = '';
    emit('update:modelValue', '');
    emit('search', '');
};
</script>

<template>
    <div class="relative w-full">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
            <Search class="w-4 h-4" />
        </div>

        <input
            type="text"
            :value="localValue"
            @input="handleInput"
            :placeholder="placeholder"
            class="w-full rounded-xl border border-gray-200 bg-white py-2 pl-10 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-blue-400 transition-all"
        />

        <button
            v-if="localValue"
            type="button"
            @click="clear"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
        >
            <X class="w-4 h-4" />
        </button>
    </div>
</template>
