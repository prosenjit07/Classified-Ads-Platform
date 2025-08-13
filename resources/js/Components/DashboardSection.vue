<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <slot name="icon">
          <svg v-if="icon" :class="`h-5 w-5 text-${iconColor}-500 mr-2`" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="icon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            <path v-else-if="icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path v-else-if="icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </slot>
        {{ title }}
      </h3>
    </div>
    
    <div v-if="items.length > 0" :class="['p-6', gridClasses]">
      <slot v-for="(item, index) in items" :key="item.id || index" :item="item" :index="index"></slot>
    </div>
    
    <div v-else class="p-6 text-center text-gray-500">
      <p>{{ emptyMessage }}</p>
      <Link v-if="emptyLink" :href="emptyLink" class="mt-2 inline-flex items-center text-indigo-600 hover:text-indigo-500">
        {{ emptyLinkText }}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </Link>
      <button v-else @click="$emit('action')" class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-500">
        {{ emptyLinkText }}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
    
    <div v-if="showViewMore && items.length > 0" class="px-6 pb-6 text-center">
      <button 
        @click="$emit('view-more')" 
        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none"
      >
        View more
      </button>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  },
  icon: {
    type: String,
    default: '' // 'heart', 'eye', etc.
  },
  iconColor: {
    type: String,
    default: 'indigo'
  },
  emptyMessage: {
    type: String,
    default: 'No items found.'
  },
  emptyLink: {
    type: String,
    default: ''
  },
  emptyLinkText: {
    type: String,
    default: 'Get started'
  },
  gridClasses: {
    type: String,
    default: 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
  },
  showViewMore: {
    type: Boolean,
    default: false
  }
});

defineEmits(['action', 'view-more']);
</script>
