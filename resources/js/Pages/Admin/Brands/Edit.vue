<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    brand: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.brand.name,
    slug: props.brand.slug,
    description: props.brand.description || '',
    website: props.brand.website || '',
    is_active: props.brand.is_active,
    order: props.brand.order || 0,
    meta_title: props.brand.meta_title || '',
    meta_description: props.brand.meta_description || '',
    logo: null,
});

const preview = ref(
    props.brand.logo 
        ? (props.brand.logo.startsWith('http') ? props.brand.logo : `/storage/${props.brand.logo}`)
        : null
);
const showLogoDelete = ref(false);

const submit = () => {
    // Create a new form data object with all fields from the form
    const formData = {
        name: form.name,
        slug: form.slug,
        description: form.description || '',
        website: form.website || '',
        is_active: form.is_active,
        order: form.order || 0,
        meta_title: form.meta_title || '',
        meta_description: form.meta_description || ''
    };
    
    // Create FormData for file upload
    const formDataObj = new FormData();
    
    // Append all fields to FormData
    Object.entries(formData).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            formDataObj.append(key, value);
        }
    });
    
    // Handle logo - only include if it's a file
    if (form.logo && form.logo instanceof File) {
        formDataObj.append('logo', form.logo);
    }
    
    // Add delete_logo flag if set
    if (form.delete_logo) {
        formDataObj.append('delete_logo', '1');
    }
    
    // Submit the form using PUT directly
    form.put(route('admin.brands.update', props.brand.slug), formDataObj, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // Handle success if needed
        },
        onError: (errors) => {
            console.error('Error updating brand:', errors);
        }
    });
};

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target.result;
            showLogoDelete.value = true;
        };
        reader.readAsDataURL(file);
    }
};

const removeLogo = () => {
    form.logo = null;
    preview.value = null;
    showLogoDelete.value = false;
    
    // Set flag to indicate logo should be removed on the server
    form.delete_logo = true;
};

const deleteBrand = () => {
    if (confirm('Are you sure you want to delete this brand? This action cannot be undone.')) {
        router.delete(route('admin.brands.destroy', props.brand.slug));
    }
};

// Generate slug from name
watch(() => form.name, (newName) => {
    if (!form.isDirty || !newName) return;
    
    // Only update slug if it's empty or matches the auto-generated version of the previous name
    const autoSlug = props.brand.name
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
        
    if (!form.slug || form.slug === autoSlug) {
        form.slug = newName
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});
</script>

<template>
    <Head :title="`Edit ${brand.name} | Brand`" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">
                    Edit Brand: {{ brand.name }}
                </h2>
                <div class="space-x-2">
                    <Link 
                        :href="route('admin.brands.show', brand.id)" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                        View Brand
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
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Logo Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Brand Logo</label>
                                    <div class="mt-1 flex items-center">
                                        <div class="relative">
                                            <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100">
                                                <img 
                                                    v-if="preview" 
                                                    :src="preview" 
                                                    alt="Preview" 
                                                    class="h-full w-full object-contain"
                                                >
                                                <svg 
                                                    v-else 
                                                    class="h-full w-full text-gray-300" 
                                                    fill="currentColor" 
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <button 
                                                v-if="showLogoDelete"
                                                @click.prevent="removeLogo"
                                                type="button"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                title="Remove logo"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <label class="ml-5">
                                            <div class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
                                                {{ preview ? 'Change' : 'Upload' }}
                                                <input type="file" class="hidden" @change="onFileChange" accept="image/*">
                                            </div>
                                        </label>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500" id="logo_help">Square logo with transparent background works best.</p>
                                    <p v-if="form.errors.logo" class="mt-2 text-sm text-red-600">{{ form.errors.logo }}</p>
                                </div>

                                <!-- Basic Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="name" 
                                            v-model="form.name" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required
                                        >
                                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                                    </div>

                                    <!-- Slug -->
                                    <div>
                                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="slug" 
                                            v-model="form.slug" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required
                                        >
                                        <p v-if="form.errors.slug" class="mt-2 text-sm text-red-600">{{ form.errors.slug }}</p>
                                    </div>

                                    <!-- Website -->
                                    <div>
                                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                https://
                                            </span>
                                            <input 
                                                type="text" 
                                                id="website" 
                                                v-model="form.website" 
                                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                placeholder="www.example.com"
                                            >
                                        </div>
                                        <p v-if="form.errors.website" class="mt-2 text-sm text-red-600">{{ form.errors.website }}</p>
                                    </div>

                                    <!-- Order -->
                                    <div>
                                        <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                                        <input 
                                            type="number" 
                                            id="order" 
                                            v-model.number="form.order" 
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        >
                                        <p v-if="form.errors.order" class="mt-2 text-sm text-red-600">{{ form.errors.order }}</p>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-end">
                                        <div class="flex items-center h-5">
                                            <input 
                                                id="is_active" 
                                                v-model="form.is_active" 
                                                type="checkbox" 
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                                            <p class="text-gray-500">Brand will be visible on the website</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <div class="mt-1">
                                        <textarea 
                                            id="description" 
                                            v-model="form.description" 
                                            rows="3" 
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        ></textarea>
                                    </div>
                                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                                </div>

                                <!-- SEO Section -->
                                <div class="border-t border-gray-200 pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
                                    
                                    <!-- Meta Title -->
                                    <div class="mb-4">
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input 
                                                type="text" 
                                                id="meta_title" 
                                                v-model="form.meta_title" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                :maxlength="60"
                                            >
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-xs">{{ form.meta_title ? form.meta_title.length : 0 }}/60</span>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">Recommended: 50-60 characters</p>
                                        <p v-if="form.errors.meta_title" class="mt-2 text-sm text-red-600">{{ form.errors.meta_title }}</p>
                                    </div>

                                    <!-- Meta Description -->
                                    <div>
                                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <div class="mt-1">
                                            <textarea 
                                                id="meta_description" 
                                                v-model="form.meta_description" 
                                                rows="3" 
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                :maxlength="160"
                                            ></textarea>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">Recommended: 150-160 characters. {{ form.meta_description ? form.meta_description.length : 0 }}/160</p>
                                        <p v-if="form.errors.meta_description" class="mt-2 text-sm text-red-600">{{ form.errors.meta_description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="pt-5">
                                <div class="flex justify-between">
                                    <button 
                                        type="button" 
                                        @click="deleteBrand"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        :disabled="form.processing"
                                    >
                                        Delete Brand
                                    </button>
                                    <div class="flex">
                                        <Link 
                                            :href="route('admin.brands.index')" 
                                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Cancel
                                        </Link>
                                        <button 
                                            type="submit" 
                                            :disabled="form.processing"
                                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            :class="{ 'opacity-75': form.processing }"
                                        >
                                            <span v-if="form.processing">Saving...</span>
                                            <span v-else>Save Changes</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
