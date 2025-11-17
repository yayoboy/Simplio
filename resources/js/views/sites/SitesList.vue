<template>
  <div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Sites</h2>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        Create Site
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-500">Loading...</div>
    </div>

    <div v-else-if="sites.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
      <h3 class="text-lg font-medium text-gray-900 mb-2">No sites yet</h3>
      <p class="text-gray-500 mb-4">Create your first site to get started</p>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        Create Your First Site
      </button>
    </div>

    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="site in sites"
        :key="site.id"
        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow"
      >
        <div class="p-5">
          <h3 class="text-lg font-medium text-gray-900">{{ site.name }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ site.description || 'No description' }}</p>
          <div class="mt-4 flex items-center text-sm text-gray-500">
            <span class="mr-4">{{ site.pages_count || 0 }} pages</span>
            <span
              :class="[
                'px-2 py-1 rounded-full text-xs font-medium',
                site.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ site.is_published ? 'Published' : 'Draft' }}
            </span>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3 flex justify-between">
          <router-link
            :to="{ name: 'site-detail', params: { id: site.id } }"
            class="text-sm text-blue-600 hover:text-blue-500"
          >
            Manage →
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const sites = ref([]);
const loading = ref(false);
const showCreateModal = ref(false);

async function fetchSites() {
  loading.value = true;
  try {
    const response = await axios.get('/api/sites');
    sites.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch sites:', error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchSites();
});
</script>
