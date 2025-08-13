<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array
});

const { props: pageProps } = usePage();
const isAuthenticated = computed(() => pageProps.auth?.user !== null);

const toggleWishlist = (productId) => {
    if (!isAuthenticated.value) {
        window.location.href = route('login');
        return;
    }
    
    // Toggle wishlist status
    axios.post(route('wishlist.toggle', productId))
        .then(response => {
            // Update the product's wishlist status
            props.product.is_in_wishlist = response.data.is_in_wishlist;
        })
        .catch(error => {
            console.error('Error updating wishlist:', error);
        });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(price);
};
</script>

<template>
    <Head :title="product.name" />

    <div class="bg-white">
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
                        <Link v-if="!isAuthenticated" :href="route('login')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Log in
                        </Link>
                        <Link v-if="!isAuthenticated" :href="route('register')"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Register
                        </Link>
                        <Link v-if="isAuthenticated" :href="route('dashboard')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Product Details -->
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
                <!-- Product images -->
                <div class="mt-10 lg:mt-0">
                    <div class="aspect-w-1 aspect-h-1">
                        <div class="w-full h-96 rounded-lg overflow-hidden">
                            <img :src="product.media[0]?.original_url || 'https://via.placeholder.com/600x600?text=No+Image'" 
                                :alt="product.name"
                                class="w-full h-full object-center object-cover">
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-4 gap-4">
                        <div v-for="(image, index) in product.media.slice(0, 4)" :key="index" 
                            class="h-24 rounded-md overflow-hidden">
                            <img :src="image.original_url" 
                                :alt="`${product.name} thumbnail ${index + 1}`"
                                class="w-full h-full object-cover cursor-pointer"
                                @click="currentImage = image.original_url">
                        </div>
                    </div>
                </div>

                <!-- Product info -->
                <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                                {{ product.name }}
                            </h1>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ product.category?.name }}
                                <span v-if="product.brand" class="ml-2">• {{ product.brand.name }}</span>
                            </p>
                        </div>
                        <!-- Wishlist Button (Visible only to logged-in users) -->
                        <div class="relative group">
                            <button v-if="isAuthenticated"
                                @click="toggleWishlist(product.id)"
                                class="ml-4 p-2 rounded-full transition-colors"
                                :class="{ 
                                    'text-red-500 hover:text-red-600': product.is_in_wishlist, 
                                    'text-gray-400 hover:text-gray-500': !product.is_in_wishlist 
                                }"
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
                            
                            <!-- Login Prompt (Shown on hover when not logged in) -->
                            <div v-else class="relative">
                                <button class="ml-4 p-2 rounded-full text-gray-400 cursor-default group-hover:opacity-0 transition-opacity">
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
                                
                                <div class="absolute z-10 left-1/2 transform -translate-x-1/2 mt-2 px-2 w-48">
                                    <div class="bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-2">
                                        <p class="text-sm text-gray-700 text-center">
                                            <Link :href="route('login')" class="text-blue-600 hover:underline">
                                                Sign in
                                            </Link> to add to wishlist
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="sr-only">Description</h3>
                        <div class="text-base text-gray-700 space-y-6" v-html="product.description">
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-900">Highlights</h3>
                        <div class="mt-4">
                            <ul role="list" class="pl-4 list-disc text-sm space-y-2">
                                <li v-for="(detail, index) in product.details" :key="index" class="text-gray-600">
                                    <span class="text-gray-900 font-medium">{{ detail.key }}:</span> {{ detail.value }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h3 class="text-sm font-medium text-gray-900">Condition</h3>
                        <div class="mt-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                :class="{
                                    'bg-green-100 text-green-800': product.condition === 'new',
                                    'bg-yellow-100 text-yellow-800': product.condition === 'used',
                                    'bg-blue-100 text-blue-800': product.condition === 'refurbished'
                                }">
                                {{ product.condition }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center">
                            <p class="text-3xl font-bold text-gray-900">
                                <template v-if="product.sale_price">
                                    <span class="text-gray-500 line-through text-2xl mr-2">
                                        {{ formatPrice(product.price / 100) }}
                                    </span>
                                    <span class="text-red-600">
                                        {{ formatPrice(product.sale_price / 100) }}
                                    </span>
                                </template>
                                <template v-else>
                                    {{ formatPrice(product.price / 100) }}
                                </template>
                            </p>
                        </div>

                        <div class="mt-6">
                            <button type="button"
                                class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Contact Seller
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related products -->
            <section aria-labelledby="related-heading" class="mt-16 sm:mt-24">
                <h2 id="related-heading" class="text-lg font-medium text-gray-900">
                    You may also like
                </h2>

                <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" class="group relative">
                        <div class="w-full min-h-80 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
                            <img :src="relatedProduct.media[0]?.original_url || 'https://via.placeholder.com/300x300?text=No+Image'" 
                                :alt="relatedProduct.name"
                                class="w-full h-full object-center object-cover lg:w-full lg:h-full">
                        </div>
                        <div class="mt-4 flex justify-between">
                            <div>
                                <h3 class="text-sm text-gray-700">
                                    <Link :href="route('products.show', relatedProduct.slug)">
                                        <span aria-hidden="true" class="absolute inset-0" />
                                        {{ relatedProduct.name }}
                                    </Link>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ relatedProduct.category?.name }}
                                </p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                <template v-if="relatedProduct.sale_price">
                                    <span class="text-gray-500 line-through text-xs mr-1">
                                        {{ formatPrice(relatedProduct.price / 100) }}
                                    </span>
                                    <span class="text-red-600">
                                        {{ formatPrice(relatedProduct.sale_price / 100) }}
                                    </span>
                                </template>
                                <template v-else>
                                    {{ formatPrice(relatedProduct.price / 100) }}
                                </template>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
