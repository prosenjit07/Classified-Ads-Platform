<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Add New Product
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

                  <!-- Condition -->
                  <div>
                    <InputLabel for="condition" value="Condition" required />
                    <select
                      id="condition"
                      v-model="form.condition"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      :class="{ 'border-red-500': form.errors.condition }"
                      required
                    >
                      <option v-for="condition in conditions" :key="condition.value" :value="condition.value">
                        {{ condition.label }}
                      </option>
                    </select>
                    <InputError :message="form.errors.condition" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                <div>
                  <InputLabel for="description" value="Product Description" />
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="6"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    :class="{ 'border-red-500': form.errors.description }"
                  ></textarea>
                  <InputError :message="form.errors.description" class="mt-2" />
                </div>
              </div>

              <!-- Images -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Images</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div v-for="(image, index) in form.images" :key="index" class="relative group">
                    <img :src="image.preview" class="h-32 w-full object-cover rounded-md" />
                    <button
                      type="button"
                      @click="removeImage(index)"
                      class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                      title="Remove image"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                  <div v-if="form.images.length < 5" class="border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center h-32">
                    <label class="cursor-pointer text-center p-4">
                      <input
                        type="file"
                        class="hidden"
                        accept="image/*"
                        multiple
                        @change="handleImageUpload"
                      />
                      <div class="flex flex-col items-center">
                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="mt-2 text-sm text-gray-600">Add Images</span>
                        <span class="text-xs text-gray-500">(Max 5 images)</span>
                      </div>
                    </label>
                  </div>
                </div>
                <InputError :message="form.errors.images" class="mt-2" />
              </div>

              <!-- Attributes -->
              <div v-if="attributes.length > 0" class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Attributes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-for="attribute in attributes" :key="attribute.id" class="mb-4">
                    <InputLabel :for="'attribute_' + attribute.id" :value="attribute.name" />
                    <TextInput
                      :id="'attribute_' + attribute.id"
                      v-model="form.attributes[attribute.id]"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attributes.${attribute.id}`] }"
                    />
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
                    <Textarea
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
                      placeholder="keyword1, keyword2, keyword3"
                    />
                    <InputError :message="form.errors.meta_keywords" class="mt-2" />
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex items-center justify-end mt-8">
                <Link
                  :href="route('admin.products.index')"
                  class="mr-4 text-sm text-gray-600 hover:text-gray-900"
                >
                  Cancel
                </Link>
                <PrimaryButton
                  type="submit"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Save Product
                </PrimaryButton>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
  brands: {
    type: Array,
    required: true,
  },
  conditions: {
    type: Array,
    required: true,
  },
  attributeTypes: {
    type: Array,
    required: true,
  },
  attributes: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  name: '',
  sku: '',
  description: '',
  price: 0,
  sale_price: null,
  quantity: 0,
  weight: null,
  is_active: true,
  is_featured: false,
  condition: 'new',
  category_id: '',
  brand_id: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  images: [],
  attributes: {},
});

const handleImageUpload = (event) => {
  const files = event.target.files;
  if (!files.length) return;

  // Convert FileList to array and take only up to 5 images
  const newImages = Array.from(files).slice(0, 5 - form.images.length);
  
  newImages.forEach(file => {
    if (file.size > 2 * 1024 * 1024) { // 2MB limit
      alert('Image size should be less than 2MB');
      return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
      form.images.push({
        file,
        preview: e.target.result,
      });
    };
    reader.readAsDataURL(file);
  });
  
  // Reset the file input
  event.target.value = '';
};

const removeImage = (index) => {
  form.images.splice(index, 1);
};

const submit = () => {
  const formData = new FormData();
  
  // Append all form data
  Object.keys(form.data()).forEach(key => {
    if (key !== 'images' && key !== 'attributes') {
      const value = form[key];
      if (value !== null && value !== undefined) {
        formData.append(key, value);
      }
    }
  });
  
  // Append images
  form.images.forEach((image) => {
    formData.append('images[]', image.file);
  });
  
  // Append attributes
  if (Object.keys(form.attributes).length > 0) {
    formData.append('attributes', JSON.stringify(form.attributes));
  }
  
  // Submit the form using router with our FormData
  router.post(route('admin.products.store'), formData, {
    forceFormData: true,
    onSuccess: () => {},
    onError: (errors) => {
      console.error('Error creating product', errors);
    },
  });
};
</script>