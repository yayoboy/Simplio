<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <button
              @click="goBack"
              class="text-gray-600 hover:text-gray-900 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </button>
            <div>
              <h1 class="text-xl font-semibold text-gray-900">Theme Customization</h1>
              <p v-if="currentSite" class="text-sm text-gray-600">{{ currentSite.name }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              v-if="hasChanges"
              @click="resetChanges"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm"
            >
              Reset
            </button>
            <button
              @click="saveTheme"
              :disabled="!hasChanges || saving"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors text-sm',
                hasChanges && !saving
                  ? 'bg-blue-600 text-white hover:bg-blue-700'
                  : 'bg-gray-300 text-gray-500 cursor-not-allowed'
              ]"
            >
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Editor Panel -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
              <nav class="flex -mb-px">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  @click="activeTab = tab.id"
                  :class="[
                    'flex-1 py-3 px-4 text-center border-b-2 font-medium text-sm transition-colors',
                    activeTab === tab.id
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  {{ tab.label }}
                </button>
              </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6 space-y-6 max-h-[calc(100vh-240px)] overflow-y-auto">
              <!-- Colors Tab -->
              <div v-if="activeTab === 'colors'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                  <div class="flex gap-2">
                    <input
                      v-model="themeData.colors.primary"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                      v-model="themeData.colors.primary"
                      type="text"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                  <div class="flex gap-2">
                    <input
                      v-model="themeData.colors.secondary"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                      v-model="themeData.colors.secondary"
                      type="text"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                  <div class="flex gap-2">
                    <input
                      v-model="themeData.colors.accent"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                      v-model="themeData.colors.accent"
                      type="text"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Background</label>
                  <div class="flex gap-2">
                    <input
                      v-model="themeData.colors.background"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                      v-model="themeData.colors.background"
                      type="text"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                  <div class="flex gap-2">
                    <input
                      v-model="themeData.colors.text"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                      v-model="themeData.colors.text"
                      type="text"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>
              </div>

              <!-- Typography Tab -->
              <div v-else-if="activeTab === 'typography'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                  <select
                    v-model="themeData.typography.fontFamily"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                  >
                    <option value="system-ui, -apple-system, sans-serif">System (Sans-serif)</option>
                    <option value="Georgia, serif">Georgia (Serif)</option>
                    <option value="'Courier New', monospace">Courier New (Monospace)</option>
                    <option value="'Inter', sans-serif">Inter</option>
                    <option value="'Roboto', sans-serif">Roboto</option>
                    <option value="'Open Sans', sans-serif">Open Sans</option>
                    <option value="'Lato', sans-serif">Lato</option>
                    <option value="'Montserrat', sans-serif">Montserrat</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Base Font Size</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.typography.baseFontSize"
                      type="range"
                      min="14"
                      max="20"
                      step="1"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-12">{{ themeData.typography.baseFontSize }}px</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Heading Font Weight</label>
                  <select
                    v-model.number="themeData.typography.headingWeight"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                  >
                    <option :value="400">Regular (400)</option>
                    <option :value="500">Medium (500)</option>
                    <option :value="600">Semi-bold (600)</option>
                    <option :value="700">Bold (700)</option>
                    <option :value="800">Extra-bold (800)</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Line Height</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.typography.lineHeight"
                      type="range"
                      min="1.2"
                      max="2"
                      step="0.1"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-12">{{ themeData.typography.lineHeight }}</span>
                  </div>
                </div>
              </div>

              <!-- Spacing Tab -->
              <div v-else-if="activeTab === 'spacing'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Container Max Width</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.spacing.containerWidth"
                      type="range"
                      min="960"
                      max="1920"
                      step="80"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-16">{{ themeData.spacing.containerWidth }}px</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Section Spacing</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.spacing.sectionSpacing"
                      type="range"
                      min="2"
                      max="8"
                      step="0.5"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-16">{{ themeData.spacing.sectionSpacing }}rem</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Element Spacing</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.spacing.elementSpacing"
                      type="range"
                      min="0.5"
                      max="3"
                      step="0.25"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-16">{{ themeData.spacing.elementSpacing }}rem</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Border Radius</label>
                  <div class="flex items-center gap-4">
                    <input
                      v-model.number="themeData.spacing.borderRadius"
                      type="range"
                      min="0"
                      max="24"
                      step="2"
                      class="flex-1"
                    />
                    <span class="text-sm text-gray-600 w-12">{{ themeData.spacing.borderRadius }}px</span>
                  </div>
                </div>
              </div>

              <!-- Advanced Tab -->
              <div v-else-if="activeTab === 'advanced'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Custom CSS</label>
                  <textarea
                    v-model="themeData.customCss"
                    rows="12"
                    placeholder="/* Add custom CSS here */"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                  ></textarea>
                  <p class="mt-1 text-xs text-gray-500">Advanced: Add custom CSS rules</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview Panel -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">Preview</h3>
              <div class="flex gap-2">
                <button
                  v-for="device in devices"
                  :key="device.id"
                  @click="activeDevice = device.id"
                  :class="[
                    'px-3 py-1.5 rounded-md text-sm transition-colors',
                    activeDevice === device.id
                      ? 'bg-blue-100 text-blue-700'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  ]"
                >
                  {{ device.label }}
                </button>
              </div>
            </div>

            <!-- Preview Container -->
            <div class="bg-gray-100 rounded-lg p-8 overflow-auto">
              <div
                :class="[
                  'bg-white mx-auto transition-all duration-300',
                  activeDevice === 'desktop' ? 'max-w-full' : '',
                  activeDevice === 'tablet' ? 'max-w-3xl' : '',
                  activeDevice === 'mobile' ? 'max-w-sm' : ''
                ]"
                :style="previewStyles"
              >
                <!-- Preview Content -->
                <div class="p-8 space-y-6">
                  <h1 class="text-4xl" :style="{ fontWeight: themeData.typography.headingWeight }">
                    Welcome to Your Site
                  </h1>
                  <p class="text-lg">
                    This is a preview of how your theme will look. The colors, typography, and spacing settings
                    are applied in real-time.
                  </p>

                  <div class="flex gap-4">
                    <button
                      class="px-6 py-3 rounded font-medium transition-colors"
                      :style="{
                        backgroundColor: themeData.colors.primary,
                        color: '#ffffff',
                        borderRadius: themeData.spacing.borderRadius + 'px'
                      }"
                    >
                      Primary Button
                    </button>
                    <button
                      class="px-6 py-3 rounded font-medium transition-colors"
                      :style="{
                        backgroundColor: themeData.colors.secondary,
                        color: '#ffffff',
                        borderRadius: themeData.spacing.borderRadius + 'px'
                      }"
                    >
                      Secondary Button
                    </button>
                  </div>

                  <div
                    class="p-6 rounded"
                    :style="{
                      backgroundColor: themeData.colors.accent + '20',
                      borderLeft: `4px solid ${themeData.colors.accent}`,
                      borderRadius: themeData.spacing.borderRadius + 'px'
                    }"
                  >
                    <h3 class="text-xl mb-2" :style="{ fontWeight: themeData.typography.headingWeight }">
                      Featured Content
                    </h3>
                    <p>This box demonstrates the accent color and border radius settings.</p>
                  </div>

                  <div class="grid grid-cols-3 gap-4">
                    <div
                      v-for="i in 3"
                      :key="i"
                      class="p-4 border rounded"
                      :style="{
                        borderRadius: themeData.spacing.borderRadius + 'px',
                        borderColor: themeData.colors.primary + '40'
                      }"
                    >
                      <h4 class="font-medium mb-2" :style="{ fontWeight: themeData.typography.headingWeight }">
                        Card {{ i }}
                      </h4>
                      <p class="text-sm">Sample card content</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSitesStore } from '@/stores/sites';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const sitesStore = useSitesStore();

