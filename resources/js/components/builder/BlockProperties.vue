<template>
  <div class="space-y-6">
    <!-- Block Info -->
    <div class="pb-4 border-b border-gray-200">
      <h3 class="text-sm font-medium text-gray-900">{{ block.name || block.type }}</h3>
      <p class="text-xs text-gray-500 mt-1">{{ getBlockConfig(block.type)?.description }}</p>
    </div>

    <!-- Dynamic Properties based on block type -->
    <div class="space-y-4">
      <!-- Text Block Properties -->
      <template v-if="block.type === 'text'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
          <textarea
            v-model="localContent.text"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          ></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Text Align</label>
          <select v-model="localProperties.textAlign" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
          <select v-model="localProperties.fontSize" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="sm">Small</option>
            <option value="base">Base</option>
            <option value="lg">Large</option>
            <option value="xl">Extra Large</option>
          </select>
        </div>
      </template>

      <!-- Heading Block Properties -->
      <template v-else-if="block.type === 'heading'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Text</label>
          <input
            v-model="localContent.text"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Heading Level</label>
          <select v-model.number="localContent.level" @change="updateContent" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option :value="1">H1</option>
            <option :value="2">H2</option>
            <option :value="3">H3</option>
            <option :value="4">H4</option>
            <option :value="5">H5</option>
            <option :value="6">H6</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Text Align</label>
          <select v-model="localProperties.textAlign" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
          </select>
        </div>
      </template>

      <!-- Image Block Properties -->
      <template v-else-if="block.type === 'image'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
          <div class="flex gap-2">
            <input
              v-model="localContent.src"
              type="text"
              placeholder="https://example.com/image.jpg"
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              @input="debouncedUpdate"
            />
            <button
              @click="openMediaPicker('image')"
              class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors"
            >
              Browse
            </button>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
          <input
            v-model="localContent.alt"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Caption</label>
          <input
            v-model="localContent.caption"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alignment</label>
          <select v-model="localProperties.align" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Width</label>
          <input
            v-model="localProperties.width"
            type="text"
            placeholder="100%"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
      </template>

      <!-- Button Block Properties -->
      <template v-else-if="block.type === 'button'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
          <input
            v-model="localContent.text"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Link URL</label>
          <input
            v-model="localContent.link"
            type="text"
            placeholder="https://example.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
        </div>
        <div class="flex items-center">
          <input
            v-model="localContent.openInNewTab"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            @change="updateContent"
          />
          <label class="ml-2 block text-sm text-gray-700">Open in new tab</label>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Style</label>
          <select v-model="localProperties.variant" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="outline">Outline</option>
            <option value="ghost">Ghost</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
          <select v-model="localProperties.size" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="small">Small</option>
            <option value="medium">Medium</option>
            <option value="large">Large</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alignment</label>
          <select v-model="localProperties.align" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
          </select>
        </div>
        <div class="flex items-center">
          <input
            v-model="localProperties.fullWidth"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            @change="updateProperties"
          />
          <label class="ml-2 block text-sm text-gray-700">Full width</label>
        </div>
      </template>

      <!-- Divider Block Properties -->
      <template v-else-if="block.type === 'divider'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Style</label>
          <select v-model="localProperties.style" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="solid">Solid</option>
            <option value="dashed">Dashed</option>
            <option value="dotted">Dotted</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Thickness</label>
          <input
            v-model="localProperties.thickness"
            type="text"
            placeholder="1px"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
          <input
            v-model="localProperties.color"
            type="color"
            class="w-full h-10 px-1 py-1 border border-gray-300 rounded-md"
            @input="updateProperties"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Spacing</label>
          <input
            v-model="localProperties.spacing"
            type="text"
            placeholder="2rem"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
      </template>

      <!-- Gallery Block Properties -->
      <template v-else-if="block.type === 'gallery'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
          <select v-model.number="localProperties.columns" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option :value="2">2 Columns</option>
            <option :value="3">3 Columns</option>
            <option :value="4">4 Columns</option>
            <option :value="5">5 Columns</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gap</label>
          <select v-model="localProperties.gap" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="0.5rem">Small (0.5rem)</option>
            <option value="1rem">Medium (1rem)</option>
            <option value="1.5rem">Large (1.5rem)</option>
            <option value="2rem">Extra Large (2rem)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Aspect Ratio</label>
          <select v-model="localProperties.aspectRatio" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="16/9">16:9</option>
            <option value="4/3">4:3</option>
            <option value="1/1">1:1 (Square)</option>
          </select>
        </div>
        <div class="pt-2 border-t">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">Images ({{ (localContent.images || []).length }})</label>
            <button
              @click="openMediaPicker('gallery')"
              class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700 transition-colors"
            >
              Add Images
            </button>
          </div>
          <div v-if="localContent.images && localContent.images.length > 0" class="grid grid-cols-3 gap-2 mt-2">
            <div
              v-for="(image, index) in localContent.images"
              :key="index"
              class="relative aspect-square bg-gray-100 rounded overflow-hidden group"
            >
              <img :src="image.src || image" :alt="image.alt || ''" class="w-full h-full object-cover" />
              <button
                @click="removeGalleryImage(index)"
                class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
          <p v-else class="text-xs text-gray-500 mt-2">No images added yet</p>
        </div>
      </template>

      <!-- Video Block Properties -->
      <template v-else-if="block.type === 'video'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
          <input
            v-model="localContent.url"
            type="text"
            placeholder="https://youtube.com/watch?v=..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdate"
          />
          <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
          <select v-model="localContent.provider" @change="updateContent" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="youtube">YouTube</option>
            <option value="vimeo">Vimeo</option>
          </select>
        </div>
        <div class="flex items-center">
          <input
            v-model="localContent.autoplay"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            @change="updateContent"
          />
          <label class="ml-2 block text-sm text-gray-700">Autoplay</label>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Aspect Ratio</label>
          <select v-model="localProperties.aspectRatio" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="16/9">16:9</option>
            <option value="4/3">4:3</option>
            <option value="1/1">1:1 (Square)</option>
          </select>
        </div>
      </template>

      <!-- HTML Block Properties -->
      <template v-else-if="block.type === 'html'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">HTML Code</label>
          <textarea
            v-model="localContent.html"
            rows="8"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="<div>Your HTML here</div>"
            @input="debouncedUpdate"
          ></textarea>
        </div>
        <div class="flex items-center bg-yellow-50 border border-yellow-200 rounded p-3">
          <input
            v-model="localProperties.sanitize"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            @change="updateProperties"
          />
          <label class="ml-2 block text-sm text-gray-700">
            Sanitize HTML (recommended for security)
          </label>
        </div>
        <div class="text-xs text-gray-500 bg-gray-50 rounded p-2">
          <strong>⚠️ Warning:</strong> Disabling sanitization may expose your site to XSS attacks. Only disable if you trust the HTML source.
        </div>
      </template>

      <!-- Spacer Block Properties -->
      <template v-else-if="block.type === 'spacer'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Height</label>
          <input
            v-model="localProperties.height"
            type="text"
            placeholder="2rem"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
          <p class="mt-1 text-xs text-gray-500">Use CSS units: px, rem, em, vh, etc.</p>
        </div>
        <div class="text-xs text-gray-500 bg-gray-50 rounded p-2">
          <strong>Examples:</strong> 1rem, 2rem, 50px, 10vh
        </div>
      </template>

      <!-- Container Block Properties -->
      <template v-else-if="block.type === 'container'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Max Width</label>
          <input
            v-model="localProperties.maxWidth"
            type="text"
            placeholder="1200px"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Padding</label>
          <input
            v-model="localProperties.padding"
            type="text"
            placeholder="1rem"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
          <input
            v-model="localProperties.backgroundColor"
            type="color"
            class="w-full h-10 px-1 py-1 border border-gray-300 rounded-md"
            @input="updateProperties"
          />
        </div>
        <div class="text-xs text-gray-500 bg-blue-50 rounded p-2">
          <strong>Note:</strong> Nested blocks feature coming soon
        </div>
      </template>

      <!-- Columns Block Properties -->
      <template v-else-if="block.type === 'columns'">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Number of Columns</label>
          <select v-model.number="columnsCount" @change="updateColumnsCount" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option :value="2">2 Columns</option>
            <option :value="3">3 Columns</option>
            <option :value="4">4 Columns</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gap</label>
          <input
            v-model="localProperties.gap"
            type="text"
            placeholder="1rem"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="debouncedUpdateProperties"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Vertical Align</label>
          <select v-model="localProperties.verticalAlign" @change="updateProperties" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="top">Top</option>
            <option value="center">Center</option>
            <option value="bottom">Bottom</option>
          </select>
        </div>
        <div class="text-xs text-gray-500 bg-blue-50 rounded p-2">
          <strong>Note:</strong> Nested blocks feature coming soon
        </div>
      </template>

      <!-- Fallback for other types -->
      <template v-else>
        <div class="text-center py-8">
          <p class="text-sm text-gray-500">Properties panel for "{{ block.type }}" coming soon</p>
        </div>
      </template>
    </div>

    <!-- Media Picker Modal -->
    <MediaPicker
      :is-open="showMediaPicker"
      :site-id="siteId"
      :multiple="mediaPickerMode === 'gallery'"
      :accept="mediaPickerMode === 'image' ? 'images' : 'all'"
      @close="showMediaPicker = false"
      @select="handleMediaSelect"
    />
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import { getBlockConfig } from '@/config/blockTypes';
import MediaPicker from '@/components/media/MediaPicker.vue';

