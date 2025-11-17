<template>
  <div>
    <!-- Sanitized HTML -->
    <div v-if="content.html && !properties.sanitize" v-html="content.html" class="custom-html"></div>

    <!-- Sanitized Warning -->
    <div v-else-if="content.html && properties.sanitize" class="border border-yellow-300 bg-yellow-50 rounded-lg p-4">
      <div class="flex items-start">
        <svg class="h-5 w-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="ml-3 flex-1">
          <h4 class="text-sm font-medium text-yellow-800">HTML Sanitization Enabled</h4>
          <p class="mt-1 text-sm text-yellow-700">Custom HTML is disabled for security. Disable sanitization in properties to render HTML.</p>
          <details class="mt-2">
            <summary class="text-xs text-yellow-600 cursor-pointer hover:text-yellow-800">Show HTML code</summary>
            <pre class="mt-2 p-2 bg-white rounded text-xs overflow-x-auto">{{ content.html }}</pre>
          </details>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">No HTML content</p>
      <p class="text-xs text-gray-400">Add custom HTML via properties panel</p>
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
</script>

<style scoped>
.custom-html :deep(*) {
  max-width: 100%;
}

.custom-html :deep(img) {
  max-width: 100%;
  height: auto;
}

.custom-html :deep(iframe) {
  max-width: 100%;
}
</style>
