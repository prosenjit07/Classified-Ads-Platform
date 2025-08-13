<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center justify-center px-4 py-2 border rounded-md font-medium text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
            variantClasses,
            {
                'opacity-50 cursor-not-allowed': disabled,
                'w-full': block,
            },
            sizeClasses,
            extraClasses
        ]"
        :disabled="disabled"
        v-bind="$attrs"
    >
        <slot />
    </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning', 'info', 'light', 'dark'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    block: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    const variants = {
        primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 border-transparent',
        secondary: 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-500 border-transparent',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border-transparent',
        success: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 border-transparent',
        warning: 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 border-transparent',
        info: 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500 border-transparent',
        light: 'bg-gray-100 text-gray-800 hover:bg-gray-200 focus:ring-gray-300 border-gray-300',
        dark: 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-500 border-transparent',
    };
    return variants[props.variant] || variants.primary;
});

const sizeClasses = computed(() => {
    const sizes = {
        xs: 'px-2.5 py-1.5 text-xs',
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-2 text-sm',
        lg: 'px-4 py-2 text-base',
        xl: 'px-6 py-3 text-base',
    };
    return sizes[props.size] || sizes.md;
});

const extraClasses = computed(() => {
    return {
        'rounded-full': props.rounded,
    };
});
</script>
