<template>
  <div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Sites</h2>
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Create Site
      </button>
    </div>

    <!-- Loading state -->
    <div v-if="sitesStore.loading" class="text-center py-12">
      <div class="text-gray-500">Loading...</div>
    </div>

    <!-- Empty state -->
    <div v-else-if="sitesStore.sites.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
      <h3 class="text-lg font-medium text-gray-900 mb-2">No sites yet</h3>
      <p class="text-gray-500 mb-4">Create your first site to get started</p>
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        Create Your First Site
      </button>
    </div>

    <!-- Sites grid -->
    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="site in sitesStore.sites"
        :key="site.id"
        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow"
      >
        <div class="p-5">
          <div class="flex justify-between items-start">
            <h3 class="text-lg font-medium text-gray-900">{{ site.name }}</h3>
            <div class="flex space-x-1">
              <button
                @click="openEditModal(site)"
                class="text-gray-400 hover:text-gray-600 p-1"
                title="Edit"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="confirmDelete(site)"
                class="text-red-400 hover:text-red-600 p-1"
                title="Delete"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
          <p class="mt-1 text-sm text-gray-500">{{ site.description || 'No description' }}</p>
          <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
            <span>{{ site.pages_count || 0 }} pages</span>
            <span
              :class="[
                'px-2 py-1 rounded-full text-xs font-medium',
                site.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ site.is_published ? 'Published' : 'Draft' }}
            </span>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3 flex justify-between items-center">
          <router-link
            :to="{ name: 'site-detail', params: { id: site.id } }"
            class="text-sm text-blue-600 hover:text-blue-500"
          >
            Manage →
          </router-link>
          <div class="flex space-x-2">
            <button
              @click="togglePublish(site)"
              :class="[
                'text-xs px-2 py-1 rounded',
                site.is_published
                  ? 'text-gray-600 hover:text-gray-800'
                  : 'text-blue-600 hover:text-blue-800'
              ]"
            >
              {{ site.is_published ? 'Unpublish' : 'Publish' }}
            </button>
            <button
              @click="openDuplicateModal(site)"
              class="text-xs text-gray-600 hover:text-gray-800 px-2 py-1 rounded"
            >
              Duplicate
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal
      v-model="showFormModal"
      :title="isEditing ? 'Edit Site' : 'Create Site'"
      :confirm-text="isEditing ? 'Update' : 'Create'"
      :confirm-disabled="formLoading"
      @confirm="handleSubmit"
    >
      <SiteForm
        ref="siteFormRef"
        :site="editingSite"
        :error="formError"
        v-model="formData"
      />
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal
      v-model="showDeleteModal"
      title="Delete Site"
      confirm-text="Delete"
      @confirm="handleDelete"
    >
      <p class="text-sm text-gray-500">
        Are you sure you want to delete <strong>{{ deletingSite?.name }}</strong>?
        This action cannot be undone and will delete all pages and content.
      </p>
    </Modal>

    <!-- Duplicate Modal -->
    <Modal
      v-model="showDuplicateModal"
      title="Duplicate Site"
      confirm-text="Duplicate"
      :confirm-disabled="!duplicateName"
      @confirm="handleDuplicate"
    >
      <div>
        <label for="duplicate-name" class="block text-sm font-medium text-gray-700 mb-2">
          New Site Name
        </label>
        <input
          id="duplicate-name"
          v-model="duplicateName"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="My Site (Copy)"
        />
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSitesStore } from '../../stores/sites';
import Modal from '../../components/ui/Modal.vue';
import SiteForm from '../../components/sites/SiteForm.vue';

const sitesStore = useSitesStore();

const showFormModal = ref(false);
const showDeleteModal = ref(false);
const showDuplicateModal = ref(false);
const isEditing = ref(false);
const editingSite = ref(null);
const deletingSite = ref(null);
const duplicatingSite = ref(null);
const duplicateName = ref('');
const formData = ref({});
const formError = ref('');
const formLoading = ref(false);
const siteFormRef = ref(null);

function openCreateModal() {
  isEditing.value = false;
  editingSite.value = null;
  formData.value = {};
  formError.value = '';
  showFormModal.value = true;
}

function openEditModal(site) {
  isEditing.value = true;
  editingSite.value = site;
  formError.value = '';
  showFormModal.value = true;
}

function confirmDelete(site) {
  deletingSite.value = site;
  showDeleteModal.value = true;
}

function openDuplicateModal(site) {
  duplicatingSite.value = site;
  duplicateName.value = `${site.name} (Copy)`;
  showDuplicateModal.value = true;
}

async function handleSubmit() {
  formLoading.value = true;
  formError.value = '';

  try {
    const data = siteFormRef.value.form;

    if (isEditing.value) {
      await sitesStore.updateSite(editingSite.value.id, data);
    } else {
      await sitesStore.createSite(data);
    }

    showFormModal.value = false;
  } catch (err) {
    formError.value = err.response?.data?.message || 'Operation failed. Please try again.';
  } finally {
    formLoading.value = false;
  }
}

async function handleDelete() {
  try {
    await sitesStore.deleteSite(deletingSite.value.id);
    showDeleteModal.value = false;
    deletingSite.value = null;
  } catch (err) {
    console.error('Delete failed:', err);
  }
}

async function handleDuplicate() {
  try {
    await sitesStore.duplicateSite(duplicatingSite.value.id, duplicateName.value);
    showDuplicateModal.value = false;
    duplicatingSite.value = null;
    duplicateName.value = '';
  } catch (err) {
    console.error('Duplicate failed:', err);
  }
}

async function togglePublish(site) {
  try {
    if (site.is_published) {
      await sitesStore.unpublishSite(site.id);
    } else {
      await sitesStore.publishSite(site.id);
    }
  } catch (err) {
    console.error('Publish toggle failed:', err);
  }
}

onMounted(() => {
  sitesStore.fetchSites();
});
</script>
