<template>
  <div class="nested-blocks space-y-2">
    <component
      v-for="block in blocks"
      :key="block.id"
      :is="getBlockComponent(block.type)"
      :block="block"
      :content="block.content || {}"
      :properties="block.properties || {}"
      class="nested-block"
    >
      <!-- Recursively render children if they exist -->
      <NestedBlockRenderer
        v-if="block.children && block.children.length > 0"
        :blocks="block.children"
      />
    </component>
  </div>
</template>

<script setup>
import { defineAsyncComponent } from 'vue';

const props = defineProps({
  blocks: {
    type: Array,
    default: () => [],
  },
});

// Lazy load block components
const TextBlock = defineAsyncComponent(() => import('./blocks/TextBlock.vue'));
const HeadingBlock = defineAsyncComponent(() => import('./blocks/HeadingBlock.vue'));
const ImageBlock = defineAsyncComponent(() => import('./blocks/ImageBlock.vue'));
const GalleryBlock = defineAsyncComponent(() => import('./blocks/GalleryBlock.vue'));
const VideoBlock = defineAsyncComponent(() => import('./blocks/VideoBlock.vue'));
const HtmlBlock = defineAsyncComponent(() => import('./blocks/HtmlBlock.vue'));
const ButtonBlock = defineAsyncComponent(() => import('./blocks/ButtonBlock.vue'));
const DividerBlock = defineAsyncComponent(() => import('./blocks/DividerBlock.vue'));
const SpacerBlock = defineAsyncComponent(() => import('./blocks/SpacerBlock.vue'));
const ContainerBlock = defineAsyncComponent(() => import('./blocks/ContainerBlock.vue'));
const ColumnsBlock = defineAsyncComponent(() => import('./blocks/ColumnsBlock.vue'));
const PlaceholderBlock = defineAsyncComponent(() => import('./blocks/PlaceholderBlock.vue'));

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
</script>

<style scoped>
.nested-blocks {
  min-height: 20px;
}

.nested-block {
  position: relative;
}
</style>
