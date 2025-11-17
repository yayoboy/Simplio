import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useBlocksStore = defineStore('blocks', () => {
  const blocks = ref([]);
  const selectedBlock = ref(null);
  const loading = ref(false);
  const error = ref(null);

  async function fetchBlocks(pageId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/pages/${pageId}/blocks`);
      blocks.value = response.data.data || response.data;
      return blocks.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch blocks';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createBlock(pageId, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post(`/api/pages/${pageId}/blocks`, data);
      blocks.value.push(response.data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create block';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateBlock(pageId, blockId, data) {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.put(`/api/pages/${pageId}/blocks/${blockId}`, data);
      const index = blocks.value.findIndex(b => b.id === blockId);
      if (index !== -1) {
        blocks.value[index] = response.data;
      }
      if (selectedBlock.value?.id === blockId) {
        selectedBlock.value = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update block';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteBlock(pageId, blockId) {
    loading.value = true;
    error.value = null;
    try {
      await axios.delete(`/api/pages/${pageId}/blocks/${blockId}`);
      blocks.value = blocks.value.filter(b => b.id !== blockId);
      if (selectedBlock.value?.id === blockId) {
        selectedBlock.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete block';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function reorderBlocks(pageId, blockIds) {
    try {
      await axios.post('/api/blocks/reorder', {
        page_id: pageId,
        blocks: blockIds
      });
      // Reorder local blocks array
      const orderedBlocks = blockIds.map(id =>
        blocks.value.find(b => b.id === id)
      ).filter(Boolean);
      blocks.value = orderedBlocks;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to reorder blocks';
      throw err;
    }
  }

  function selectBlock(block) {
    selectedBlock.value = block;
  }

  function deselectBlock() {
    selectedBlock.value = null;
  }

  return {
    blocks,
    selectedBlock,
    loading,
    error,
    fetchBlocks,
    createBlock,
    updateBlock,
    deleteBlock,
    reorderBlocks,
    selectBlock,
    deselectBlock,
  };
});
