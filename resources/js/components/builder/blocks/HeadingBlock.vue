<template>
  <component
    :is="headingTag"
    :class="['font-bold', textAlignClass, colorClass]"
  >
    {{ content.text }}
  </component>
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

const headingTag = computed(() => {
  const level = content.value.level || 2;
  return `h${level}`;
});

const textAlignClass = computed(() => {
  const align = properties.value.textAlign || 'left';
  return `text-${align}`;
});

const colorClass = computed(() => {
  const color = properties.value.color || 'default';
  if (color === 'default') return 'text-gray-900';
  return `text-${color}-600`;
});
</script>

<style scoped>
h1 {
  font-size: 2.25rem;
  line-height: 2.5rem;
}
h2 {
  font-size: 1.875rem;
  line-height: 2.25rem;
}
h3 {
  font-size: 1.5rem;
  line-height: 2rem;
}
h4 {
  font-size: 1.25rem;
  line-height: 1.75rem;
}
h5 {
  font-size: 1.125rem;
  line-height: 1.75rem;
}
h6 {
  font-size: 1rem;
  line-height: 1.5rem;
}
</style>
