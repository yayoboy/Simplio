<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <button
              @click="goBack"
              class="text-gray-600 hover:text-gray-900 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </button>
            <div>
              <h1 class="text-xl font-semibold text-gray-900">Media Library</h1>
              <p v-if="currentSite" class="text-sm text-gray-600">{{ currentSite.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" style="min-height: calc(100vh - 200px);">
        <MediaManager v-if="siteId" />
        <div v-else class="flex items-center justify-center h-96">
          <div class="text-center">
            <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-gray-600">No site selected</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSitesStore } from '@/stores/sites';
import MediaManager from '@/components/media/MediaManager.vue';

const route = useRoute();
const router = useRouter();
const sitesStore = useSitesStore();

const siteId = computed(() => route.params.siteId || route.params.id);
const currentSite = computed(() => sitesStore.currentSite);

function goBack() {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push({ name: 'sites' });
  }
}

onMounted(async () => {
  if (siteId.value && !currentSite.value) {
    try {
      await sitesStore.fetchSite(siteId.value);
    } catch (error) {
      console.error('Failed to load site:', error);
    }
  }
});
</script>
