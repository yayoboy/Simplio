<template>
  <div class="space-y-4">
    <draggable
      v-model="localBlocks"
      :item-key="block => block.id"
      handle=".drag-handle"
      @end="handleReorder"
      class="space-y-4"
    >
      <template #item="{ element: block }">
        <div
          :class="[
            'relative group border-2 rounded-lg transition-all',
            selectedBlockId === block.id
              ? 'border-blue-500 bg-blue-50'
              : 'border-transparent hover:border-gray-300'
          ]"
          @click="selectBlock(block)"
        >
          <!-- Block Toolbar -->
          <div
            :class="[
              'absolute -top-10 left-0 right-0 flex items-center justify-between px-3 py-1 bg-gray-900 text-white text-xs rounded-t-lg transition-opacity',
              selectedBlockId === block.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
            ]"
          >
            <div class="flex items-center space-x-2">
              <button class="drag-handle cursor-move p-1 hover:bg-gray-700 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                </svg>
              </button>
              <span class="font-medium">{{ block.name || block.type }}</span>
            </div>
            <div class="flex items-center space-x-1">
              <button
                @click.stop="toggleVisibility(block)"
                class="p-1 hover:bg-gray-700 rounded"
                :title="block.is_visible ? 'Hide' : 'Show'"
              >
                <svg v-if="block.is_visible" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
              <button
                @click.stop="duplicateBlock(block)"
                class="p-1 hover:bg-gray-700 rounded"
                title="Duplicate"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
              </button>
              <button
                @click.stop="deleteBlock(block)"
                class="p-1 hover:bg-red-600 rounded"
                title="Delete"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Block Content -->
          <div :class="['p-4', !block.is_visible && 'opacity-50']">
            <component
              :is="getBlockComponent(block.type)"
              :block="block"
              @update="updateBlock(block.id, $event)"
            />
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';
import TextBlock from './blocks/TextBlock.vue';
import HeadingBlock from './blocks/HeadingBlock.vue';
import ImageBlock from './blocks/ImageBlock.vue';
import GalleryBlock from './blocks/GalleryBlock.vue';
import VideoBlock from './blocks/VideoBlock.vue';
import HtmlBlock from './blocks/HtmlBlock.vue';
import ButtonBlock from './blocks/ButtonBlock.vue';
import DividerBlock from './blocks/DividerBlock.vue';
import SpacerBlock from './blocks/SpacerBlock.vue';
import ContainerBlock from './blocks/ContainerBlock.vue';
import ColumnsBlock from './blocks/ColumnsBlock.vue';
import PlaceholderBlock from './blocks/PlaceholderBlock.vue';

const props = defineProps({
  blocks: {
    type: Array,
    required: true,
  },
  selectedBlockId: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['select-block', 'update-block', 'delete-block', 'reorder-blocks']);

const localBlocks = ref([...props.blocks]);

// Watch for external changes
watch(() => props.blocks, (newBlocks) => {
  localBlocks.value = [...newBlocks];
}, { deep: true });

function getBlockComponent(type) {
  const components = {
    text: TextBlock,
    heading: HeadingBlock,
    image: ImageBlock,
    gallery: GalleryBlock,
    video: VideoBlock,
    html: HtmlBlock,
    button: ButtonBlock,
    divider: DividerBlock,
    spacer: SpacerBlock,
    container: ContainerBlock,
    columns: ColumnsBlock,
  };
  return components[type] || PlaceholderBlock;
}

function selectBlock(block) {
  emit('select-block', block);
}

function updateBlock(blockId, data) {
  emit('update-block', blockId, data);
}

function deleteBlock(block) {
  if (confirm(`Delete "${block.name || block.type}" block?`)) {
    emit('delete-block', block.id);
  }
}

function duplicateBlock(block) {
  // Emit event to parent to handle duplication
  emit('update-block', block.id, { action: 'duplicate' });
}

function toggleVisibility(block) {
  emit('update-block', block.id, { is_visible: !block.is_visible });
}

function handleReorder() {
  const blockIds = localBlocks.value.map(b => b.id);
  emit('reorder-blocks', blockIds);
}
</script>
