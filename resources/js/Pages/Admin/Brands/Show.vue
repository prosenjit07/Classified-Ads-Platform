<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    brand: Object,
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <Head :title="`${brand.name} | Brand Details`" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">
                    {{ brand.name }}
                </h2>
                <div class="space-x-2">
                    <Link 
                        :href="route('admin.brands.edit', brand.id)" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit Brand
                    </Link>
                    <Link 
                        :href="route('admin.brands.index')" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                        Back to Brands
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="md:flex">
                            <!-- Brand Logo -->
                            <div class="md:w-1/4 mb-6 md:mb-0">
                                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                    <img 
                                        v-if="brand.logo" 
                                        :src="'/storage/' + brand.logo" 
                                        :alt="brand.name"
                                        class="h-48 w-auto object-contain"
                                    >
                                    <div v-else class="text-gray-400 text-center">
                                        <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="mt-2">No logo uploaded</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Brand Details -->
                            <div class="md:pl-8 md:w-3/4">
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Brand Information</h3>
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ brand.name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ brand.slug }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                            <dd class="mt-1">
                                                <span 
                                                    :class="{
                                                        'bg-green-100 text-green-800': brand.is_active,
                                                        'bg-red-100 text-red-800': !brand.is_active
                                                    }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                >
                                                    {{ brand.is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ brand.order || 'Not set' }}</dd>
                                        </div>
                                        <div v-if="brand.website" class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Website</dt>
                                            <dd class="mt-1 text-sm">
                                                <a 
                                                    :href="brand.website.startsWith('http') ? brand.website : 'https://' + brand.website" 
                                                    target="_blank" 
                                                    rel="noopener noreferrer"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    {{ brand.website }}
                                                </a>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Description -->
                                <div v-if="brand.description" class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                                    <div class="prose max-w-none text-gray-500">
                                        {{ brand.description }}
                                    </div>
                                </div>

                                <!-- Meta Information -->
                                <div class="border-t border-gray-200 pt-4 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Meta Information</h3>
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ brand.meta_title || 'Not set' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ brand.meta_description || 'Not set' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Timestamps -->
                                <div class="border-t border-gray-200 pt-4 mt-6">
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(brand.created_at) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(brand.updated_at) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
