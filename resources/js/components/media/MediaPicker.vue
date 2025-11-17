<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="close"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl h-5/6 flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Select Media</h2>
          <p v-if="multiple" class="text-sm text-gray-600 mt-1">
            {{ selectedItems.length }} {{ selectedItems.length === 1 ? 'item' : 'items' }} selected
          </p>
        </div>
        <button
          @click="close"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Toolbar -->
      <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
        <div class="flex items-center gap-4">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search media..."
              class="w-64 px-3 py-2 pl-9 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            />
            <svg
              class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>

          <!-- Type Filter -->
          <select
            v-model="filterType"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
          >
            <option value="all">All Media</option>
            <option value="images">Images Only</option>
            <option value="videos">Videos Only</option>
          </select>
        </div>

        <!-- Upload Button -->
        <div>
          <button
            @click="triggerFileInput"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 text-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload New
          </button>
          <input
            ref="fileInput"
            type="file"
            multiple
            accept="image/*,video/*"
            @change="handleFileSelect"
            class="hidden"
          />
        </div>
      </div>

      <!-- Media Grid -->
      <div class="flex-1 overflow-y-auto p-6">
        <!-- Loading -->
        <div v-if="mediaStore.loading" class="h-full flex items-center justify-center">
          <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading media...</p>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else-if="filteredMedia.length === 0"
          class="h-full flex items-center justify-center"
        >
          <div class="text-center">
            <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No media found</h3>
            <p class="text-sm text-gray-600 mb-4">Upload your first file to get started</p>
            <button
              @click="triggerFileInput"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Upload Media
            </button>
          </div>
        </div>

        <!-- Grid -->
        <div v-else class="grid grid-cols-4 gap-4">
          <div
            v-for="item in filteredMedia"
            :key="item.id"
            @click="toggleSelection(item)"
            :class="[
              'relative group cursor-pointer rounded-lg overflow-hidden border-2 transition-all',
              isSelected(item) ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300',
            ]"
          >
            <!-- Image Preview -->
            <div class="aspect-square bg-gray-100 flex items-center justify-center">
              <img
                v-if="item.mime_type.startsWith('image/')"
                :src="item.variants?.thumbnail?.url || item.url"
                :alt="item.alt_text || item.original_filename"
                class="w-full h-full object-cover"
              />
              <div v-else class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <!-- Selection Indicator -->
            <div
              v-if="isSelected(item)"
              class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>

            <!-- Filename -->
            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2">
              <p class="text-xs text-white truncate">{{ item.original_filename }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between p-6 border-t bg-gray-50">
        <button
          @click="close"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Cancel
        </button>
        <button
          @click="confirmSelection"
          :disabled="selectedItems.length === 0"
          :class="[
            'px-6 py-2 rounded-lg font-medium transition-colors',
            selectedItems.length > 0
              ? 'bg-blue-600 text-white hover:bg-blue-700'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed',
          ]"
        >
          Select {{ selectedItems.length > 0 ? `(${selectedItems.length})` : '' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useMediaStore } from '@/stores/media';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  siteId: {
    type: [String, Number],
    required: true,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  accept: {
    type: String,
    default: 'all', // 'all', 'images', 'videos'
  },
});

const emit = defineEmits(['close', 'select']);

const mediaStore = useMediaStore();
const fileInput = ref(null);
const searchQuery = ref('');
const filterType = ref(props.accept === 'images' ? 'images' : 'all');
const selectedItems = ref([]);

const filteredMedia = computed(() => {
  let result = mediaStore.media;

  // Apply type filter
  if (filterType.value !== 'all') {
    result = mediaStore.filterByType(filterType.value);
  }

  // Apply search
  if (searchQuery.value) {
    result = result.filter((item) => {
      const query = searchQuery.value.toLowerCase();
      return (
        item.original_filename?.toLowerCase().includes(query) ||
        item.alt_text?.toLowerCase().includes(query) ||
        item.caption?.toLowerCase().includes(query)
      );
    });
  }

  return result;
});

function isSelected(item) {
  return selectedItems.value.some((i) => i.id === item.id);
}

function toggleSelection(item) {
  if (props.multiple) {
    const index = selectedItems.value.findIndex((i) => i.id === item.id);
    if (index >= 0) {
      selectedItems.value.splice(index, 1);
    } else {
      selectedItems.value.push(item);
    }
  } else {
    // Single selection - replace
    selectedItems.value = [item];
  }
}

function confirmSelection() {
  if (selectedItems.value.length > 0) {
    emit('select', props.multiple ? selectedItems.value : selectedItems.value[0]);
    close();
  }
}

function close() {
  selectedItems.value = [];
  searchQuery.value = '';
  emit('close');
}

function triggerFileInput() {
  fileInput.value?.click();
}

async function handleFileSelect(event) {
  const files = event.target.files;
  if (files && files.length > 0) {
    try {
      const uploaded = await mediaStore.uploadMultiple(props.siteId, files);
      // Auto-select newly uploaded files
      if (props.multiple) {
        selectedItems.value.push(...uploaded);
      } else {
        selectedItems.value = [uploaded[0]];
      }
      event.target.value = ''; // Reset input
    } catch (error) {
      console.error('Upload failed:', error);
    }
  }
}

// Load media when picker opens
watch(
  () => props.isOpen,
  async (isOpen) => {
    if (isOpen && props.siteId) {
      try {
        await mediaStore.fetchMedia(props.siteId);
      } catch (error) {
        console.error('Failed to load media:', error);
      }
    }
  },
);

// Load media on mount if already open
onMounted(async () => {
  if (props.isOpen && props.siteId) {
    try {
      await mediaStore.fetchMedia(props.siteId);
    } catch (error) {
      console.error('Failed to load media:', error);
    }
  }
});
</script>
