<template>
  <div class="space-y-6">
    <!-- Search -->
    <div>
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search blocks..."
        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
      />
    </div>

    <!-- Categories -->
    <div v-for="category in filteredCategories" :key="category.id" class="space-y-2">
      <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
        {{ category.label }}
      </h3>
      <div class="space-y-1">
        <button
          v-for="block in category.blocks"
          :key="block.type"
          @click="addBlock(block.type)"
          class="w-full flex items-start p-3 text-left border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors group"
        >
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-gray-100 group-hover:bg-blue-100">
            <component :is="getIconComponent(block.icon)" class="w-5 h-5 text-gray-600 group-hover:text-blue-600" />
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900">{{ block.label }}</p>
            <p class="text-xs text-gray-500 truncate">{{ block.description }}</p>
          </div>
        </button>
      </div>
    </div>

    <!-- No Results -->
    <div v-if="filteredCategories.length === 0" class="text-center py-8">
      <p class="text-sm text-gray-500">No blocks found</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { BLOCK_CONFIGS, BLOCK_CATEGORIES, getBlocksByCategory } from '@/config/blockTypes';

const emit = defineEmits(['add-block']);

const searchQuery = ref('');

// Organize blocks by category
const categories = computed(() => {
  return Object.values(BLOCK_CATEGORIES).map(category => ({
    ...category,
    blocks: getBlocksByCategory(category.id)
  })).filter(category => category.blocks.length > 0);
});

// Filter categories based on search
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value;

  const query = searchQuery.value.toLowerCase();
  return categories.value
    .map(category => ({
      ...category,
      blocks: category.blocks.filter(block =>
        block.label.toLowerCase().includes(query) ||
        block.description.toLowerCase().includes(query)
      )
    }))
    .filter(category => category.blocks.length > 0);
});

function addBlock(type) {
  emit('add-block', type);
}

// Simple icon component mapping
function getIconComponent(iconName) {
  const icons = {
    text: TextIcon,
    heading: HeadingIcon,
    image: ImageIcon,
    images: ImagesIcon,
    video: VideoIcon,
    code: CodeIcon,
    button: ButtonIcon,
    divider: DividerIcon,
    spacer: SpacerIcon,
    container: ContainerIcon,
    columns: ColumnsIcon,
  };
  return icons[iconName] || TextIcon;
}
</script>

<script>
// Icon components (inline SVG)
const TextIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>`
};

const HeadingIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>`
};

const ImageIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`
};

const ImagesIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`
};

const VideoIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>`
};

const CodeIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>`
};

const ButtonIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>`
};

const DividerIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>`
};

const SpacerIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>`
};

const ContainerIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`
};

const ColumnsIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a2 2 0 00-2 2v12a2 2 0 002 2h4m0-16h4m-4 0v16m4-16h4a2 2 0 012 2v12a2 2 0 01-2 2h-4m0-16v16"/></svg>`
};

export { TextIcon, HeadingIcon, ImageIcon, ImagesIcon, VideoIcon, CodeIcon, ButtonIcon, DividerIcon, SpacerIcon, ContainerIcon, ColumnsIcon };
</script>
