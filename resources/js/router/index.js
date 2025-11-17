import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/auth/Register.vue'),
    meta: { guest: true },
  },
  {
    path: '/',
    component: () => import('../views/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('../views/Dashboard.vue'),
      },
      {
        path: 'sites',
        name: 'sites',
        component: () => import('../views/sites/SitesList.vue'),
      },
      {
        path: 'sites/:id',
        name: 'site-detail',
        component: () => import('../views/sites/SiteDetail.vue'),
      },
      {
        path: 'sites/:siteId/pages/:pageId/builder',
        name: 'page-builder',
        component: () => import('../views/pages/PageBuilder.vue'),
      },
      {
        path: 'sites/:siteId/media',
        name: 'media-library',
        component: () => import('../views/media/MediaLibrary.vue'),
      },
      {
        path: 'sites/:siteId/theme',
        name: 'theme-editor',
        component: () => import('../views/themes/ThemeEditor.vue'),
      },
      {
        path: 'users',
        name: 'users',
        component: () => import('../views/users/UsersList.vue'),
        meta: { requiresAdmin: true },
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' });
  } else if (to.meta.guest && isAuthenticated) {
    next({ name: 'dashboard' });
  } else if (to.meta.requiresAdmin) {
    // Check if user is admin
    if (authStore.user?.role === 'admin') {
      next();
    } else {
      // Redirect non-admin users to dashboard
      next({ name: 'dashboard' });
    }
  } else {
    next();
  }
});

export default router;
