import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import RequestsView from '../views/RequestsView.vue'
import ProfileView from '../views/ProfileView.vue'
import ActivityLogsView from '../views/ActivityLogsView.vue'
import UsersView from '../views/Admin/UsersView.vue'
import AdminRequestsView from '../views/Admin/RequestsView.vue'
import SettingsView from '../views/Admin/SettingsView.vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { requiresPublicRegistration: true },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    {
      path: '/requests',
      name: 'requests',
      component: RequestsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
      meta: { requiresAuth: true },
    },
    {
      path: '/activity-logs',
      name: 'activity-logs',
      component: ActivityLogsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/admin/users',
      name: 'admin-users',
      component: UsersView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/requests',
      name: 'admin-requests',
      component: AdminRequestsView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/settings',
      name: 'admin-settings',
      component: SettingsView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Check if route requires public registration
  if (to.meta.requiresPublicRegistration) {
    try {
      const response = await api.get('/registration-mode')
      if (!response.data.is_public) {
        // Registration is disabled, redirect to login
        next('/login')
        return
      }
    } catch (err) {
      console.error('Failed to check registration mode:', err)
      next('/login')
      return
    }
  }
  
  // Check authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated()) {
    next('/login')
  } else if (to.path === '/login' && authStore.isAuthenticated()) {
    next('/dashboard')
  } else if (to.meta.requiresAdmin && authStore.user?.role !== 'admin') {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
