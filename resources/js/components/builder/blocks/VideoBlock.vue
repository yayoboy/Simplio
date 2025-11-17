<template>
  <div class="w-full">
    <!-- Video Embed -->
    <div v-if="embedUrl" class="relative overflow-hidden rounded-lg bg-gray-900" :style="{ paddingBottom: aspectRatioPadding }">
      <iframe
        :src="embedUrl"
        class="absolute top-0 left-0 w-full h-full"
        frameborder="0"
        :allow="allowAttributes"
        allowfullscreen
      ></iframe>
    </div>

    <!-- Empty State -->
    <div v-else class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">No video URL provided</p>
      <p class="text-xs text-gray-400">Add a YouTube or Vimeo URL via properties panel</p>
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

const embedUrl = computed(() => {
  const url = content.value.url;
  if (!url) return null;

  const provider = content.value.provider || 'youtube';
  const autoplay = content.value.autoplay ? 1 : 0;

  // YouTube
  if (provider === 'youtube' || url.includes('youtube.com') || url.includes('youtu.be')) {
    const videoId = extractYouTubeId(url);
    if (!videoId) return null;
    return `https://www.youtube.com/embed/${videoId}?autoplay=${autoplay}`;
  }

  // Vimeo
  if (provider === 'vimeo' || url.includes('vimeo.com')) {
    const videoId = extractVimeoId(url);
    if (!videoId) return null;
    return `https://player.vimeo.com/video/${videoId}?autoplay=${autoplay}`;
  }

  return null;
});

const aspectRatioPadding = computed(() => {
  const ratio = properties.value.aspectRatio || '16/9';
  if (ratio === '16/9') return '56.25%';
  if (ratio === '4/3') return '75%';
  if (ratio === '1/1') return '100%';
  return '56.25%';
});

const allowAttributes = computed(() => {
  const attrs = ['accelerometer', 'clipboard-write', 'encrypted-media', 'gyroscope', 'picture-in-picture'];
  if (content.value.autoplay) attrs.push('autoplay');
  return attrs.join('; ');
});

function extractYouTubeId(url) {
  const patterns = [
    /youtube\.com\/watch\?v=([^&]+)/,
    /youtu\.be\/([^?]+)/,
    /youtube\.com\/embed\/([^?]+)/,
  ];

  for (const pattern of patterns) {
    const match = url.match(pattern);
    if (match) return match[1];
  }

  return null;
}

function extractVimeoId(url) {
  const match = url.match(/vimeo\.com\/(\d+)/);
  return match ? match[1] : null;
}
</script>
