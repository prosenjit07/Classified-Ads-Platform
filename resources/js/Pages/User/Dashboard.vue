<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import DashboardSection from '@/Components/Dashboard/DashboardSection.vue';
import DashboardStatCard from '@/Components/Dashboard/DashboardStatCard.vue';
import WishlistItem from '@/Components/WishlistItem.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineOptions({
  layout: UserLayout
});

const props = defineProps({
  wishlistItems: {
    type: Object,
    required: true
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

const loading = ref(false);

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

const toggleWishlist = (product) => {
  if (product.is_in_wishlist) {
    // Find the wishlist item ID
    const wishlistItem = props.wishlistItems.data.find(
      item => item.product_id === product.id
    );
    
    if (wishlistItem) {
      removeFromWishlist(wishlistItem);
    }
  } else {
    router.post(route('wishlist.store'), {
      product_id: product.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // The page will be refreshed automatically by Inertia
      }
    });
  }
};

const loadRecentlyViewed = () => {
  // This would navigate to a dedicated recently viewed page if implemented
  router.visit(route('welcome'));
};
</script>

<template>
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
        :items="wishlistItems.data"
        empty-message="Your wishlist is empty."
        :empty-link="route('welcome')"
        empty-link-text="Browse products"
        empty-icon="heart"
        :loading="loading"
      >
        <template #item="{ item }">
          <WishlistItem 
            :item="item" 
            @remove="removeFromWishlist" 
            @add-to-cart="(product) => $emit('add-to-cart', product)"
          />
        </template>
      </DashboardSection>

      <!-- Recently Viewed Section -->
      <DashboardSection 
        title="Recently Viewed"
        :items="recentlyViewed"
        empty-message="You haven't viewed any products recently."
        :empty-link="route('welcome')"
        empty-link-text="Browse products"
        empty-icon="eye"
        :loading="loading"
        grid-classes="grid-cols-2 sm:grid-cols-3 lg:grid-cols-6"
      >
        <template #item="{ item: product }">
          <ProductCard 
            :product="product" 
            @toggle-wishlist="toggleWishlist"
            @add-to-cart="(p) => $emit('add-to-cart', p)"
          />
        </template>
      </DashboardSection>
    </div>
  </div>
</template>
