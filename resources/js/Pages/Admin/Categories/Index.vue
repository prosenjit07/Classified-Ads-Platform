<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => []
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const categories = ref([...props.categories]);
const isLoading = ref(false);

// Function to fetch categories
const fetchCategories = async () => {
    try {
        isLoading.value = true;
        const response = await fetch(route('admin.categories.index'));
        if (!response.ok) throw new Error('Failed to fetch categories');
        const data = await response.json();
        categories.value = data.categories || [];
    } catch (error) {
        console.error('Error fetching categories:', error);
    } finally {
        isLoading.value = false;
    }
};

// Fetch categories when component is mounted
onMounted(() => {
    if (categories.value.length === 0) {
        fetchCategories();
    }
});

// Watch for changes in props
watch(() => props.categories, (newCategories) => {
    if (newCategories && newCategories.length > 0) {
        categories.value = [...newCategories];
    }
}, { immediate: true, deep: true });

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        router.delete(route('admin.categories.destroy', { category: id }), {
            preserveScroll: true,
            onSuccess: () => {
                // Refresh the categories list after deletion
                fetchCategories();
            },
            onError: (errors) => {
                console.error('Error deleting category:', errors);
                let errorMessage = 'Failed to delete category. ';
                if (errors?.message) {
                    errorMessage += errors.message;
                } else if (errors?.error) {
                    errorMessage += errors.error;
                }
                alert(errorMessage);
            }
        });
    }
};

const renderCategories = (cats, level = 0) => {
    return cats.map(category => ({
        ...category,
        level,
        children: category.children ? renderCategories(category.children, level + 1) : []
    }));
};

// Make flattenedCategories a computed property to react to changes
const flattenedCategories = computed(() => renderCategories(categories.value));
</script>

<template>
    <Head title="Manage Categories" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Categories
                </h2>
                <Link 
                    :href="route('admin.categories.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Add New Category
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Loading State -->
                        <div v-if="isLoading" class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                            Loading categories...
                        </div>

                        <!-- Error State -->
                        <div v-else-if="categories.length === 0" class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                            No categories found. Add your first category to get started.
                        </div>

                        <!-- Flash Messages -->
                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>
                        <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $page.props.flash.error }}
                        </div>
                        <div v-if="Object.keys(errors).length > 0" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <div v-for="(error, key) in errors" :key="key">
                                {{ error }}
                            </div>
                        </div>

                        <!-- Categories Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Slug
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="category in flattenedCategories" :key="category.id" :class="{ 'bg-gray-50': category.level > 0 }">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div :class="{ 'pl-4': category.level > 0 }" :style="`padding-left: ${category.level * 1.5}rem`">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ category.name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ category.slug }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                category.status 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800',
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                            ]">
                                                {{ category.status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ category.order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <Link 
                                                    :href="route('admin.categories.edit', { category: category.id })"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3 focus:outline-none"
                                                    :title="'Edit ' + category.name"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    type="button"
                                                    @click="deleteCategory(category.id)"
                                                    class="text-red-600 hover:text-red-900 focus:outline-none"
                                                    :disabled="category.children && category.children.length > 0"
                                                    :title="category.children && category.children.length > 0 ? 'Cannot delete category with subcategories' : 'Delete category'"
                                                >
                                                    <span :class="{ 'opacity-50 cursor-not-allowed': category.children && category.children.length > 0 }">
                                                        Delete
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="flattenedCategories.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No categories found. Create your first category.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>