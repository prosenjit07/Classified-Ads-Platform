<script setup>
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
});

const emit = defineEmits(['update:modelValue']);
const model = defineModel({
    type: [String, Number],
    required: true,
});

const input = ref(null);

const inputValue = computed({
    get() {
        return model.value === null || model.value === undefined ? '' : model.value.toString();
    },
    set(value) {
        if (props.type === 'number') {
            const numValue = value === '' ? null : Number(value);
            model.value = isNaN(numValue) ? value : numValue;
        } else {
            model.value = value === '' ? null : value;
        }
    },
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :type="type"
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        v-model="inputValue"
        ref="input"
    />
</template>
