<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>

    <div>
      <label for="title" class="block text-sm font-medium text-gray-700">Page Title *</label>
      <input
        id="title"
        v-model="form.title"
        type="text"
        required
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="About Us"
      />
    </div>

    <div>
      <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
      <input
        id="slug"
        v-model="form.slug"
        type="text"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="about-us"
      />
      <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from title</p>
    </div>

    <div>
      <label for="meta_title" class="block text-sm font-medium text-gray-700">SEO Title</label>
      <input
        id="meta_title"
        v-model="form.meta_title"
        type="text"
        maxlength="60"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="About Us - Company Name"
      />
      <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters</p>
    </div>

    <div>
      <label for="meta_description" class="block text-sm font-medium text-gray-700">SEO Description</label>
      <textarea
        id="meta_description"
        v-model="form.meta_description"
        rows="3"
        maxlength="160"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="A brief description for search engines"
      ></textarea>
      <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
    </div>

    <div class="flex items-center">
      <input
        id="is_home"
        v-model="form.is_home"
        type="checkbox"
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
      />
      <label for="is_home" class="ml-2 block text-sm text-gray-700">
        Set as home page
      </label>
    </div>
    <p v-if="form.is_home" class="text-xs text-blue-600 -mt-2">
      This will replace the current home page
    </p>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  page: {
    type: Object,
    default: null,
  },
  error: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const form = ref({
  title: '',
  slug: '',
  meta_title: '',
  meta_description: '',
  is_home: false,
});

// Initialize form with page data if editing
watch(() => props.page, (newPage) => {
  if (newPage) {
    form.value = {
      title: newPage.title || '',
      slug: newPage.slug || '',
      meta_title: newPage.meta_title || '',
      meta_description: newPage.meta_description || '',
      is_home: newPage.is_home || false,
    };
  }
}, { immediate: true });

// Emit form data changes
watch(form, (newForm) => {
  emit('update:modelValue', newForm);
}, { deep: true });

function handleSubmit() {
  // Form submission handled by parent
}

// Expose form data for parent component
defineExpose({
  form,
});
</script>
