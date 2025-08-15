<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    products: Object,
    categories: {
        type: Array,
        default: () => []
    },
    brands: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: '',
            brand: '',
            min_price: '',
            max_price: '',
            condition: '',
            sort_by: 'latest',
            per_page: 12
        })
    }
});

// Refs for form inputs
const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const brand = ref(props.filters.brand || '');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');
const condition = ref(props.filters.condition || '');
const sortBy = ref(props.filters.sort_by || 'latest');
const perPage = ref(props.filters.per_page || 12);

// Watch for filter changes and update URL
const updateFilters = debounce(() => {
    router.get(route('welcome'), {
        search: search.value,
        category: category.value,
        brand: brand.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        condition: condition.value,
        sort_by: sortBy.value,
        per_page: perPage.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 500);

// Watch all filter values
watch([search, category, brand, minPrice, maxPrice, condition, sortBy, perPage], () => {
    updateFilters();
});

// Format price to display
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(price);
};

// Toggle wishlist
const toggleWishlist = (productId) => {
    router.post(route('wishlist.toggle', productId), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Update UI accordingly
        }
    });
};
</script>

<template>
    <Head title="Welcome to Classified Ads" />

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <Link :href="route('welcome')" class="text-xl font-bold text-gray-900">
                        Classified Ads
                    </Link>
                </div>
                <div class="flex items-center space-x-4">
                    <Link v-if="!$page.props.auth.user" :href="route('login')"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Log in
                    </Link>
                    <Link v-if="canRegister && !$page.props.auth.user" :href="route('register')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Register
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="route('logout')" method="post" as="button"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Log out
                    </Link>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Filters Sidebar -->
                <div class="w-full md:w-64 flex-shrink-0">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-medium text-gray-900 mb-4">Filters</h3>
                        
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" v-model="search" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Brand Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <select v-model="brand" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Brands</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price Range</label>
                            <div class="flex space-x-2">
                                <input type="number" v-model="minPrice" placeholder="Min" 
                                    class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="number" v-model="maxPrice" placeholder="Max"
                                    class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Condition Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                            <select v-model="condition" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Conditions</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select v-model="sortBy" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="latest">Latest</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="name_asc">Name: A to Z</option>
                                <option value="name_desc">Name: Z to A</option>
                            </select>
                        </div>

                        <!-- Items Per Page -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items per page</label>
                            <select v-model="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="12">12</option>
                                <option value="24">24</option>
                                <option value="48">48</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1">
                    <!-- Results Count -->
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-medium">{{ products.from || 0 }}</span> to 
                            <span class="font-medium">{{ products.to || 0 }}</span> of 
                            <span class="font-medium">{{ products.total || 0 }}</span> results
                        </p>
                    </div>

                    <!-- Product Grid -->
                    <div v-if="products.data && products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="product in products.data" :key="product.id" class="bg-white rounded-lg shadow overflow-hidden">
                            <!-- Product Image -->
                            <div class="relative">
                                <Link :href="route('products.show', product.slug)" class="block">
                                    <img :src="product.media[0]?.original_url || 'https://placehold.co/600x400/ffffff/e5e7eb?text=No+Image'" 
                                        :alt="product.name"
                                        class="w-full h-48 object-cover">
                                </Link>
                                <!-- Wishlist Button -->
                                <div class="absolute top-2 right-2">
                                    <div class="relative group">
                                        <!-- Wishlist Button for Logged-in Users -->
                                        <button v-if="$page.props.auth.user"
                                            @click="toggleWishlist(product.id)"
                                            class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 focus:outline-none transition-colors"
                                            :class="{ 'text-red-500 hover:text-red-600': product.is_in_wishlist, 'text-gray-400 hover:text-gray-500': !product.is_in_wishlist }"
                                            :title="product.is_in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                class="h-6 w-6" 
                                                :class="{ 'fill-current': product.is_in_wishlist }" 
                                                fill="none" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor">
                                                <path stroke-linecap="round" 
                                                    stroke-linejoin="round" 
                                                    stroke-width="2" 
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Wishlist Button for Guests -->
                                        <div v-else class="relative">
                                            <button class="p-2 bg-white rounded-full shadow-md text-gray-400 cursor-default transition-colors hover:text-green-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" 
                                                    class="h-6 w-6" 
                                                    fill="none" 
                                                    viewBox="0 0 24 24" 
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" 
                                                        stroke-linejoin="round" 
                                                        stroke-width="2" 
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sale Badge -->
                                <span v-if="product.sale_price" 
                                    class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                                    SALE
                                </span>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <Link :href="route('products.show', product.slug)" class="hover:text-blue-600">
                                                {{ product.name }}
                                            </Link>
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ product.category?.name }}
                                            <span v-if="product.brand" class="ml-2">• {{ product.brand.name }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p v-if="product.sale_price" class="text-gray-500 text-sm line-through">
                                            {{ formatPrice(product.price) }}
                                        </p>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ formatPrice(product.sale_price || product.price) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-between items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                        :class="{
                                            'bg-green-100 text-green-800': product.condition === 'new',
                                            'bg-yellow-100 text-yellow-800': product.condition === 'used',
                                            'bg-blue-100 text-blue-800': product.condition === 'refurbished'
                                        }">
                                        {{ product.condition }}
                                    </span>
                                    <Link :href="route('products.show', product.slug)" 
                                        class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div v-else class="bg-white rounded-lg shadow p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">No products found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                        <div class="mt-6">
                            <button @click="resetFilters" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Reset Filters
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-8">
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-lg">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="products.prev_page_url" :href="products.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </Link>
                                <Link v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                    Previous
                                </Link>
                                <Link v-if="products.next_page_url" :href="products.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </Link>
                                <Link v-else class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ products.from || 0 }}</span> to 
                                        <span class="font-medium">{{ products.to || 0 }}</span> of 
                                        <span class="font-medium">{{ products.total || 0 }}</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <Link v-if="products.prev_page_url" :href="products.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <Link href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed" aria-disabled="true">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        
                                        <template v-for="(link, index) in products.links" :key="index">
                                            <Link v-if="link.url && link.label != '&laquo; Previous' && link.label != 'Next &raquo;'" 
                                                :href="link.url" 
                                                :class="{
                                                    'z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium': link.active,
                                                    'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium': !link.active
                                                }" 
                                                v-html="link.label"
                                                :aria-current="link.active ? 'page' : undefined">
                                            </Link>
                                        </template>
                                        
                                        <Link v-if="products.next_page_url" :href="products.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <Link href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed" aria-disabled="true">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add any custom styles here */
</style>
