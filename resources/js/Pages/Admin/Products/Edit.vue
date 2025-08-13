<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Edit Product
        </h2>
        <Link
          :href="route('admin.products.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Back to Products
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="p-6 bg-white border-b border-gray-200">
              <!-- Basic Information -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Name -->
                  <div class="col-span-2">
                    <InputLabel for="name" value="Product Name" required />
                    <TextInput
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.name }"
                      required
                      autofocus
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                  </div>

                  <!-- SKU -->
                  <div>
                    <InputLabel for="sku" value="SKU" />
                    <TextInput
                      id="sku"
                      v-model="form.sku"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.sku }"
                    />
                    <InputError :message="form.errors.sku" class="mt-2" />
                  </div>

                  <!-- Category -->
                  <div>
                    <InputLabel for="category_id" value="Category" required />
                    <select
                      id="category_id"
                      v-model="form.category_id"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors.category_id }"
                      required
                    >
                      <option value="">Select a category</option>
                      <option 
                        v-for="category in categories" 
                        :key="category.id" 
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                    <InputError :message="form.errors.category_id" class="mt-2" />
                  </div>

                  <!-- Brand -->
                  <div>
                    <InputLabel for="brand_id" value="Brand" />
                    <select
                      id="brand_id"
                      v-model="form.brand_id"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors.brand_id }"
                    >
                      <option value="">Select a brand</option>
                      <option 
                        v-for="brand in brands" 
                        :key="brand.id" 
                        :value="brand.id"
                      >
                        {{ brand.name }}
                      </option>
                    </select>
                    <InputError :message="form.errors.brand_id" class="mt-2" />
                  </div>

                  <!-- Price -->
                  <div>
                    <InputLabel for="price" value="Price" required />
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <TextInput
                        id="price"
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                        :class="{ 'border-red-500': form.errors.price }"
                        placeholder="0.00"
                        required
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm" id="price-currency">USD</span>
                      </div>
                    </div>
                    <InputError :message="form.errors.price" class="mt-2" />
                  </div>

                  <!-- Sale Price -->
                  <div>
                    <InputLabel for="sale_price" value="Sale Price" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <TextInput
                        id="sale_price"
                        v-model="form.sale_price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                        :class="{ 'border-red-500': form.errors.sale_price }"
                        placeholder="0.00"
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm" id="sale-price-currency">USD</span>
                      </div>
                    </div>
                    <InputError :message="form.errors.sale_price" class="mt-2" />
                  </div>

                  <!-- Quantity -->
                  <div>
                    <InputLabel for="quantity" value="Quantity" required />
                    <TextInput
                      id="quantity"
                      v-model.number="form.quantity"
                      type="number"
                      min="0"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.quantity }"
                      required
                    />
                    <InputError :message="form.errors.quantity" class="mt-2" />
                  </div>

                  <!-- Weight -->
                  <div>
                    <InputLabel for="weight" value="Weight (kg)" />
                    <TextInput
                      id="weight"
                      v-model.number="form.weight"
                      type="number"
                      step="0.01"
                      min="0"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.weight }"
                    />
                    <InputError :message="form.errors.weight" class="mt-2" />
                  </div>

                  <!-- Status -->
                  <div>
                    <InputLabel for="is_active" value="Status" />
                    <select
                      id="is_active"
                      v-model="form.is_active"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors.is_active }"
                    >
                      <option :value="true">Active</option>
                      <option :value="false">Inactive</option>
                    </select>
                    <InputError :message="form.errors.is_active" class="mt-2" />
                  </div>

                  <!-- Featured -->
                  <div>
                    <InputLabel for="is_featured" value="Featured" />
                    <select
                      id="is_featured"
                      v-model="form.is_featured"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors.is_featured }"
                    >
                      <option :value="false">No</option>
                      <option :value="true">Yes</option>
                    </select>
                    <InputError :message="form.errors.is_featured" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                <div>
                  <InputLabel for="short_description" value="Short Description" />
                  <TextArea
                    id="short_description"
                    v-model="form.short_description"
                    rows="3"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.short_description }"
                  />
                  <InputError :message="form.errors.short_description" class="mt-2" />
                </div>

                <div class="mt-4">
                  <InputLabel for="description" value="Description" />
                  <TextArea
                    id="description"
                    v-model="form.description"
                    rows="5"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.description }"
                  />
                  <InputError :message="form.errors.description" class="mt-2" />
                </div>
              </div>

              <!-- Images -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Images</h3>
                
                <!-- Existing Images -->
                <div v-if="product.media && product.media.length > 0" class="mb-6">
                  <h4 class="text-sm font-medium text-gray-700 mb-3">Current Images</h4>
                  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div v-for="(media, index) in product.media" :key="media.id" class="relative group">
                      <img :src="media.original_url" :alt="product.name" class="w-full h-32 object-cover rounded-md">
                      <button
                        type="button"
                        @click="removeImage(media.id)"
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Remove image"
                      >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                      <div v-if="index === 0" class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 rounded">
                        Main
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Upload New Images -->
                <div>
                  <InputLabel for="images" value="Add More Images" />
                  <input
                    id="images"
                    type="file"
                    multiple
                    @input="form.images = $event.target.files"
                    class="mt-1 block w-full text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-md file:border-0
                      file:text-sm file:font-semibold
                      file:bg-blue-50 file:text-blue-700
                      hover:file:bg-blue-100"
                    accept="image/*"
                  />
                  <p class="mt-1 text-sm text-gray-500">Upload product images (multiple allowed). First image will be used as the main image.</p>
                  <InputError :message="form.errors.images" class="mt-2" />
                </div>
              </div>

              <!-- Product Attributes -->
              <div v-if="attributes.length > 0" class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Attributes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-for="attribute in attributes" :key="attribute.id" class="mb-4">
                    <InputLabel :for="'attribute_' + attribute.id" :value="attribute.name" />
                    
                    <!-- Text Input -->
                    <TextInput
                      v-if="attribute.type === 'text'"
                      :id="'attribute_' + attribute.id"
                      v-model="form.attributes[attribute.id]"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attributes.${attribute.id}`] }"
                    />
                    
                    <!-- Number Input -->
                    <TextInput
                      v-else-if="attribute.type === 'number'"
                      :id="'attribute_' + attribute.id"
                      v-model.number="form.attributes[attribute.id]"
                      type="number"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attributes.${attribute.id}`] }"
                    />
                    
                    <!-- Select Dropdown -->
                    <select
                      v-else-if="attribute.type === 'select'"
                      :id="'attribute_' + attribute.id"
                      v-model="form.attributes[attribute.id]"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors[`attributes.${attribute.id}`] }"
                    >
                      <option value="">Select {{ attribute.name }}</option>
                      <option v-for="option in attribute.options" :key="option" :value="option">
                        {{ option }}
                      </option>
                    </select>
                    
                    <!-- Checkbox -->
                    <div v-else-if="attribute.type === 'checkbox'" class="mt-2">
                      <label class="inline-flex items-center">
                        <input
                          type="checkbox"
                          :id="'attribute_' + attribute.id"
                          v-model="form.attributes[attribute.id]"
                          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        <span class="ml-2 text-sm text-gray-600">{{ attribute.name }}</span>
                      </label>
                    </div>
                    
                    <InputError :message="form.errors[`attributes.${attribute.id}`]" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- SEO Information -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="col-span-2">
                    <InputLabel for="meta_title" value="Meta Title" />
                    <TextInput
                      id="meta_title"
                      v-model="form.meta_title"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.meta_title }"
                    />
                    <InputError :message="form.errors.meta_title" class="mt-2" />
                  </div>
                  
                  <div class="col-span-2">
                    <InputLabel for="meta_description" value="Meta Description" />
                    <TextArea
                      id="meta_description"
                      v-model="form.meta_description"
                      rows="3"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.meta_description }"
                    />
                    <InputError :message="form.errors.meta_description" class="mt-2" />
                  </div>
                  
                  <div class="col-span-2">
                    <InputLabel for="meta_keywords" value="Meta Keywords" />
                    <TextInput
                      id="meta_keywords"
                      v-model="form.meta_keywords"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors.meta_keywords }"
                      placeholder="Comma-separated keywords"
                    />
                    <InputError :message="form.errors.meta_keywords" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex items-center justify-end mt-8">
                <Link
                  :href="route('admin.products.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  :disabled="form.processing"
                  :class="{ 'opacity-50': form.processing }"
                >
                  <span v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </span>
                  <span v-else>Update Product</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
  product: {
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
  attributes: {
    type: Array,
    default: () => [],
  },
});

