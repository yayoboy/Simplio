import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);

  const isAuthenticated = computed(() => !!token.value);

  function setToken(newToken) {
    token.value = newToken;
    if (newToken) {
      localStorage.setItem('token', newToken);
      axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
    } else {
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    }
  }

  async function login(credentials) {
    const response = await axios.post('/api/login', credentials);
    user.value = response.data.user;
    setToken(response.data.token);
    return response.data;
  }

  async function register(data) {
    const response = await axios.post('/api/register', data);
    user.value = response.data.user;
    setToken(response.data.token);
    return response.data;
  }

  async function logout() {
    await axios.post('/api/logout');
    user.value = null;
    setToken(null);
  }

  async function fetchUser() {
    if (!token.value) return;

    try {
      const response = await axios.get('/api/me');
      user.value = response.data;
    } catch (error) {
      setToken(null);
      user.value = null;
    }
  }

  // Initialize token in axios if exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    fetchUser();
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
  };
});
