<template>
  <div :class="alignClass">
    <button
      :class="[
        'inline-flex items-center justify-center font-medium rounded-lg transition-colors',
        variantClass,
        sizeClass,
        properties.fullWidth && 'w-full'
      ]"
      @click.prevent="handleClick"
    >
      {{ content.text || 'Button' }}
    </button>
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
  const align = properties.value.align || 'left';
  const alignMap = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
  };
  return alignMap[align] || 'text-left';
});

const variantClass = computed(() => {
  const variant = properties.value.variant || 'primary';
  const variants = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-gray-600 text-white hover:bg-gray-700',
    outline: 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
    ghost: 'text-blue-600 hover:bg-blue-50',
  };
  return variants[variant] || variants.primary;
});

const sizeClass = computed(() => {
  const size = properties.value.size || 'medium';
  const sizes = {
    small: 'px-3 py-1.5 text-sm',
    medium: 'px-4 py-2 text-base',
    large: 'px-6 py-3 text-lg',
  };
  return sizes[size] || sizes.medium;
});

function handleClick() {
  if (content.value.link) {
    if (content.value.openInNewTab) {
      window.open(content.value.link, '_blank');
    } else {
      window.location.href = content.value.link;
    }
  }
}
</script>
