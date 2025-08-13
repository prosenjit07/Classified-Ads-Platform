<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">
          {{ title }}
        </h3>
        <slot name="header">
          <Link 
            v-if="link" 
            :href="link"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
          >
            {{ linkText || 'View all' }}
          </Link>
        </slot>
      </div>
      
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="animate-pulse">
          <div class="h-48 bg-gray-200 rounded"></div>
          <div class="mt-2 h-4 bg-gray-200 rounded w-3/4"></div>
          <div class="mt-1 h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>
      
      <div v-else-if="items.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <slot v-for="(item, index) in items" :key="index" :item="item" />
      </div>
      
      <div v-else class="text-center py-8">
        <slot name="empty">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="emptyIcon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            <path v-else-if="emptyIcon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            {{ emptyMessage }}
          </h3>
          <p v-if="emptyDescription" class="mt-1 text-sm text-gray-500">
            {{ emptyDescription }}
          </p>
          <div v-if="emptyLink" class="mt-6">
            <Link 
              :href="emptyLink" 
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ emptyLinkText || 'Get started' }}
            </Link>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  link: {
    type: String,
    default: ''
  },
  linkText: {
    type: String,
    default: ''
  },
  emptyMessage: {
    type: String,
    default: 'No items found.'
  },
  emptyDescription: {
    type: String,
    default: ''
  },
  emptyLink: {
    type: String,
    default: ''
  },
  emptyLinkText: {
    type: String,
    default: ''
  },
  emptyIcon: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'heart', 'eye'].includes(value)
  }
});
</script>
