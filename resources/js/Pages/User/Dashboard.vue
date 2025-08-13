<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Dashboard
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <DashboardStatCard 
            title="My Wishlist"
            :count="stats.wishlist_count"
            icon="heart"
            icon-color="indigo"
            :link="route('wishlist.index')"
            link-text="View all"
          />
          
          <DashboardStatCard 
            title="Recently Viewed"
            :count="stats.recently_viewed_count"
            icon="eye"
            icon-color="blue"
            @view-all="loadRecentlyViewed"
          />
        </div>

        <!-- Wishlist Section -->
        <DashboardSection 
          title="My Wishlist"
          :items="wishlistItems"
          empty-message="Your wishlist is empty."
          :empty-link="route('products.index')"
          empty-link-text="Browse products"
        >
          <template #item="{ item }">
            <WishlistItem :item="item" @remove="removeFromWishlist" />
          </template>
        </DashboardSection>

        <!-- Recently Viewed Section -->
        <DashboardSection 
          title="Recently Viewed"
          :items="recentlyViewed"
          empty-message="You haven't viewed any products recently."
          :empty-link="route('products.index')"
          empty-link-text="Browse products"
          grid-classes="grid-cols-2 sm:grid-cols-3 lg:grid-cols-6"
        >
          <template #item="{ item: product }">
            <ProductCard :product="product" @toggle-wishlist="toggleWishlist" />
          </template>
        </DashboardSection>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import DashboardStatCard from '@/Components/DashboardStatCard.vue';
import DashboardSection from '@/Components/DashboardSection.vue';
import WishlistItem from '@/Components/WishlistItem.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
  wishlistItems: {
    type: Array,
    default: () => []
  },
  recentlyViewed: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      wishlist_count: 0,
      recently_viewed_count: 0
    })
  }
});

const removeFromWishlist = (item) => {
  router.delete(route('wishlist.destroy', item.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Update local state or refetch data
    }
  });
};

const toggleWishlist = (product) => {
  const method = product.is_in_wishlist ? 'delete' : 'post';
  const url = product.is_in_wishlist 
    ? route('wishlist.destroy', product.wishlist_id)
    : route('wishlist.store', { product: product.id });
    
  router[method](url, {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Update local state or refetch data
    }
  });
};

const loadRecentlyViewed = () => {
  // Implement logic to load more recently viewed items
  console.log('Load more recently viewed');
};
</script>
