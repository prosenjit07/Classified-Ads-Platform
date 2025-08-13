<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Admin Dashboard
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Total Products -->
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Products
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.total_products }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="px-4 py-4 bg-gray-50 sm:px-6">
              <div class="text-sm">
                <Link :href="route('admin.products.index')" class="font-medium text-indigo-600 hover:text-indigo-500">
                  View all
                </Link>
              </div>
            </div>
          </div>

          <!-- Total Categories -->
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-500 rounded-md">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Categories
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.total_categories }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="px-4 py-4 bg-gray-50 sm:px-6">
              <div class="text-sm">
                <Link :href="route('admin.categories.index')" class="font-medium text-green-600 hover:text-green-500">
                  View all
                </Link>
              </div>
            </div>
          </div>

          <!-- Total Brands -->
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-yellow-500 rounded-md">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Brands
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.total_brands }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="px-4 py-4 bg-gray-50 sm:px-6">
              <div class="text-sm">
                <Link :href="route('admin.brands.index')" class="font-medium text-yellow-600 hover:text-yellow-500">
                  View all
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Products -->
        <div class="mt-8">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-900">
              Recently Added Products
            </h3>
          </div>
          
          <div class="mt-4 overflow-hidden bg-white shadow sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="product in stats.latest_products" :key="product.id">
                <Link :href="route('admin.products.show', product.id)" class="block hover:bg-gray-50">
                  <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-medium text-indigo-600 truncate">
                        {{ product.name }}
                      </p>
                      <div class="flex flex-shrink-0 ml-2">
                        <p class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                          {{ product.status }}
                        </p>
                      </div>
                    </div>
                    <div class="mt-2 sm:flex sm:justify-between">
                      <div class="sm:flex">
                        <p class="flex items-center text-sm text-gray-500">
                          {{ product.category }}
                        </p>
                        <p class="flex items-center mt-2 text-sm text-gray-500 sm:mt-0 sm:ml-6">
                          {{ product.brand }}
                        </p>
                      </div>
                      <div class="flex items-center mt-2 text-sm text-gray-500 sm:mt-0">
                        <p>
                          ${{ product.price.toFixed(2) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  stats: Object,
});
</script>