const siteId = computed(() => route.params.siteId || route.params.id);
const currentSite = computed(() => sitesStore.currentSite);

const activeTab = ref('colors');
const activeDevice = ref('desktop');
const saving = ref(false);
const originalTheme = ref(null);

const tabs = [
  { id: 'colors', label: 'Colors' },
  { id: 'typography', label: 'Typography' },
  { id: 'spacing', label: 'Spacing' },
  { id: 'advanced', label: 'Advanced' },
];

const devices = [
  { id: 'desktop', label: 'Desktop' },
  { id: 'tablet', label: 'Tablet' },
  { id: 'mobile', label: 'Mobile' },
];

const themeData = ref({
  colors: {
    primary: '#3b82f6',
    secondary: '#6366f1',
    accent: '#8b5cf6',
    background: '#ffffff',
    text: '#1f2937',
  },
  typography: {
    fontFamily: 'system-ui, -apple-system, sans-serif',
    baseFontSize: 16,
    headingWeight: 700,
    lineHeight: 1.6,
  },
  spacing: {
    containerWidth: 1280,
    sectionSpacing: 4,
    elementSpacing: 1.5,
    borderRadius: 8,
  },
  customCss: '',
});

const previewStyles = computed(() => ({
  fontFamily: themeData.value.typography.fontFamily,
  fontSize: themeData.value.typography.baseFontSize + 'px',
  lineHeight: themeData.value.typography.lineHeight,
  color: themeData.value.colors.text,
  backgroundColor: themeData.value.colors.background,
  maxWidth: themeData.value.spacing.containerWidth + 'px',
}));

const hasChanges = computed(() => {
  if (!originalTheme.value) return false;
  return JSON.stringify(themeData.value) !== JSON.stringify(originalTheme.value);
});

function goBack() {
  if (hasChanges.value) {
    if (!confirm('You have unsaved changes. Are you sure you want to leave?')) {
      return;
    }
  }
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push({ name: 'sites' });
  }
}

function resetChanges() {
  if (originalTheme.value) {
    themeData.value = JSON.parse(JSON.stringify(originalTheme.value));
  }
}

async function saveTheme() {
  if (!siteId.value || saving.value) return;

  saving.value = true;
  try {
    const response = await axios.post(`/api/sites/${siteId.value}/theme`, {
      design_tokens: themeData.value,
    });

    originalTheme.value = JSON.parse(JSON.stringify(themeData.value));
    alert('Theme saved successfully!');
  } catch (error) {
    console.error('Failed to save theme:', error);
    alert('Failed to save theme: ' + (error.response?.data?.message || error.message));
  } finally {
    saving.value = false;
  }
}

async function loadTheme() {
  if (!siteId.value) return;

  try {
    const response = await axios.get(`/api/sites/${siteId.value}`);
    const site = response.data.data || response.data;

    if (site.theme && site.theme.design_tokens) {
      themeData.value = {
        ...themeData.value,
        ...site.theme.design_tokens,
      };
      originalTheme.value = JSON.parse(JSON.stringify(themeData.value));
    } else {
      originalTheme.value = JSON.parse(JSON.stringify(themeData.value));
    }
  } catch (error) {
    console.error('Failed to load theme:', error);
  }
}

onMounted(async () => {
  if (siteId.value && !currentSite.value) {
    try {
      await sitesStore.fetchSite(siteId.value);
    } catch (error) {
      console.error('Failed to load site:', error);
    }
  }
  await loadTheme();
});
</script>
