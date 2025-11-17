<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>

    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Site Name *</label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        required
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="My Awesome Site"
      />
    </div>

    <div>
      <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
      <input
        id="slug"
        v-model="form.slug"
        type="text"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="my-awesome-site"
      />
      <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
    </div>

    <div>
      <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
      <textarea
        id="description"
        v-model="form.description"
        rows="3"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="A brief description of your site"
      ></textarea>
    </div>

    <div>
      <label for="domain" class="block text-sm font-medium text-gray-700">Domain</label>
      <input
        id="domain"
        v-model="form.domain"
        type="text"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        placeholder="example.com"
      />
    </div>

    <div>
      <label for="theme_id" class="block text-sm font-medium text-gray-700">Theme</label>
      <select
        id="theme_id"
        v-model="form.theme_id"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
      >
        <option :value="null">No theme selected</option>
        <option v-for="theme in themes" :key="theme.id" :value="theme.id">
          {{ theme.name }}
        </option>
      </select>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  site: {
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
  name: '',
  slug: '',
  description: '',
  domain: '',
  theme_id: null,
});

const themes = ref([]);

// Load themes
async function fetchThemes() {
  try {
    const response = await axios.get('/api/themes');
    themes.value = response.data;
  } catch (err) {
    console.error('Failed to fetch themes:', err);
  }
}

// Initialize form with site data if editing
watch(() => props.site, (newSite) => {
  if (newSite) {
    form.value = {
      name: newSite.name || '',
      slug: newSite.slug || '',
      description: newSite.description || '',
      domain: newSite.domain || '',
      theme_id: newSite.theme_id || null,
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

onMounted(() => {
  fetchThemes();
});

// Expose form data for parent component
defineExpose({
  form,
});
</script>
