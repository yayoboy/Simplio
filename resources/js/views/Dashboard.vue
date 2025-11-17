<template>
  <div class="px-4 sm:px-0">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Sites</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalSites }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <router-link to="/sites" class="text-sm text-blue-600 hover:text-blue-500">
            View all sites →
          </router-link>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Published Sites</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.publishedSites }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Pages</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalPages }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
      <div class="space-y-3">
        <router-link
          to="/sites"
          class="block px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
        >
          <div class="font-medium text-blue-900">Create New Site</div>
          <div class="text-sm text-blue-700">Start building your next website</div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const stats = ref({
  totalSites: 0,
  publishedSites: 0,
  totalPages: 0,
});

onMounted(() => {
  // TODO: Fetch real stats from API
  stats.value = {
    totalSites: 0,
    publishedSites: 0,
    totalPages: 0,
  };
});
</script>
