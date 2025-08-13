<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div class="flex items-center">
      <div :class="`p-3 rounded-full bg-${iconColor}-100 text-${iconColor}-600`">
        <svg v-if="icon === 'heart'" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <svg v-else-if="icon === 'eye'" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <svg v-else-if="icon === 'shopping-cart'" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
      </div>
      <div class="ml-4">
        <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
        <p class="text-2xl font-semibold text-gray-700">{{ count }} {{ count === 1 ? 'item' : 'items' }}</p>
        <a v-if="link" :href="link" class="text-sm text-indigo-600 hover:text-indigo-500">{{ linkText || 'View all' }}</a>
        <button v-else @click="$emit('view-all')" class="text-sm text-blue-600 hover:text-blue-500">{{ linkText || 'View all' }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  title: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    default: 0
  },
  icon: {
    type: String,
    default: 'chart-bar' // 'heart', 'eye', 'shopping-cart', 'chart-bar'
  },
  iconColor: {
    type: String,
    default: 'indigo' // 'indigo', 'blue', 'green', 'red', 'yellow', 'purple', 'pink'
  },
  link: {
    type: String,
    default: ''
  },
  linkText: {
    type: String,
    default: 'View all'
  }
});

defineEmits(['view-all']);
</script>