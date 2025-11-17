import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const usePagesStore = defineStore('pages', () => {
  const pages = ref([]);
  const currentPage = ref(null);
  const loading = ref(false);
  const error = ref(null);

  async function fetchPages(siteId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/sites/${siteId}/pages`);
      pages.value = response.data.data || response.data;
      return pages.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch pages';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchPage(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/pages/${id}`);
      currentPage.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createPage(siteId, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(`/api/sites/${siteId}/pages`, data);
      pages.value.unshift(response.data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updatePage(id, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.put(`/api/pages/${id}`, data);
      const index = pages.value.findIndex(p => p.id === id);
      if (index !== -1) {
        pages.value[index] = response.data;
      }
      if (currentPage.value?.id === id) {
        currentPage.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deletePage(id) {
    loading.value = true;
    error.value = null;
    try {
      await axios.delete(`/api/pages/${id}`);
      pages.value = pages.value.filter(p => p.id !== id);
      if (currentPage.value?.id === id) {
        currentPage.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function publishPage(id) {
    try {
      const response = await axios.post(`/api/pages/${id}/publish`);
      const index = pages.value.findIndex(p => p.id === id);
      if (index !== -1) {
        pages.value[index] = response.data;
      }
      if (currentPage.value?.id === id) {
        currentPage.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to publish page';
      throw err;
    }
  }

  async function unpublishPage(id) {
    try {
      const response = await axios.post(`/api/pages/${id}/unpublish`);
      const index = pages.value.findIndex(p => p.id === id);
      if (index !== -1) {
        pages.value[index] = response.data;
      }
      if (currentPage.value?.id === id) {
        currentPage.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to unpublish page';
      throw err;
    }
  }

  async function duplicatePage(id, title) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(`/api/pages/${id}/duplicate`, { title });
      pages.value.unshift(response.data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to duplicate page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function setAsHome(id) {
    try {
      const response = await axios.post(`/api/pages/${id}/set-home`);
      // Update all pages in the list to reflect the new home page
      pages.value = pages.value.map(p => ({
        ...p,
        is_home: p.id === id
      }));
      if (currentPage.value?.id === id) {
        currentPage.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to set page as home';
      throw err;
    }
  }

  return {
    pages,
    currentPage,
    loading,
    error,
    fetchPages,
    fetchPage,
    createPage,
    updatePage,
    deletePage,
    publishPage,
    unpublishPage,
    duplicatePage,
    setAsHome,
  };
});
