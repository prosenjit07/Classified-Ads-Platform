<template>
    <div>
        <select
            :id="id || name"
            :name="name"
            :value="modelValue"
            :class="{
                'border-red-500 focus:border-red-500 focus:ring-red-500': !!error,
                'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500': !error,
                'mt-1 block w-full rounded-md shadow-sm': true,
                'opacity-50 bg-gray-100': disabled
            }"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            v-bind="$attrs"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
            <option 
                v-for="option in options" 
                :key="getOptionValue(option, optionValue)" 
                :value="getOptionValue(option, optionValue)"
            >
                {{ getOptionLabel(option, optionLabel) }}
            </option>
        </select>
        <InputError v-if="error" :message="error" class="mt-1" />
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Object],
        default: '',
    },
    id: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    optionLabel: {
        type: [String, Function],
        default: 'label',
    },
    optionValue: {
        type: [String, Function],
        default: 'value',
    },
    placeholder: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const getOptionLabel = (option, labelPath) => {
    if (typeof labelPath === 'function') {
        return labelPath(option);
    }
    return option[labelPath] || option;
};

const getOptionValue = (option, valuePath) => {
    if (typeof valuePath === 'function') {
        return valuePath(option);
    }
    return option[valuePath] !== undefined ? option[valuePath] : option;
};
</script>
