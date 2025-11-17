<template>
  <div>
    <!-- Gallery Grid -->
    <div v-if="content.images && content.images.length > 0" :class="['grid gap-' + gapSize]" :style="gridStyle">
      <div v-for="(image, index) in content.images" :key="index" class="relative group overflow-hidden rounded-lg">
        <img
          :src="image.src || image"
          :alt="image.alt || `Image ${index + 1}`"
          class="w-full h-full object-cover"
          :style="{ aspectRatio: properties.aspectRatio || '16/9' }"
        />
        <div v-if="image.caption" class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 text-white text-sm p-2">
          {{ image.caption }}
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">No images in gallery</p>
      <p class="text-xs text-gray-400">Add images via properties panel</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  block: {
    type: Object,
    required: true,
  },
});

const content = computed(() => props.block.content || {});
const properties = computed(() => props.block.properties || {});

const gridStyle = computed(() => {
  const columns = properties.value.columns || 3;
  return {
    gridTemplateColumns: `repeat(${columns}, 1fr)`,
  };
});

const gapSize = computed(() => {
  const gap = properties.value.gap || '1rem';
  // Convert rem to tailwind class
  if (gap === '0.5rem') return '2';
  if (gap === '1rem') return '4';
  if (gap === '1.5rem') return '6';
  if (gap === '2rem') return '8';
  return '4';
});
</script>
