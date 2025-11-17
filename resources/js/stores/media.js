import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useMediaStore = defineStore('media', () => {
  const media = ref([]);
  const currentMedia = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const uploadProgress = ref(0);

  /**
   * Fetch all media for a site
   */
  async function fetchMedia(siteId, params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/sites/${siteId}/media`, { params });
      media.value = response.data.data || response.data;
      return media.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch media';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Fetch a single media item
   */
  async function fetchMediaItem(siteId, mediaId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/sites/${siteId}/media/${mediaId}`);
      currentMedia.value = response.data.data;
      return currentMedia.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch media';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Upload a new media file
   */
  async function uploadMedia(siteId, file, metadata = {}) {
    loading.value = true;
    error.value = null;
    uploadProgress.value = 0;

    try {
      const formData = new FormData();
      formData.append('file', file);

      if (metadata.alt_text) {
        formData.append('alt_text', metadata.alt_text);
      }
      if (metadata.caption) {
        formData.append('caption', metadata.caption);
      }

      const response = await axios.post(`/api/sites/${siteId}/media`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        },
      });

      const newMedia = response.data.data;
      media.value.unshift(newMedia);
      uploadProgress.value = 0;
      return newMedia;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to upload media';
      uploadProgress.value = 0;
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Upload multiple files at once
   */
  async function uploadMultiple(siteId, files, metadata = {}) {
    const promises = Array.from(files).map((file) => uploadMedia(siteId, file, metadata));
    try {
      const results = await Promise.all(promises);
      return results;
    } catch (err) {
      error.value = 'Failed to upload one or more files';
      throw err;
    }
  }

  /**
   * Update media metadata
   */
  async function updateMedia(siteId, mediaId, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.put(`/api/sites/${siteId}/media/${mediaId}`, data);
      const updatedMedia = response.data.data;

      const index = media.value.findIndex((m) => m.id === mediaId);
      if (index !== -1) {
        media.value[index] = updatedMedia;
      }

      if (currentMedia.value?.id === mediaId) {
        currentMedia.value = updatedMedia;
      }

      return updatedMedia;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update media';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Delete a media item
   */
  async function deleteMedia(siteId, mediaId) {
    loading.value = true;
    error.value = null;
    try {
      await axios.delete(`/api/sites/${siteId}/media/${mediaId}`);
      media.value = media.value.filter((m) => m.id !== mediaId);
      if (currentMedia.value?.id === mediaId) {
        currentMedia.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete media';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Regenerate image variants
   */
  async function regenerateVariants(mediaId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(`/api/media/${mediaId}/regenerate`);
      const updatedMedia = response.data.data;

      const index = media.value.findIndex((m) => m.id === mediaId);
      if (index !== -1) {
        media.value[index] = updatedMedia;
      }

      if (currentMedia.value?.id === mediaId) {
        currentMedia.value = updatedMedia;
      }

      return updatedMedia;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to regenerate variants';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Filter media by type
   */
  function filterByType(type) {
    if (!type || type === 'all') {
      return media.value;
    }
    return media.value.filter((item) => {
      if (type === 'images') {
        return item.mime_type.startsWith('image/');
      }
      if (type === 'videos') {
        return item.mime_type.startsWith('video/');
      }
      if (type === 'documents') {
        return !item.mime_type.startsWith('image/') && !item.mime_type.startsWith('video/');
      }
      return true;
    });
  }

  /**
   * Search media by filename, alt text, or caption
   */
  function searchMedia(query) {
    if (!query) {
      return media.value;
    }
    const lowerQuery = query.toLowerCase();
    return media.value.filter((item) => {
      return (
        item.original_filename?.toLowerCase().includes(lowerQuery) ||
        item.alt_text?.toLowerCase().includes(lowerQuery) ||
        item.caption?.toLowerCase().includes(lowerQuery)
      );
    });
  }

  /**
   * Clear error state
   */
  function clearError() {
    error.value = null;
  }

  /**
   * Reset store state
   */
  function reset() {
    media.value = [];
    currentMedia.value = null;
    loading.value = false;
    error.value = null;
    uploadProgress.value = 0;
  }

  return {
    // State
    media,
    currentMedia,
    loading,
    error,
    uploadProgress,

    // Actions
    fetchMedia,
    fetchMediaItem,
    uploadMedia,
    uploadMultiple,
    updateMedia,
    deleteMedia,
    regenerateVariants,
    filterByType,
    searchMedia,
    clearError,
    reset,
  };
});
