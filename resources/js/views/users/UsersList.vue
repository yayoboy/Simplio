<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
      <p class="mt-2 text-gray-600">
        Manage users and their roles
      </p>
    </div>

    <!-- Filters and Actions -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
      <!-- Search -->
      <div class="flex-1">
        <input
          v-model="searchQuery"
          @input="debouncedSearch"
          type="text"
          placeholder="Search users by name or email..."
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Role Filter -->
      <div class="w-full sm:w-48">
        <select
          v-model="roleFilter"
          @change="applyFilters"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Roles</option>
          <option v-for="role in roles" :key="role.value" :value="role.value">
            {{ role.label }}
          </option>
        </select>
      </div>

      <!-- Create User Button -->
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium whitespace-nowrap"
      >
        + Create User
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="usersStore.loading && !users.length" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Loading users...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="usersStore.error" class="bg-red-50 border border-red-200 rounded-md p-4">
      <p class="text-red-800">{{ usersStore.error }}</p>
      <button @click="loadUsers" class="mt-2 text-sm text-red-600 hover:text-red-700 underline">
        Try Again
      </button>
    </div>

    <!-- Users Table -->
    <div v-else-if="users.length" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              User
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Role
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Joined
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-medium">
                    {{ getUserInitials(user.name) }}
                  </span>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                :class="getRoleBadgeClass(user.role)"
              >
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openEditModal(user)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="confirmDelete(user)"
                class="text-red-600 hover:text-red-900"
                :disabled="user.id === currentUserId"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
          to {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
          of {{ pagination.total }} users
        </div>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-1 border rounded-md text-sm',
              page === pagination.current_page
                ? 'bg-blue-600 text-white border-blue-600'
                : 'border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white rounded-lg shadow">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchQuery || roleFilter ? 'Try adjusting your filters' : 'Get started by creating a new user' }}
      </p>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h2 class="text-xl font-bold mb-4">
          {{ editingUser ? 'Edit User' : 'Create New User' }}
        </h2>
        <UserForm
          :user-id="editingUser?.id"
          @cancel="closeModal"
          @saved="handleUserSaved"
        />
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="showDeleteConfirm = false"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h2 class="text-xl font-bold mb-4 text-red-600">Delete User</h2>
        <p class="mb-4 text-gray-700">
          Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?
          This action cannot be undone.
        </p>
        <div class="flex justify-end gap-3">
          <button
            @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="handleDelete"
            :disabled="usersStore.loading"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50"
          >
            {{ usersStore.loading ? 'Deleting...' : 'Delete User' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useUsersStore } from '../../stores/users';
import { useAuthStore } from '../../stores/auth';
import UserForm from '../../components/users/UserForm.vue';

const usersStore = useUsersStore();
const authStore = useAuthStore();

const searchQuery = ref('');
const roleFilter = ref('');
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);
const roles = ref([]);
let searchTimeout = null;

const users = computed(() => usersStore.users);
const pagination = computed(() => usersStore.pagination);
const currentUserId = computed(() => authStore.user?.id);

const visiblePages = computed(() => {
  const pages = [];
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;

  // Show up to 5 pages
  let start = Math.max(1, current - 2);
  let end = Math.min(last, start + 4);

  if (end - start < 4) {
    start = Math.max(1, end - 4);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

onMounted(async () => {
  await loadUsers();
  try {
    roles.value = await usersStore.fetchRoles();
  } catch (error) {
    console.error('Failed to load roles:', error);
  }
});

async function loadUsers() {
  try {
    await usersStore.fetchUsers({
      search: searchQuery.value,
      role: roleFilter.value,
    });
  } catch (error) {
    console.error('Failed to load users:', error);
  }
}

function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 500);
}

function applyFilters() {
  loadUsers();
}

function goToPage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    usersStore.fetchUsers({
      search: searchQuery.value,
      role: roleFilter.value,
      page,
    });
  }
}

function openCreateModal() {
  editingUser.value = null;
  showModal.value = true;
}

function openEditModal(user) {
  editingUser.value = user;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingUser.value = null;
}

function handleUserSaved() {
  closeModal();
  loadUsers();
}

function confirmDelete(user) {
  if (user.id === currentUserId.value) {
    alert('You cannot delete your own account');
    return;
  }
  userToDelete.value = user;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  try {
    await usersStore.deleteUser(userToDelete.value.id);
    showDeleteConfirm.value = false;
    userToDelete.value = null;
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to delete user');
  }
}

function getUserInitials(name) {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
}

function getRoleLabel(role) {
  const roleObj = roles.value.find(r => r.value === role);
  return roleObj?.label || role;
}

function getRoleBadgeClass(role) {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    editor: 'bg-blue-100 text-blue-800',
    user: 'bg-gray-100 text-gray-800',
  };
  return classes[role] || classes.user;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date);
}
</script>
