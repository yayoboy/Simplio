<template>
  <div class="px-4 sm:px-0">
    <div v-if="sitesStore.loading && !site" class="text-center py-12">
      <div class="text-gray-500">Loading...</div>
    </div>

    <div v-else-if="site">
      <!-- Site Header -->
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ site.name }}</h2>
        <p class="text-gray-500">{{ site.description }}</p>
      </div>

      <!-- Quick Actions -->
      <div class="flex gap-3 mb-6">
        <button
          @click="goToMediaLibrary"
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Media Library
        </button>
      </div>

      <!-- Pages Section -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-medium text-gray-900">Pages</h3>
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Page
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="pagesStore.loading" class="text-center py-8">
          <div class="text-gray-500">Loading pages...</div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!pagesStore.pages.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No pages</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating your first page.</p>
          <div class="mt-6">
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Create Your First Page
            </button>
          </div>
        </div>

        <!-- Pages Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="page in pagesStore.pages" :key="page.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ page.title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-500">/{{ page.slug }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      page.is_published
                        ? 'bg-green-100 text-green-800'
                        : 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ page.is_published ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="page.is_home" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    Home Page
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-3">
                    <!-- Set as Home Button -->
                    <button
                      v-if="!page.is_home"
                      @click="handleSetAsHome(page)"
                      class="text-blue-600 hover:text-blue-900"
                      title="Set as home page"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                      </svg>
                    </button>

                    <!-- Page Builder Button -->
                    <button
                      @click="openBuilder(page)"
                      class="px-3 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200"
                      title="Open Page Builder"
                    >
                      Builder
                    </button>

                    <!-- Publish/Unpublish Button -->
                    <button
                      @click="handleTogglePublish(page)"
                      :class="[
                        'px-3 py-1 rounded text-xs font-medium',
                        page.is_published
                          ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                          : 'bg-green-100 text-green-700 hover:bg-green-200'
                      ]"
                    >
                      {{ page.is_published ? 'Unpublish' : 'Publish' }}
                    </button>

                    <!-- Edit Button -->
                    <button
                      @click="openEditModal(page)"
                      class="text-blue-600 hover:text-blue-900"
                      title="Edit page"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>

                    <!-- Duplicate Button -->
                    <button
                      @click="openDuplicateModal(page)"
                      class="text-purple-600 hover:text-purple-900"
                      title="Duplicate page"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                      </svg>
                    </button>

                    <!-- Delete Button -->
                    <button
                      @click="openDeleteModal(page)"
                      class="text-red-600 hover:text-red-900"
                      title="Delete page"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Page Modal -->
    <Modal
      v-model="showFormModal"
      :title="isEditing ? 'Edit Page' : 'Create Page'"
      :confirm-text="isEditing ? 'Update' : 'Create'"
      :confirm-disabled="formLoading"
      @confirm="handleSubmit"
    >
      <PageForm
        ref="pageFormRef"
        :page="editingPage"
        :error="formError"
      />
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal
      v-model="showDeleteModal"
      title="Delete Page"
      confirm-text="Delete"
      :confirm-disabled="deleteLoading"
      @confirm="handleDelete"
    >
      <div class="text-sm text-gray-500">
        <p>Are you sure you want to delete <strong>{{ deletingPage?.title }}</strong>?</p>
        <p class="mt-2 text-red-600">This action cannot be undone.</p>
      </div>
    </Modal>

    <!-- Duplicate Page Modal -->
    <Modal
      v-model="showDuplicateModal"
      title="Duplicate Page"
      confirm-text="Duplicate"
      :confirm-disabled="duplicateLoading"
      @confirm="handleDuplicate"
    >
      <div class="space-y-4">
        <p class="text-sm text-gray-500">Enter a title for the duplicated page:</p>
        <input
          v-model="duplicateTitle"
          type="text"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="Page title"
        />
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSitesStore } from '@/stores/sites';
import { usePagesStore } from '@/stores/pages';
import Modal from '@/components/ui/Modal.vue';
import PageForm from '@/components/pages/PageForm.vue';

const route = useRoute();
const router = useRouter();
const sitesStore = useSitesStore();
const pagesStore = usePagesStore();

const site = ref(null);
const siteId = computed(() => route.params.id);

// Form Modal State
const showFormModal = ref(false);
const isEditing = ref(false);
const editingPage = ref(null);
const pageFormRef = ref(null);
const formLoading = ref(false);
const formError = ref('');

// Delete Modal State
const showDeleteModal = ref(false);
const deletingPage = ref(null);
const deleteLoading = ref(false);

// Duplicate Modal State
const showDuplicateModal = ref(false);
const duplicatingPage = ref(null);
const duplicateTitle = ref('');
const duplicateLoading = ref(false);

async function fetchSite() {
  try {
    site.value = await sitesStore.fetchSite(siteId.value);
  } catch (error) {
    console.error('Failed to fetch site:', error);
  }
}

async function fetchPages() {
  try {
    await pagesStore.fetchPages(siteId.value);
  } catch (error) {
    console.error('Failed to fetch pages:', error);
  }
}

function openCreateModal() {
  isEditing.value = false;
  editingPage.value = null;
  formError.value = '';
  showFormModal.value = true;
}

function openEditModal(page) {
  isEditing.value = true;
  editingPage.value = page;
  formError.value = '';
  showFormModal.value = true;
}

async function handleSubmit() {
  formLoading.value = true;
  formError.value = '';
  try {
    const data = pageFormRef.value.form;
    if (isEditing.value) {
      await pagesStore.updatePage(editingPage.value.id, data);
    } else {
      await pagesStore.createPage(siteId.value, data);
    }
    showFormModal.value = false;
  } catch (err) {
    formError.value = err.response?.data?.message || 'Operation failed';
  } finally {
    formLoading.value = false;
  }
}

function openDeleteModal(page) {
  deletingPage.value = page;
  showDeleteModal.value = true;
}

async function handleDelete() {
  deleteLoading.value = true;
  try {
    await pagesStore.deletePage(deletingPage.value.id);
    showDeleteModal.value = false;
    deletingPage.value = null;
  } catch (error) {
    console.error('Failed to delete page:', error);
  } finally {
    deleteLoading.value = false;
  }
}

function openDuplicateModal(page) {
  duplicatingPage.value = page;
  duplicateTitle.value = `${page.title} (Copy)`;
  showDuplicateModal.value = true;
}

async function handleDuplicate() {
  duplicateLoading.value = true;
  try {
    await pagesStore.duplicatePage(duplicatingPage.value.id, duplicateTitle.value);
    showDuplicateModal.value = false;
    duplicatingPage.value = null;
    duplicateTitle.value = '';
  } catch (error) {
    console.error('Failed to duplicate page:', error);
  } finally {
    duplicateLoading.value = false;
  }
}

async function handleTogglePublish(page) {
  try {
    if (page.is_published) {
      await pagesStore.unpublishPage(page.id);
    } else {
      await pagesStore.publishPage(page.id);
    }
  } catch (error) {
    console.error('Failed to toggle publish status:', error);
  }
}

async function handleSetAsHome(page) {
  try {
    await pagesStore.setAsHome(page.id);
  } catch (error) {
    console.error('Failed to set page as home:', error);
  }
}

function openBuilder(page) {
  router.push({
    name: 'page-builder',
    params: {
      siteId: siteId.value,
      pageId: page.id
    }
  });
}

function goToMediaLibrary() {
  router.push({
    name: 'media-library',
    params: {
      siteId: siteId.value
    }
  });
}

onMounted(() => {
  fetchSite();
  fetchPages();
});
</script>
