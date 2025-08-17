<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    category: Object,
    parentCategories: Array,
    fieldTypes: Array,
});

const form = useForm({
    name: props.category.name,
    parent_id: props.category.parent_id,
    description: props.category.description,
    status: props.category.status,
    order: props.category.order,
    meta_title: props.category.meta_title || '',
    meta_description: props.category.meta_description || '',
    form_fields: props.category.form_fields || [],
});

const newField = ref({
    label: '',
    name: '',
    type: 'text',
    required: false,
    options: '',
});


const submit = () => {
    form.put(route('admin.categories.update', { id: props.category.id }), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Update error:', errors);
        },
        onSuccess: () => {
            console.log('Category updated successfully');
        }
    });
};

const addField = () => {
    if (!newField.value.label || !newField.value.name) return;
    
    const field = { ...newField.value };
    
    if (['select', 'radio', 'checkbox'].includes(field.type) && field.options) {
        field.options = field.options.split(',').map(opt => opt.trim()).filter(opt => opt);
    } else {
        field.options = [];
    }
    
    form.form_fields.push(field);
    newField.value = { label: '', name: '', type: 'text', required: false, options: '' };
};

const removeField = (index) => {
    form.form_fields.splice(index, 1);
};
</script>

<template>
    <Head :title="`Edit Category: ${props.category.name}`" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2>Edit Category: {{ props.category.name }}</h2>
                <div class="space-x-2">
                    <Link :href="route('admin.categories.index')" class="btn">
                        Back to Categories
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="mb-8">
                                <h3>Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label>Name <span class="text-red-500">*</span></label>
                                        <input type="text" v-model="form.name" required />
                                    </div>


                                    <!-- Parent Category -->
                                    <div>
                                        <label>Parent Category</label>
                                        <select v-model="form.parent_id">
                                            <option :value="null">No Parent</option>
                                            <option 
                                                v-for="category in parentCategories" 
                                                :key="category.id" 
                                                :value="category.id"
                                                :disabled="category.id === props.category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center">
                                        <input type="checkbox" v-model="form.status" />
                                        <span class="ml-2">Active</span>
                                    </div>

                                    <!-- Order -->
                                    <div>
                                        <label>Order</label>
                                        <input type="number" v-model="form.order" min="0" />
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label>Description</label>
                                        <textarea v-model="form.description" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Form Fields -->
                            <div class="mb-8">
                                <h3>Dynamic Form Fields</h3>
                                
                                <!-- Add New Field -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Field</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Label</label>
                                            <input
                                                type="text"
                                                v-model="newField.label"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                placeholder="e.g. Color"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Name</label>
                                            <input
                                                type="text"
                                                v-model="newField.name"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                placeholder="e.g. color"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Type</label>
                                            <select
                                                v-model="newField.type"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            >
                                                <option v-for="type in fieldTypes" :key="type.value" :value="type.value">
                                                    {{ type.label }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex items-end">
                                            <button
                                                type="button"
                                                @click="addField"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            >
                                                Add Field
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Field Options -->
                                    <div v-if="['select', 'radio', 'checkbox'].includes(newField.type)" class="mt-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Options (comma-separated)
                                        </label>
                                        <input
                                            type="text"
                                            v-model="newField.options"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            placeholder="e.g. Red, Blue, Green"
                                        />
                                    </div>

                                    <div class="mt-3 flex items-center">
                                        <input
                                            type="checkbox"
                                            id="required"
                                            v-model="newField.required"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label for="required" class="ml-2 block text-sm text-gray-700">
                                            Required field
                                        </label>
                                    </div>
                                </div>

                                <!-- Current Fields -->
                                <div v-if="form.form_fields.length > 0" class="space-y-4">
                                    <div v-for="(field, index) in form.form_fields" :key="index" class="bg-white border rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="font-medium">{{ field.label }}</span>
                                                <span class="ml-2 text-xs text-gray-500">{{ field.name }}</span>
                                                <div class="mt-1 text-xs text-gray-500">
                                                    <span class="capitalize">{{ field.type }}</span>
                                                    <span v-if="field.required" class="ml-2 text-red-500">* Required</span>
                                                </div>
                                                <div v-if="field.options && field.options.length > 0" class="mt-1">
                                                    <span class="text-xs text-gray-500">Options: {{ field.options.join(', ') }}</span>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeField(index)"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Base styles */
input[type="text"],
input[type="number"],
select,
textarea {
    @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

h2 {
    @apply text-xl font-semibold text-gray-800;
}

h3 {
    @apply text-lg font-medium text-gray-900 mb-4;
}

label {
    @apply block text-sm font-medium text-gray-700;
}
</style>
