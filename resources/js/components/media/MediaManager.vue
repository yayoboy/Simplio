<template>
  <div class="media-manager h-full flex flex-col">
    <!-- Header with Upload and Filters -->
    <div class="flex items-center justify-between mb-6 pb-4 border-b">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Media Library</h2>
        <p class="text-sm text-gray-600 mt-1">
          {{ filteredMedia.length }} {{ filteredMedia.length === 1 ? 'item' : 'items' }}
        </p>
      </div>
      <div class="flex items-center gap-4">
        <!-- Type Filter -->
        <select
          v-model="filterType"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="all">All Media</option>
          <option value="images">Images</option>
          <option value="videos">Videos</option>
          <option value="documents">Documents</option>
        </select>

        <!-- Upload Button -->
        <button
          @click="triggerFileInput"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Upload Media
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

    <!-- Search Bar -->
    <div class="mb-6">
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by filename, alt text, or caption..."
          class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <svg
          class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"
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
    </div>

    <!-- Upload Progress -->
    <div v-if="mediaStore.uploadProgress > 0" class="mb-4">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-blue-700">Uploading...</span>
          <span class="text-sm text-blue-600">{{ mediaStore.uploadProgress }}%</span>
        </div>
        <div class="w-full bg-blue-200 rounded-full h-2">
          <div
            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${mediaStore.uploadProgress}%` }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="mediaStore.error" class="mb-4">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start justify-between">
        <div class="flex items-start gap-2">
          <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-sm text-red-700">{{ mediaStore.error }}</span>
        </div>
        <button @click="mediaStore.clearError" class="text-red-500 hover:text-red-700">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Drop Zone -->
    <div
      v-if="filteredMedia.length === 0 && !mediaStore.loading"
      class="flex-1 flex items-center justify-center"
    >
      <div
        @drop.prevent="handleDrop"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        :class="[
          'w-full max-w-2xl border-2 border-dashed rounded-xl p-12 text-center transition-colors',
          isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50',
        ]"
      >
        <svg
          class="mx-auto w-16 h-16 text-gray-400 mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
          />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Drop files to upload</h3>
        <p class="text-sm text-gray-600 mb-4">or click the button above to browse</p>
        <p class="text-xs text-gray-500">Supports: JPG, PNG, GIF, WebP (max 10MB)</p>
      </div>
    </div>

    <!-- Media Grid -->
    <div v-else-if="filteredMedia.length > 0" class="flex-1 overflow-y-auto">
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div
          v-for="item in filteredMedia"
          :key="item.id"
          @click="selectMedia(item)"
          :class="[
            'relative group cursor-pointer rounded-lg overflow-hidden border-2 transition-all',
            selectedMedia?.id === item.id ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300',
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
            <div v-else class="flex flex-col items-center justify-center p-4">
              <svg class="w-12 h-12 text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                  clip-rule="evenodd"
                />
              </svg>
              <span class="text-xs text-gray-600 font-medium">{{ item.extension?.toUpperCase() }}</span>
            </div>
          </div>

          <!-- Overlay on Hover -->
          <div
            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center"
          >
            <button
              @click.stop="deleteMediaItem(item)"
              class="opacity-0 group-hover:opacity-100 bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-all"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
            </button>
          </div>

          <!-- Filename -->
          <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2">
            <p class="text-xs text-white truncate">{{ item.original_filename }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else-if="mediaStore.loading" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
        <p class="text-gray-600">Loading media...</p>
      </div>
    </div>

    <!-- Selected Media Detail Panel -->
    <div
      v-if="selectedMedia"
      class="fixed inset-y-0 right-0 w-96 bg-white shadow-2xl border-l border-gray-200 flex flex-col z-50"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-900">Media Details</h3>
        <button
          @click="selectedMedia = null"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto p-6">
        <!-- Preview -->
        <div class="mb-6">
          <img
            v-if="selectedMedia.mime_type.startsWith('image/')"
            :src="selectedMedia.variants?.medium?.url || selectedMedia.url"
            :alt="selectedMedia.alt_text || selectedMedia.original_filename"
            class="w-full rounded-lg border border-gray-200"
          />
          <div v-else class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>

        <!-- Info -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filename</label>
            <input
              v-model="editForm.original_filename"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
            <input
              v-model="editForm.alt_text"
              type="text"
              placeholder="Describe this image..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
            <textarea
              v-model="editForm.caption"
              rows="3"
              placeholder="Add a caption..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <!-- File Info -->
          <div class="pt-4 border-t">
            <h4 class="text-sm font-medium text-gray-700 mb-3">File Information</h4>
            <dl class="space-y-2 text-sm">
              <div class="flex justify-between">
                <dt class="text-gray-600">Type:</dt>
                <dd class="font-medium text-gray-900">{{ selectedMedia.mime_type }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-gray-600">Size:</dt>
                <dd class="font-medium text-gray-900">{{ formatFileSize(selectedMedia.size) }}</dd>
              </div>
              <div v-if="selectedMedia.width && selectedMedia.height" class="flex justify-between">
                <dt class="text-gray-600">Dimensions:</dt>
                <dd class="font-medium text-gray-900">{{ selectedMedia.width }} × {{ selectedMedia.height }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-gray-600">Uploaded:</dt>
                <dd class="font-medium text-gray-900">{{ formatDate(selectedMedia.created_at) }}</dd>
              </div>
            </dl>
          </div>

          <!-- URL -->
          <div class="pt-4 border-t">
            <label class="block text-sm font-medium text-gray-700 mb-2">File URL</label>
            <div class="flex gap-2">
              <input
                :value="selectedMedia.url"
                readonly
                class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm"
              />
              <button
                @click="copyToClipboard(selectedMedia.url)"
                class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors"
                title="Copy URL"
              >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="p-6 border-t flex gap-3">
        <button
          @click="saveChanges"
          :disabled="!hasChanges"
          :class="[
            'flex-1 px-4 py-2 rounded-lg font-medium transition-colors',
            hasChanges
              ? 'bg-blue-600 text-white hover:bg-blue-700'
              : 'bg-gray-100 text-gray-400 cursor-not-allowed',
          ]"
        >
          Save Changes
        </button>
        <button
          @click="deleteMediaItem(selectedMedia)"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useMediaStore } from '@/stores/media';
import { useRoute } from 'vue-router';

const route = useRoute();
const mediaStore = useMediaStore();

const fileInput = ref(null);
const searchQuery = ref('');
const filterType = ref('all');
const selectedMedia = ref(null);
const isDragging = ref(false);
const editForm = ref({
  original_filename: '',
  alt_text: '',
  caption: '',
});

const siteId = computed(() => route.params.siteId || route.params.id);

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

const hasChanges = computed(() => {
  if (!selectedMedia.value) return false;
  return (
    editForm.value.original_filename !== selectedMedia.value.original_filename ||
    editForm.value.alt_text !== (selectedMedia.value.alt_text || '') ||
    editForm.value.caption !== (selectedMedia.value.caption || '')
  );
});

function triggerFileInput() {
  fileInput.value?.click();
}

async function handleFileSelect(event) {
  const files = event.target.files;
  if (files && files.length > 0) {
    await uploadFiles(files);
    event.target.value = ''; // Reset input
  }
}

async function handleDrop(event) {
  isDragging.value = false;
  const files = event.dataTransfer.files;
  if (files && files.length > 0) {
    await uploadFiles(files);
  }
}

async function uploadFiles(files) {
  try {
    await mediaStore.uploadMultiple(siteId.value, files);
  } catch (error) {
    console.error('Upload failed:', error);
  }
}

function selectMedia(item) {
  selectedMedia.value = item;
  editForm.value = {
    original_filename: item.original_filename,
    alt_text: item.alt_text || '',
    caption: item.caption || '',
  };
}

async function saveChanges() {
  if (!selectedMedia.value || !hasChanges.value) return;

  try {
    const updated = await mediaStore.updateMedia(siteId.value, selectedMedia.value.id, editForm.value);
    selectedMedia.value = updated;
  } catch (error) {
    console.error('Save failed:', error);
  }
}

async function deleteMediaItem(item) {
  if (!confirm(`Are you sure you want to delete "${item.original_filename}"?`)) {
    return;
  }

  try {
    await mediaStore.deleteMedia(siteId.value, item.id);
    if (selectedMedia.value?.id === item.id) {
      selectedMedia.value = null;
    }
  } catch (error) {
    console.error('Delete failed:', error);
  }
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('URL copied to clipboard!');
  });
}

function formatFileSize(bytes) {
  const units = ['B', 'KB', 'MB', 'GB'];
  let size = bytes;
  let unitIndex = 0;

  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024;
    unitIndex++;
  }

  return `${size.toFixed(2)} ${units[unitIndex]}`;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

// Load media on mount
onMounted(async () => {
  if (siteId.value) {
    try {
      await mediaStore.fetchMedia(siteId.value);
    } catch (error) {
      console.error('Failed to load media:', error);
    }
  }
});

// Watch for site changes
watch(siteId, async (newSiteId) => {
  if (newSiteId) {
    selectedMedia.value = null;
    try {
      await mediaStore.fetchMedia(newSiteId);
    } catch (error) {
      console.error('Failed to load media:', error);
    }
  }
});
</script>

<style scoped>
/* Custom scrollbar for media grid */
.media-manager ::-webkit-scrollbar {
  width: 8px;
}

.media-manager ::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.media-manager ::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 4px;
}

.media-manager ::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