const { showSuccess, showError } = useToast();

const form = useForm({
  name: props.product.name,
  sku: props.product.sku,
  category_id: props.product.category_id,
  brand_id: props.product.brand_id,
  price: (props.product.price / 100).toFixed(2),
  sale_price: props.product.sale_price ? (props.product.sale_price / 100).toFixed(2) : '',
  quantity: props.product.quantity,
  weight: props.product.weight,
  short_description: props.product.short_description,
  description: props.product.description,
  is_active: props.product.is_active,
  is_featured: props.product.is_featured,
  meta_title: props.product.meta_title,
  meta_description: props.product.meta_description,
  meta_keywords: props.product.meta_keywords,
  images: [],
  attributes: {},
  _method: 'PUT',
});

// Initialize attributes
onMounted(() => {
  if (props.product.attributes && props.product.attributes.length > 0) {
    props.product.attributes.forEach(attr => {
      form.attributes[attr.attribute_id] = attr.value;
    });
  }
});

const removeImage = (mediaId) => {
  if (confirm('Are you sure you want to delete this image?')) {
    router.delete(route('admin.products.media.destroy', [props.product.id, mediaId]), {
      preserveScroll: true,
      onSuccess: () => {
        showSuccess('Image deleted successfully');
      },
      onError: () => {
        showError('Failed to delete image');
      },
    });
  }
};

const submit = () => {
  // Convert price and sale_price to cents before submitting
  const formData = new FormData();
  
  // Append all form data
  Object.keys(form).forEach(key => {
    if (key === 'price' || key === 'sale_price') {
      formData.append(key, form[key] ? Math.round(parseFloat(form[key]) * 100) : null);
    } else if (key === 'images') {
      // Handle file uploads
      Array.from(form.images).forEach((file, index) => {
        formData.append(`images[${index}]`, file);
      });
    } else if (key === 'attributes') {
      // Handle attributes
      Object.keys(form.attributes).forEach(attrId => {
        formData.append(`attributes[${attrId}]`, form.attributes[attrId] || '');
      });
    } else if (key !== 'processing' && key !== 'errors' && key !== 'reset' && key !== 'clearErrors' && key !== 'setError' && key !== 'cancel') {
      formData.append(key, form[key]);
    }
  });

  form.post(route('admin.products.update', props.product.id), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      showSuccess('Product updated successfully');
    },
    onError: () => {
      showError('Failed to update product. Please check the form for errors.');
    },
  });
};
</script>