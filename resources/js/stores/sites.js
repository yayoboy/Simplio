import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useSitesStore = defineStore('sites', () => {
  const sites = ref([]);
  const currentSite = ref(null);
  const loading = ref(false);
  const error = ref(null);

  async function fetchSites() {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get('/api/sites');
      sites.value = response.data.data || response.data;
      return sites.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sites';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchSite(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/sites/${id}`);
      currentSite.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch site';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createSite(data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post('/api/sites', data);
      sites.value.unshift(response.data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create site';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateSite(id, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.put(`/api/sites/${id}`, data);
      const index = sites.value.findIndex(s => s.id === id);
      if (index !== -1) {
        sites.value[index] = response.data;
      }
      if (currentSite.value?.id === id) {
        currentSite.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update site';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteSite(id) {
    loading.value = true;
    error.value = null;
    try {
      await axios.delete(`/api/sites/${id}`);
      sites.value = sites.value.filter(s => s.id !== id);
      if (currentSite.value?.id === id) {
        currentSite.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete site';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function publishSite(id) {
    try {
      const response = await axios.post(`/api/sites/${id}/publish`);
      const index = sites.value.findIndex(s => s.id === id);
      if (index !== -1) {
        sites.value[index] = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to publish site';
      throw err;
    }
  }

  async function unpublishSite(id) {
    try {
      const response = await axios.post(`/api/sites/${id}/unpublish`);
      const index = sites.value.findIndex(s => s.id === id);
      if (index !== -1) {
        sites.value[index] = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to unpublish site';
      throw err;
    }
  }

  async function duplicateSite(id, name) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(`/api/sites/${id}/duplicate`, { name });
      sites.value.unshift(response.data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to duplicate site';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    sites,
    currentSite,
    loading,
    error,
    fetchSites,
    fetchSite,
    createSite,
    updateSite,
    deleteSite,
    publishSite,
    unpublishSite,
    duplicateSite,
  };
});
