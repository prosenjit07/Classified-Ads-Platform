<template>
  <div class="group relative">
    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75">
      <Link :href="route('products.show', product.slug)">
        <img 
          :src="product.image || '/images/placeholder-product.png'" 
          :alt="product.name" 
          class="h-full w-full object-cover object-center"
        >
      </Link>
      <button 
        @click="$emit('toggle-wishlist', product)" 
        class="absolute top-2 right-2 p-1.5 bg-white rounded-full shadow-md hover:bg-gray-100 focus:outline-none transition-colors"
        :class="{ 'text-red-500': product.is_in_wishlist, 'text-gray-400': !product.is_in_wishlist }"
        :title="product.is_in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :fill="product.is_in_wishlist ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>
      <button 
        v-if="showAddToCart"
        @click="$emit('add-to-cart', product)" 
        class="absolute bottom-2 left-1/2 transform -translate-x-1/2 px-3 py-1 bg-white text-sm rounded-full shadow-md text-gray-700 hover:bg-gray-100 focus:outline-none opacity-0 group-hover:opacity-100 transition-opacity"
      >
        Add to Cart
      </button>
    </div>
    <div class="mt-2">
      <h3 class="text-sm text-gray-700">
        <Link :href="route('products.show', product.slug)" class="hover:text-indigo-600">
          {{ product.name }}
        </Link>
      </h3>
      <p class="mt-1 text-sm font-medium text-gray-900">
        {{ formatCurrency(product.sale_price || product.price) }}
        <span v-if="product.sale_price" class="ml-1 text-xs text-gray-500 line-through">
          {{ formatCurrency(product.price) }}
        </span>
      </p>
      <div v-if="showCategory && product.category" class="mt-1">
        <Link 
          :href="route('products.index', { category: product.category.slug })" 
          class="text-xs text-gray-500 hover:text-indigo-600"
        >
          {{ product.category.name }}
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  showAddToCart: {
    type: Boolean,
    default: true
  },
  showCategory: {
    type: Boolean,
    default: true
  }
});

const formatCurrency = (value) => {
  if (value === undefined || value === null) return '';
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value / 100); // Assuming prices are stored in cents
};

defineEmits(['toggle-wishlist', 'add-to-cart']);
</script>
