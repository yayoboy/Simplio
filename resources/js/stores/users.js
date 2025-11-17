import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useUsersStore = defineStore('users', () => {
  const users = ref([]);
  const currentUser = ref(null);
  const roles = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
  });

  /**
   * Fetch all users with optional filters
   */
  async function fetchUsers(filters = {}) {
    loading.value = true;
    error.value = null;

    try {
      const params = new URLSearchParams();

      if (filters.search) {
        params.append('search', filters.search);
      }

      if (filters.role) {
        params.append('role', filters.role);
      }

      if (filters.page) {
        params.append('page', filters.page);
      }

      const response = await axios.get(`/api/users?${params.toString()}`);

      users.value = response.data.data;
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
      };

      return users.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch users';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Fetch single user
   */
  async function fetchUser(userId) {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get(`/api/users/${userId}`);
      currentUser.value = response.data.data;
      return currentUser.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Fetch available roles
   */
  async function fetchRoles() {
    try {
      const response = await axios.get('/api/users/roles');
      roles.value = response.data.data;
      return roles.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch roles';
      throw err;
    }
  }

  /**
   * Create new user
   */
  async function createUser(userData) {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post('/api/users', userData);
      const newUser = response.data.data;

      // Add to beginning of list
      users.value.unshift(newUser);
      pagination.value.total += 1;

      return newUser;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create user';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Update existing user
   */
  async function updateUser(userId, userData) {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.put(`/api/users/${userId}`, userData);
      const updatedUser = response.data.data;

      // Update in list
      const index = users.value.findIndex(u => u.id === userId);
      if (index !== -1) {
        users.value[index] = updatedUser;
      }

      if (currentUser.value?.id === userId) {
        currentUser.value = updatedUser;
      }

      return updatedUser;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update user';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Delete user
   */
  async function deleteUser(userId) {
    loading.value = true;
    error.value = null;

    try {
      await axios.delete(`/api/users/${userId}`);

      // Remove from list
      const index = users.value.findIndex(u => u.id === userId);
      if (index !== -1) {
        users.value.splice(index, 1);
        pagination.value.total -= 1;
      }

      if (currentUser.value?.id === userId) {
        currentUser.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete user';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Reset store state
   */
  function $reset() {
    users.value = [];
    currentUser.value = null;
    roles.value = [];
    loading.value = false;
    error.value = null;
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
    };
  }

  return {
    users,
    currentUser,
    roles,
    loading,
    error,
    pagination,
    fetchUsers,
    fetchUser,
    fetchRoles,
    createUser,
    updateUser,
    deleteUser,
    $reset,
  };
});
