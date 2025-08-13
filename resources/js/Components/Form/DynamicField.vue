<template>
  <div class="space-y-2">
    <label :for="fieldName" class="block text-sm font-medium text-gray-700">
      {{ field.label }}
      <span v-if="field.is_required" class="text-red-500">*</span>
      <span v-if="field.help_text" class="ml-1 text-gray-400 text-xs">
        ({{ field.help_text }})
      </span>
    </label>
    
    <!-- Text Input -->
    <input
      v-if="['text', 'number', 'email', 'url', 'date', 'datetime-local', 'color'].includes(field.type)"
      :id="fieldName"
      v-model="localValue"
      :type="field.type"
      :name="fieldName"
      :required="field.is_required"
      :class="[
        'mt-1 block w-full rounded-md border-gray-300 shadow-sm',
        'focus:border-indigo-500 focus:ring-indigo-500',
        'sm:text-sm',
        { 'border-red-300': error }
      ]"
      :step="field.type === 'number' ? 'any' : undefined"
      :min="field.type === 'number' ? '0' : undefined"
    />
    
    <!-- Textarea -->
    <textarea
      v-else-if="field.type === 'textarea'"
      :id="fieldName"
      v-model="localValue"
      :name="fieldName"
      :rows="3"
      :required="field.is_required"
      :class="[
        'mt-1 block w-full rounded-md border border-gray-300 shadow-sm',
        'focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        { 'border-red-300': error }
      ]"
    />
    
    <!-- Select Dropdown -->
    <select
      v-else-if="field.type === 'select'"
      :id="fieldName"
      v-model="localValue"
      :name="fieldName"
      :required="field.is_required"
      :class="[
        'mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm',
        'focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm',
        { 'border-red-300': error }
      ]"
    >
      <option v-if="!field.is_required" value="">Select an option</option>
      <option v-for="option in fieldOptions" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    
    <!-- Checkbox -->
    <div v-else-if="field.type === 'checkbox'" class="mt-1">
      <label class="inline-flex items-center">
        <input
          :id="fieldName"
          v-model="localValue"
          :name="fieldName"
          type="checkbox"
          :required="field.is_required"
          :class="[
            'h-4 w-4 rounded border-gray-300 text-indigo-600',
            'focus:ring-indigo-500',
            { 'border-red-300': error }
          ]"
        >
        <span v-if="fieldOptions.length === 1" class="ml-2 text-sm text-gray-700">
          {{ fieldOptions[0].label }}
        </span>
      </label>
      
      <!-- Checkbox Group -->
      <div v-if="fieldOptions.length > 1" class="mt-2 space-y-2">
        <div v-for="option in fieldOptions" :key="option.value" class="flex items-center">
          <input
            :id="`${fieldName}-${option.value}`"
            v-model="localValue"
            :value="option.value"
            :name="fieldName"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
          >
          <label :for="`${fieldName}-${option.value}`" class="ml-2 text-sm text-gray-700">
            {{ option.label }}
          </label>
        </div>
      </div>
    </div>
    
    <!-- Radio Buttons -->
    <div v-else-if="field.type === 'radio'" class="mt-1 space-y-2">
      <div v-for="option in fieldOptions" :key="option.value" class="flex items-center">
        <input
          :id="`${fieldName}-${option.value}`"
          v-model="localValue"
          :value="option.value"
          :name="fieldName"
          type="radio"
          :required="field.is_required"
          class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
        >
        <label :for="`${fieldName}-${option.value}`" class="ml-2 text-sm text-gray-700">
          {{ option.label }}
        </label>
      </div>
    </div>
    
    <!-- File Upload -->
    <div v-else-if="field.type === 'file'" class="mt-1">
      <div class="flex items-center">
        <input
          :id="fieldName"
          type="file"
          :name="fieldName"
          :required="field.is_required && !modelValue"
          :class="[
            'block w-full text-sm text-gray-500',
            'file:mr-4 file:py-2 file:px-4',
            'file:rounded-md file:border-0',
            'file:text-sm file:font-semibold',
            'file:bg-indigo-50 file:text-indigo-700',
            'hover:file:bg-indigo-100',
            { 'border-red-300': error }
          ]"
          @change="handleFileUpload"
        >
      </div>
      <p v-if="field.help_text" class="mt-1 text-xs text-gray-500">
        {{ field.help_text }}
      </p>
      <div v-if="modelValue" class="mt-2 flex items-center">
        <span class="text-sm text-gray-500">
          {{ typeof modelValue === 'string' ? modelValue.split('/').pop() : modelValue.name }}
        </span>
        <button
          type="button"
          class="ml-2 text-red-600 hover:text-red-800"
          @click="removeFile"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
    
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, Array, File, null],
    default: '',
  },
  field: {
    type: Object,
    required: true,
    validator: (field) => {
      return [
        'text', 'number', 'textarea', 'select', 'checkbox', 'radio', 'email',
        'url', 'date', 'datetime-local', 'color', 'file'
      ].includes(field.type);
    },
  },
  fieldName: {
    type: String,
    required: true,
  },
  error: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const localValue = ref(props.modelValue);

// Parse options from the field's options string
const fieldOptions = computed(() => {
  if (!props.field.options) return [];
  
  return props.field.options.split(',').map(option => {
    const [value, label] = option.split(':');
    return {
      value: (value || '').trim(),
      label: (label || value || '').trim(),
    };
  });
});

// Handle file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    emit('update:modelValue', file);
  }
};

// Remove file
const removeFile = () => {
  emit('update:modelValue', null);
};

// Watch for changes to localValue and emit update event
watch(localValue, (newValue) => {
  emit('update:modelValue', newValue);
});

// Watch for changes to modelValue from parent
watch(() => props.modelValue, (newValue) => {
  localValue.value = newValue;
});
</script>
