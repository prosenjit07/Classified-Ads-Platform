<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Products
        </h2>
        <Link
          :href="route('admin.products.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Add New Product
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Search and Filters -->
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <!-- Search -->
              <div class="md:col-span-2">
                <TextInput
                  v-model="filters.search"
                  type="search"
                  placeholder="Search products..."
                  class="w-full"
                  @keyup.enter="filterProducts"
                />
              </div>
              
              <!-- Category Filter -->
              <div>
                <SelectInput
                  v-model="filters.category_id"
                  :options="categories"
                  option-label="name"
                  option-value="id"
                  placeholder="Filter by category"
                  class="w-full"
                  @change="filterProducts"
                />
              </div>
              
              <!-- Brand Filter -->
              <div>
                <SelectInput
                  v-model="filters.brand_id"
                  :options="brands"
                  option-label="name"
                  option-value="id"
                  placeholder="Filter by brand"
                  class="w-full"
                  @change="filterProducts"
                />
              </div>
            </div>
            
            <div class="flex justify-between items-center">
              <div class="flex space-x-2">
                <Button @click="resetFilters" variant="secondary">
                  Reset Filters
                </Button>
              </div>
              
              <div class="text-sm text-gray-600">
                Showing {{ products.from }}-{{ products.to }} of {{ products.total }} products
              </div>
            </div>
          </div>
          
          <!-- Products Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Product
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Category
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Brand
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products.data" :key="product.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          v-if="product.media && product.media.length > 0" 
                          :src="product.media[0].original_url" 
                          :alt="product.name"
                          class="h-10 w-10 rounded-full object-cover"
                        >
                        <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                          <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ product.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ product.sku }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ product.category?.name || '—' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ product.brand?.name || '—' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      <template v-if="product.sale_price">
                        <span class="text-red-600 font-medium">${{ Number(product.sale_price).toFixed(2) }}</span>
                        <span class="text-gray-500 text-xs line-through ml-1">${{ Number(product.price).toFixed(2) }}</span>
                      </template>
                      <template v-else>
                        ${{ Number(product.price).toFixed(2) }}
                      </template>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="{
                        'bg-green-100 text-green-800': product.is_active,
                        'bg-red-100 text-red-800': !product.is_active
                      }" 
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    >
                      {{ product.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link 
                        :href="route('admin.products.show', { product: product.slug || product.id })" 
                        class="text-indigo-600 hover:text-indigo-900"
                        title="View"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </Link>
                      <Link 
                        :href="route('admin.products.edit', { product: product.slug || product.id })" 
                        class="text-blue-600 hover:text-blue-900"
                        title="Edit"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </Link>
                      <button 
                        @click="confirmProductDeletion(product)" 
                        class="text-red-600 hover:text-red-900"
                        title="Delete"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="products.data.length === 0">
                  <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    No products found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="px-6 py-4 bg-white border-t border-gray-200">
            <Pagination :links="products.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="confirmingProductDeletion" @close="closeModal" @confirm="deleteProduct">
      <template #title>
        Delete Product
      </template>

      <template #content>
        Are you sure you want to delete this product? This action cannot be undone.
      </template>

      <template #footer>
        <SecondaryButton @click="closeModal">
          Cancel
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deleteProduct"
        >
          Delete Product
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Button from '@/Components/Button.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  products: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    required: true,
  },
  brands: {
    type: Array,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      category_id: '',
      brand_id: '',
      status: '',
    }),
  },
});

const filters = reactive({
  search: props.filters.search || '',
  category_id: props.filters.category_id || '',
  brand_id: props.filters.brand_id || '',
  status: props.filters.status || '',
});

const confirmingProductDeletion = ref(false);
const productToDelete = ref(null);

const deleteForm = reactive({
  processing: false,
});

const filterProducts = () => {
  const query = {};
  
  if (filters.search) {
    query.search = filters.search;
  }
  
  if (filters.category_id) {
    query.category_id = filters.category_id;
  }
  
  if (filters.brand_id) {
    query.brand_id = filters.brand_id;
  }
  
  if (filters.status) {
    query.status = filters.status;
  }
  
  router.get(route('admin.products.index'), query, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

const resetFilters = () => {
  filters.search = '';
  filters.category_id = '';
  filters.brand_id = '';
  filters.status = '';
  
  filterProducts();
};

const confirmProductDeletion = (product) => {
  productToDelete.value = product;
  confirmingProductDeletion.value = true;
};

const closeModal = () => {
  confirmingProductDeletion.value = false;
  productToDelete.value = null;
};

const deleteProduct = () => {
  deleteForm.processing = true;
  
  router.delete(route('admin.products.destroy', { product: productToDelete.value.slug || productToDelete.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
    },
    onFinish: () => {
      deleteForm.processing = false;
    },
  });
};

// Watch for filter changes and debounce the API call
let filterTimeout = null;

watch(
  [() => filters.search, () => filters.category_id, () => filters.brand_id, () => filters.status],
  () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
      filterProducts();
    }, 300);
  },
  { deep: true }
);
</script>
