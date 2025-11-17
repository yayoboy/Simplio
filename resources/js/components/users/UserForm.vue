<template>
  <div class="space-y-6">
    <!-- Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Name <span class="text-red-500">*</span>
      </label>
      <input
        v-model="form.name"
        type="text"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'border-red-500': errors.name }"
        placeholder="John Doe"
      />
      <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Email <span class="text-red-500">*</span>
      </label>
      <input
        v-model="form.email"
        type="email"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'border-red-500': errors.email }"
        placeholder="john@example.com"
      />
      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
    </div>

    <!-- Password (only for new users or if changing) -->
    <div v-if="!userId || showPasswordField">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Password <span v-if="!userId" class="text-red-500">*</span>
      </label>
      <input
        v-model="form.password"
        type="password"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'border-red-500': errors.password }"
        placeholder="Minimum 8 characters"
      />
      <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
    </div>

    <!-- Change Password Toggle (for editing) -->
    <div v-if="userId && !showPasswordField">
      <button
        @click="showPasswordField = true"
        type="button"
        class="text-sm text-blue-600 hover:text-blue-700"
      >
        Change Password
      </button>
    </div>

    <!-- Role -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Role <span class="text-red-500">*</span>
      </label>
      <select
        v-model="form.role"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'border-red-500': errors.role }"
      >
        <option value="">Select a role</option>
        <option v-for="role in roles" :key="role.value" :value="role.value">
          {{ role.label }}
        </option>
      </select>
      <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role }}</p>

      <!-- Role descriptions -->
      <div class="mt-2 text-sm text-gray-600">
        <p v-if="form.role === 'admin'" class="text-blue-600">
          <strong>Administrator:</strong> Full access to all features including user management
        </p>
        <p v-else-if="form.role === 'editor'" class="text-green-600">
          <strong>Editor:</strong> Can manage sites, pages, and content
        </p>
        <p v-else-if="form.role === 'user'" class="text-gray-600">
          <strong>User:</strong> Limited access to assigned sites
        </p>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end gap-3 pt-4 border-t">
      <button
        @click="$emit('cancel')"
        type="button"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
      >
        Cancel
      </button>
      <button
        @click="handleSubmit"
        :disabled="loading"
        type="button"
        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ loading ? 'Saving...' : (userId ? 'Update User' : 'Create User') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useUsersStore } from '../../stores/users';

const props = defineProps({
  userId: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['cancel', 'saved']);

const usersStore = useUsersStore();

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
});

const errors = ref({});
const loading = ref(false);
const roles = ref([]);
const showPasswordField = ref(false);

onMounted(async () => {
  // Load roles
  try {
    roles.value = await usersStore.fetchRoles();
  } catch (error) {
    console.error('Failed to load roles:', error);
  }

  // Load user data if editing
  if (props.userId) {
    try {
      const user = await usersStore.fetchUser(props.userId);
      form.value.name = user.name;
      form.value.email = user.email;
      form.value.role = user.role;
    } catch (error) {
      console.error('Failed to load user:', error);
    }
  }
});

watch(() => props.userId, async (newUserId) => {
  if (newUserId) {
    try {
      const user = await usersStore.fetchUser(newUserId);
      form.value.name = user.name;
      form.value.email = user.email;
      form.value.role = user.role;
      form.value.password = '';
      showPasswordField.value = false;
    } catch (error) {
      console.error('Failed to load user:', error);
    }
  } else {
    form.value = { name: '', email: '', password: '', role: '' };
    showPasswordField.value = false;
  }
});

function validateForm() {
  errors.value = {};

  if (!form.value.name) {
    errors.value.name = 'Name is required';
  }

  if (!form.value.email) {
    errors.value.email = 'Email is required';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Invalid email format';
  }

  if (!props.userId && !form.value.password) {
    errors.value.password = 'Password is required';
  } else if (form.value.password && form.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters';
  }

  if (!form.value.role) {
    errors.value.role = 'Role is required';
  }

  return Object.keys(errors.value).length === 0;
}

async function handleSubmit() {
  if (!validateForm()) {
    return;
  }

  loading.value = true;

  try {
    const userData = {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role,
    };

    // Only include password if it's set
    if (form.value.password) {
      userData.password = form.value.password;
    }

    if (props.userId) {
      await usersStore.updateUser(props.userId, userData);
    } else {
      await usersStore.createUser(userData);
    }

    emit('saved');
  } catch (error) {
    // Handle validation errors from backend
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      alert(error.response?.data?.message || 'Failed to save user');
    }
  } finally {
    loading.value = false;
  }
}
</script>
