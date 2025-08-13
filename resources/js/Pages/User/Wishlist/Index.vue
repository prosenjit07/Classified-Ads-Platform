<template>
  <AppLayout title="My Wishlist">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          My Wishlist
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('welcome')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            Continue Shopping
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="wishlistItems.data.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <WishlistItem 
                v-for="item in wishlistItems.data" 
                :key="item.id" 
                :item="item" 
                @remove="removeFromWishlist"
                @add-to-cart="addToCart"
              />
            </div>
            
            <!-- Pagination -->
            <div v-if="wishlistItems.last_page > 1" class="mt-8">
              <Pagination :links="wishlistItems.links" />
            </div>
          </div>
        </div>
        
        <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Your wishlist is empty</h3>
            <p class="mt-1 text-gray-500">You haven't added any products to your wishlist yet.</p>
            <div class="mt-6">
              <Link 
                :href="route('welcome')" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Browse Products
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import WishlistItem from '@/Components/WishlistItem.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  wishlistItems: {
    type: Object,
    required: true
  }
});

const removeFromWishlist = (item) => {
  if (confirm('Are you sure you want to remove this item from your wishlist?')) {
    router.delete(route('wishlist.destroy', item.id), {
      preserveScroll: true,
      onSuccess: () => {
        // The page will be refreshed automatically by Inertia
      }
    });
  }
};

const addToCart = (product) => {
  // Implement add to cart functionality
  console.log('Add to cart:', product);
  // You can dispatch an event or make an API call to add the product to cart
};
</script>