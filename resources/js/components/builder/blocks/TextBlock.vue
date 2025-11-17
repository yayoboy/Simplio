<template>
  <div
    :class="['prose max-w-none', textAlignClass, fontSizeClass]"
    v-html="content.text"
  ></div>
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

const textAlignClass = computed(() => {
  const align = properties.value.textAlign || 'left';
  return `text-${align}`;
});

const fontSizeClass = computed(() => {
  const size = properties.value.fontSize || 'base';
  const sizeMap = {
    sm: 'text-sm',
    base: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl',
  };
  return sizeMap[size] || 'text-base';
});
</script>

<style scoped>
.prose :deep(p) {
  margin-bottom: 0.5em;
}
.prose :deep(p:last-child) {
  margin-bottom: 0;
}
.prose :deep(strong) {
  font-weight: 600;
}
.prose :deep(em) {
  font-style: italic;
}
.prose :deep(a) {
  color: #3b82f6;
  text-decoration: underline;
}
</style>
