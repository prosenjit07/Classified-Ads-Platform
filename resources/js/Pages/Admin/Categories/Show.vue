<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    category: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`View Category: ${category.name}`" />
    
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">
                    View Category: {{ category.name }}
                </h2>
                <div class="space-x-2">
                    <Link 
                        :href="route('admin.categories.edit', category.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit
                    </Link>
                    <Link 
                        :href="route('admin.categories.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Back to Categories
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ category.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Slug</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ category.slug }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Parent Category</p>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ category.parent ? category.parent.name : 'None' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <span 
                                        :class="[
                                            category.status 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800',
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1'
                                        ]"
                                    >
                                        {{ category.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Order</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ category.order }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Description</p>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                        {{ category.description || 'No description provided.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Form Fields -->
                        <div v-if="category.form_fields && category.form_fields.length > 0" class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Custom Form Fields</h3>
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Label
                                            </th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                Type
                                            </th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                Required
                                            </th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                Options
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="(field, index) in category.form_fields" :key="index">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                {{ field.label }}
                                                <div class="text-gray-500 text-xs font-normal">
                                                    {{ field.name }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ field.type }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ field.required ? 'Yes' : 'No' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <template v-if="field.options && field.options.length > 0">
                                                    {{ field.options.join(', ') }}
                                                </template>
                                                <template v-else>
                                                    —
                                                </template>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Meta Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Meta Information</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Meta Title</p>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ category.meta_title || 'Not set' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Meta Description</p>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ category.meta_description || 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
