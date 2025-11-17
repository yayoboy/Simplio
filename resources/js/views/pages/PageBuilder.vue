<template>
  <div class="h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <button @click="goBack" class="text-gray-600 hover:text-gray-900" title="Back to site">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div>
          <h1 class="text-lg font-semibold text-gray-900">{{ page?.title || 'Page Builder' }}</h1>
          <p class="text-sm text-gray-500">{{ site?.name }}</p>
        </div>
      </div>

      <div class="flex items-center space-x-3">
        <!-- View Mode Toggle -->
        <div class="flex rounded-md shadow-sm">
          <button @click="viewMode = 'desktop'" :class="['px-3 py-2 text-sm font-medium rounded-l-md border', viewMode === 'desktop' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50']">Desktop</button>
          <button @click="viewMode = 'tablet'" :class="['px-3 py-2 text-sm font-medium border-t border-b', viewMode === 'tablet' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50']">Tablet</button>
          <button @click="viewMode = 'mobile'" :class="['px-3 py-2 text-sm font-medium rounded-r-md border', viewMode === 'mobile' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50']">Mobile</button>
        </div>

        <button @click="saveChanges" :disabled="!hasUnsavedChanges || saving" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50">
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Block Palette Sidebar -->
      <aside class="w-64 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="p-4">
          <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Blocks</h2>
          <BlockPalette @add-block="handleAddBlock" />
        </div>
      </aside>

      <!-- Canvas Area -->
      <main class="flex-1 overflow-auto p-8">
        <div :class="['mx-auto bg-white shadow-lg rounded-lg transition-all duration-300', viewModeClasses]">
          <div class="min-h-screen p-8">
            <div v-if="blocksStore.loading" class="text-center py-12">
              <div class="text-gray-500">Loading blocks...</div>
            </div>
            <div v-else-if="!blocksStore.blocks.length" class="text-center py-20">
              <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Start building your page</h3>
              <p class="mt-2 text-sm text-gray-500">Click on blocks from the left sidebar to add them to your page</p>
            </div>
            <BlockCanvas
              v-else
              :blocks="blocksStore.blocks"
              :selected-block-id="blocksStore.selectedBlock?.id"
              @select-block="handleSelectBlock"
              @update-block="handleUpdateBlock"
              @delete-block="handleDeleteBlock"
              @reorder-blocks="handleReorderBlocks"
            />
          </div>
        </div>
      </main>

      <!-- Properties Sidebar -->
      <aside v-if="blocksStore.selectedBlock" class="w-80 bg-white border-l border-gray-200 overflow-y-auto">
        <div class="p-4">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Properties</h2>
            <button @click="blocksStore.deselectBlock()" class="text-gray-400 hover:text-gray-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <BlockProperties :block="blocksStore.selectedBlock" @update="handleUpdateBlockProperties" />
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSitesStore } from '@/stores/sites';
import { usePagesStore } from '@/stores/pages';
import { useBlocksStore } from '@/stores/blocks';
import { createBlockData } from '@/config/blockTypes';
import BlockPalette from '@/components/builder/BlockPalette.vue';
import BlockCanvas from '@/components/builder/BlockCanvas.vue';
import BlockProperties from '@/components/builder/BlockProperties.vue';

const route = useRoute();
const router = useRouter();
const sitesStore = useSitesStore();
const pagesStore = usePagesStore();
const blocksStore = useBlocksStore();

const site = ref(null);
const page = ref(null);
const viewMode = ref('desktop');
const hasUnsavedChanges = ref(false);
const saving = ref(false);

const siteId = computed(() => route.params.siteId);
const pageId = computed(() => route.params.pageId);

const viewModeClasses = computed(() => {
  switch (viewMode.value) {
    case 'mobile': return 'max-w-md';
    case 'tablet': return 'max-w-3xl';
    default: return 'max-w-7xl';
  }
});

async function loadData() {
  try {
    site.value = await sitesStore.fetchSite(siteId.value);
    page.value = await pagesStore.fetchPage(pageId.value);
    await blocksStore.fetchBlocks(pageId.value);
  } catch (error) {
    console.error('Failed to load page builder data:', error);
  }
}

async function handleAddBlock(blockType) {
  try {
    const blockData = createBlockData(blockType);
    const newBlock = await blocksStore.createBlock(pageId.value, blockData);
    blocksStore.selectBlock(newBlock);
    hasUnsavedChanges.value = true;
  } catch (error) {
    console.error('Failed to add block:', error);
  }
}

function handleSelectBlock(block) {
  blocksStore.selectBlock(block);
}

async function handleUpdateBlock(blockId, data) {
  try {
    await blocksStore.updateBlock(pageId.value, blockId, data);
    hasUnsavedChanges.value = true;
  } catch (error) {
    console.error('Failed to update block:', error);
  }
}

function handleUpdateBlockProperties(data) {
  if (blocksStore.selectedBlock) {
    handleUpdateBlock(blocksStore.selectedBlock.id, data);
  }
}

async function handleDeleteBlock(blockId) {
  try {
    await blocksStore.deleteBlock(pageId.value, blockId);
    hasUnsavedChanges.value = true;
  } catch (error) {
    console.error('Failed to delete block:', error);
  }
}

async function handleReorderBlocks(blockIds) {
  try {
    await blocksStore.reorderBlocks(pageId.value, blockIds);
    hasUnsavedChanges.value = true;
  } catch (error) {
    console.error('Failed to reorder blocks:', error);
  }
}

async function saveChanges() {
  saving.value = true;
  try {
    // All changes are already saved via API calls
    // This just marks the state as saved
    await new Promise(resolve => setTimeout(resolve, 500));
    hasUnsavedChanges.value = false;
  } catch (error) {
    console.error('Failed to save changes:', error);
  } finally {
    saving.value = false;
  }
}

function goBack() {
  if (hasUnsavedChanges.value) {
    if (!confirm('You have unsaved changes. Are you sure you want to leave?')) {
      return;
    }
  }
  router.push({ name: 'site-detail', params: { id: siteId.value } });
}

// Watch for block changes
watch(() => blocksStore.blocks, () => {
  hasUnsavedChanges.value = true;
}, { deep: true });

onMounted(() => {
  loadData();
});

// Warn before leaving with unsaved changes
window.addEventListener('beforeunload', (e) => {
  if (hasUnsavedChanges.value) {
    e.preventDefault();
    e.returnValue = '';
  }
});
</script>
