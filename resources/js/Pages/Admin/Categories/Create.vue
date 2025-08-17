<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    parentCategories: Array,
    fieldTypes: Array,
});

const form = useForm({
    name: '',
    parent_id: null,
    description: '',
    status: true,
    order: 0,
    meta_title: '',
    meta_description: '',
    form_fields: [],
});

const newField = ref({
    label: '',
    name: '',
    type: 'text',
    required: false,
    options: '',
});


const submit = () => {
    form.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log("okk");
            // This will be handled by the controller redirect
            // The page will refresh with the updated categories list
        },
        onError: (errors) => {
            console.error('Error creating category:', errors);
        },
        onFinish: () => {
            form.reset();
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
    <Head title="Create Category" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">Create New Category</h2>
                <Link 
                    :href="route('admin.categories.index')"
                    class="px-4 py-2 bg-gray-300 rounded-md font-semibold text-xs text-gray-800 uppercase hover:bg-gray-400"
                >
                    Back to Categories
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <p class="mt-1 text-sm text-red-600" v-if="form.errors.name">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>


                                    <!-- Parent Category -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Parent Category
                                        </label>
                                        <select
                                            v-model="form.parent_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option :value="null">No Parent</option>
                                            <option 
                                                v-for="category in parentCategories" 
                                                :key="category.id" 
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.status"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Active</span>
                                    </div>

                                    <!-- Order -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Order
                                        </label>
                                        <input
                                            type="number"
                                            v-model="form.order"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Description
                                        </label>
                                        <textarea
                                            v-model="form.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Form Fields -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Dynamic Form Fields</h3>
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Label</label>
                                            <input v-model="newField.label" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Name</label>
                                            <input v-model="newField.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Type</label>
                                            <select v-model="newField.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                                    
                                    <div v-if="['select', 'radio', 'checkbox'].includes(newField.type)" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">Options (comma-separated)</label>
                                        <input v-model="newField.options" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    
                                    <div class="mt-2 flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="newField.required"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm"
                                        >
                                        <span class="ml-2 text-sm text-gray-600">Required</span>
                                    </div>
                                </div>

                                <!-- Added Fields -->
                                <div v-if="form.form_fields.length > 0" class="space-y-4">
                                    <div v-for="(field, index) in form.form_fields" :key="index" class="bg-white p-4 border rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="font-medium">{{ field.label }}</span>
                                                <span class="ml-2 text-sm text-gray-500">({{ field.type }})</span>
                                                <span v-if="field.required" class="ml-2 text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full">
                                                    Required
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeField(index)"
                                                class="text-red-600 hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div v-if="field.options.length > 0" class="mt-2 text-sm text-gray-600">
                                            Options: {{ field.options.join(', ') }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 text-gray-500">
                                    No fields added yet. Add fields that will be used for products in this category.
                                </div>
                            </div>

                            <!-- Meta Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Meta Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                                        <input
                                            v-model="form.meta_title"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea
                                            v-model="form.meta_description"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                                <Link
                                    :href="route('admin.categories.index')"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save Category</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
