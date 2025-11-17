<template>
  <div :style="spacerStyle" class="relative group">
    <!-- Visual Indicator (only in builder) -->
    <div class="absolute inset-0 border-2 border-dashed border-gray-300 rounded opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
      <span class="bg-white px-2 py-1 rounded text-xs text-gray-500 font-medium">
        Spacer: {{ properties.height || '2rem' }}
      </span>
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

const properties = computed(() => props.block.properties || {});

const spacerStyle = computed(() => {
  const height = properties.value.height || '2rem';
  return {
    height,
    minHeight: '1rem',
  };
});
</script>
