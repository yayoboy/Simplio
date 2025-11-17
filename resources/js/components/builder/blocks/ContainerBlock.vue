<template>
  <div :style="containerStyle" class="rounded-lg">
    <div v-if="content.blocks && content.blocks.length > 0" class="space-y-4">
      <div v-for="(block, index) in content.blocks" :key="index" class="nested-block">
        <component
          :is="getBlockComponent(block.type)"
          :block="block"
        />
      </div>
    </div>
    <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
      <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">Empty container</p>
      <p class="text-xs text-gray-400">Nested blocks not yet implemented</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import PlaceholderBlock from './PlaceholderBlock.vue';

const props = defineProps({
  block: {
    type: Object,
    required: true,
  },
});

const content = computed(() => props.block.content || {});
const properties = computed(() => props.block.properties || {});

const containerStyle = computed(() => {
  return {
    maxWidth: properties.value.maxWidth || '1200px',
    padding: properties.value.padding || '1rem',
    backgroundColor: properties.value.backgroundColor || 'transparent',
    margin: '0 auto',
  };
});

function getBlockComponent(type) {
  // For now, just use placeholder
  return PlaceholderBlock;
}
</script>
