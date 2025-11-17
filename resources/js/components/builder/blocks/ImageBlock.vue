<template>
  <div :class="alignClass">
    <div :style="{ width: properties.width || '100%' }" class="inline-block">
      <img
        v-if="content.src"
        :src="content.src"
        :alt="content.alt || ''"
        :class="['w-full rounded-lg', objectFitClass]"
      />
      <div
        v-else
        class="w-full aspect-video bg-gray-100 rounded-lg flex items-center justify-center"
      >
        <div class="text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p class="mt-2 text-sm text-gray-500">No image selected</p>
        </div>
      </div>
      <p v-if="content.caption" class="mt-2 text-sm text-gray-600 text-center">
        {{ content.caption }}
      </p>
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

const alignClass = computed(() => {
  const align = properties.value.align || 'center';
  const alignMap = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
  };
  return alignMap[align] || 'text-center';
});

const objectFitClass = computed(() => {
  const fit = properties.value.objectFit || 'cover';
  return `object-${fit}`;
});
</script>
