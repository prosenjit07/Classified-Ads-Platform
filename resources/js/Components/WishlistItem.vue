<template>
  <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow h-full flex flex-col">
    <div class="relative flex-grow">
      <Link :href="route('products.show', item.product.slug)" class="block h-full">
        <img 
          :src="item.product.image || '/images/placeholder-product.png'" 
          :alt="item.product.name" 
          class="w-full h-48 object-cover"
        >
      </Link>
      <button 
        @click="$emit('remove', item)" 
        class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md text-red-500 hover:bg-red-50 focus:outline-none"
        title="Remove from wishlist"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
    <div class="p-4 flex-grow flex flex-col">
      <div class="flex-grow">
        <h3 class="font-medium text-gray-900 mb-1">
          <Link :href="route('products.show', item.product.slug)" class="hover:text-indigo-600">
            {{ item.product.name }}
          </Link>
        </h3>
        <p class="text-sm text-gray-500 mb-2">{{ item.product.category?.name }}</p>
        <p class="text-lg font-semibold text-gray-900">
          {{ formatCurrency(item.product.sale_price || item.product.price) }}
          <span v-if="item.product.sale_price" class="ml-1 text-sm text-gray-500 line-through">
            {{ formatCurrency(item.product.price) }}
          </span>
        </p>
        <p v-if="item.notes" class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
          {{ item.notes }}
        </p>
      </div>
      <div class="mt-4 flex justify-between items-center">
        <Link 
          :href="route('products.show', item.product.slug)" 
          class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
        >
          View details
        </Link>
        <button 
          @click="$emit('add-to-cart', item.product)" 
          class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Add to Cart
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'; 
import { defineProps } from 'vue';

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value / 100); // Assuming prices are stored in cents
};

defineEmits(['remove', 'add-to-cart']);
</script>
