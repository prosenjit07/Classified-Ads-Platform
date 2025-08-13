<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  product: { type: Object, required: true },
  images: { type: Array, default: () => [] },
});

const formatMoney = (value) => {
  if (value === null || value === undefined) return '—';
  const num = typeof value === 'number' ? value : parseFloat(value);
  if (isNaN(num)) return '—';
  return `$${num.toFixed(2)}`;
};
</script>

<template>
  <Head :title="`${product.name} | Product Details`" />
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800">{{ product.name }}</h2>
        <div class="space-x-2">
          <Link :href="route('admin.products.edit', product.slug || product.id)" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</Link>
          <Link :href="route('admin.products.index')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Back to Products</Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Images -->
              <div class="lg:col-span-1">
                <div class="grid grid-cols-2 gap-3">
                  <div v-for="img in images" :key="img.id" class="rounded-md overflow-hidden bg-gray-100">
                    <img :src="img.thumb_url || img.url" :alt="product.name" class="w-full h-32 object-cover" />
                  </div>
                  <div v-if="images.length === 0" class="text-gray-400 text-sm">No images</div>
                </div>
              </div>

              <!-- Details -->
              <div class="lg:col-span-2">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <dt class="text-sm text-gray-500">SKU</dt>
                    <dd class="text-sm text-gray-900">{{ product.sku || '—' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm text-gray-500">Category</dt>
                    <dd class="text-sm text-gray-900">{{ product.category?.name || '—' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm text-gray-500">Brand</dt>
                    <dd class="text-sm text-gray-900">{{ product.brand?.name || '—' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm text-gray-500">Price</dt>
                    <dd class="text-sm text-gray-900">
                      <template v-if="product.sale_price">
                        <span class="text-red-600 font-medium">{{ formatMoney(product.sale_price) }}</span>
                        <span class="ml-1 text-gray-500 line-through">{{ formatMoney(product.price) }}</span>
                      </template>
                      <template v-else>
                        {{ formatMoney(product.price) }}
                      </template>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm text-gray-500">Stock</dt>
                    <dd class="text-sm text-gray-900">{{ product.stock_quantity ?? 0 }} ({{ product.stock_status || 'in_stock' }})</dd>
                  </div>
                  <div>
                    <dt class="text-sm text-gray-500">Status</dt>
                    <dd class="text-sm">
                      <span :class="{'bg-green-100 text-green-800': product.is_active, 'bg-red-100 text-red-800': !product.is_active}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ product.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </dd>
                  </div>
                </dl>

                <div v-if="product.description" class="mt-8">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                  <div class="prose max-w-none text-gray-600 whitespace-pre-line">{{ product.description }}</div>
                </div>

                <div v-if="product.details?.length" class="mt-8">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Attributes</h3>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="d in product.details" :key="d.id" class="bg-gray-50 rounded p-3">
                      <div class="text-xs text-gray-500">{{ d.attribute_name }}</div>
                      <div class="text-sm text-gray-900 break-words">{{ d.attribute_value }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>