const props = defineProps({
  block: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['update']);

const route = useRoute();
const siteId = computed(() => route.params.siteId || route.params.id);

const localContent = ref({ ...props.block.content });
const localProperties = ref({ ...props.block.properties });
const columnsCount = ref(props.block.content?.columns?.length || 2);

// Media Picker state
const showMediaPicker = ref(false);
const mediaPickerMode = ref('image'); // 'image' or 'gallery'

// Watch for external changes
watch(() => props.block, (newBlock) => {
  localContent.value = { ...newBlock.content };
  localProperties.value = { ...newBlock.properties };
}, { deep: true });

let updateTimeout;
function debouncedUpdate() {
  clearTimeout(updateTimeout);
  updateTimeout = setTimeout(() => {
    updateContent();
  }, 500);
}

function debouncedUpdateProperties() {
  clearTimeout(updateTimeout);
  updateTimeout = setTimeout(() => {
    updateProperties();
  }, 500);
}

function updateContent() {
  emit('update', {
    content: localContent.value,
  });
}

function updateProperties() {
  emit('update', {
    properties: localProperties.value,
  });
}

function updateColumnsCount() {
  const newColumns = Array.from({ length: columnsCount.value }, (_, i) => ({
    blocks: localContent.value.columns?.[i]?.blocks || [],
    width: `${100 / columnsCount.value}%`,
  }));
  localContent.value.columns = newColumns;
  updateContent();
}

// Media Picker functions
function openMediaPicker(mode) {
  mediaPickerMode.value = mode;
  showMediaPicker.value = true;
}

function handleMediaSelect(selected) {
  if (mediaPickerMode.value === 'image') {
    // Single image for Image block
    localContent.value.src = selected.url;
    localContent.value.alt = selected.alt_text || selected.original_filename;
    localContent.value.caption = selected.caption || '';
    updateContent();
  } else if (mediaPickerMode.value === 'gallery') {
    // Multiple images for Gallery block
    const newImages = Array.isArray(selected) ? selected : [selected];
    const formattedImages = newImages.map((media) => ({
      src: media.url,
      alt: media.alt_text || media.original_filename,
      caption: media.caption || '',
    }));

    if (!localContent.value.images) {
      localContent.value.images = [];
    }
    localContent.value.images.push(...formattedImages);
    updateContent();
  }
}

function removeGalleryImage(index) {
  if (localContent.value.images) {
    localContent.value.images.splice(index, 1);
    updateContent();
  }
}
</script>
