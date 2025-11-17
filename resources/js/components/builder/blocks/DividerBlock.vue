<template>
  <div :style="spacingStyle">
    <hr :class="['border-0', styleClass]" :style="dividerStyle" />
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

const styleClass = computed(() => {
  const style = properties.value.style || 'solid';
  if (style === 'dashed') return 'border-t-2 border-dashed';
  if (style === 'dotted') return 'border-t-2 border-dotted';
  return ''; // solid handled in dividerStyle
});

const dividerStyle = computed(() => {
  const thickness = properties.value.thickness || '1px';
  const color = properties.value.color || '#e5e7eb';
  const style = properties.value.style || 'solid';

  if (style === 'solid') {
    return {
      height: thickness,
      backgroundColor: color,
    };
  }

  return {
    borderColor: color,
  };
});

const spacingStyle = computed(() => {
  const spacing = properties.value.spacing || '2rem';
  return {
    paddingTop: spacing,
    paddingBottom: spacing,
  };
});
</script>
