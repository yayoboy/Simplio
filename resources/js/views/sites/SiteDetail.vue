<template>
  <div class="px-4 sm:px-0">
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-500">Loading...</div>
    </div>

    <div v-else-if="site">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ site.name }}</h2>
        <p class="text-gray-500">{{ site.description }}</p>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Pages</h3>
        <p class="text-gray-500">Page builder coming soon...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const site = ref(null);
const loading = ref(false);

async function fetchSite() {
  loading.value = true;
  try {
    const response = await axios.get(`/api/sites/${route.params.id}`);
    site.value = response.data;
  } catch (error) {
    console.error('Failed to fetch site:', error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchSite();
});
</script>
