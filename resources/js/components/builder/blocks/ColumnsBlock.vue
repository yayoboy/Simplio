<template>
  <div :style="columnsContainerStyle">
    <div v-if="content.columns && content.columns.length > 0" :style="columnsGridStyle">
      <div
        v-for="(column, index) in content.columns"
        :key="index"
        class="column-wrapper border-2 border-dashed border-gray-200 rounded-lg p-4 min-h-[100px]"
        :style="{ width: column.width || 'auto' }"
      >
        <div v-if="column.blocks && column.blocks.length > 0" class="space-y-2">
          <div v-for="(block, blockIndex) in column.blocks" :key="blockIndex">
            <component
              :is="getBlockComponent(block.type)"
              :block="block"
            />
          </div>
        </div>
        <div v-else class="text-center py-6">
          <p class="text-xs text-gray-400">Column {{ index + 1 }}</p>
          <p class="text-xs text-gray-400 mt-1">Drag blocks here</p>
        </div>
      </div>
    </div>
    <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
      <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a2 2 0 00-2 2v12a2 2 0 002 2h4m0-16h4m-4 0v16m4-16h4a2 2 0 012 2v12a2 2 0 01-2 2h-4m0-16v16"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">No columns defined</p>
      <p class="text-xs text-gray-400">Configure columns via properties panel</p>
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

const columnsContainerStyle = computed(() => {
  return {
    width: '100%',
  };
});

const columnsGridStyle = computed(() => {
  const gap = properties.value.gap || '1rem';
  const verticalAlign = properties.value.verticalAlign || 'top';

  return {
    display: 'grid',
    gridAutoFlow: 'column',
    gridAutoColumns: '1fr',
    gap,
    alignItems: verticalAlign === 'top' ? 'start' : verticalAlign === 'bottom' ? 'end' : 'center',
  };
});

function getBlockComponent(type) {
  // For now, just use placeholder
  return PlaceholderBlock;
}
</script>

<style scoped>
.column-wrapper {
  min-width: 0; /* Prevent overflow in grid */
}
</style>
