<template>
  <div>
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button @click="toggleSidebar" class="hamburger-btn" aria-label="Open menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div class="mobile-brand">
        <div class="brand-dot"></div>
        <span>{{ title }}</span>
      </div>
      <div class="mobile-avatar">{{ userInitial }}</div>
    </div>

    <!-- Overlay -->
    <transition name="fade">
      <div v-if="isOpen" class="sidebar-overlay" @click="closeSidebar"></div>
    </transition>

    <!-- Drawer -->
    <transition name="slide">
      <aside v-if="isOpen" class="mobile-sidebar">
        <div class="sidebar-brand">
          <div class="brand-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <span class="brand-name">WorkflowOS</span>
          <button @click="closeSidebar" class="close-btn" aria-label="Close menu">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <nav class="sidebar-nav">
          <div class="nav-section-label">Main</div>
          <router-link to="/dashboard" class="nav-item" active-class="active" @click="closeSidebar">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
          </router-link>
          <router-link to="/requests" class="nav-item" active-class="active" @click="closeSidebar">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
            </svg>
            My Requests
          </router-link>

          <template v-if="authStore.user?.role === 'admin'">
            <div class="nav-section-label">Admin</div>
            <router-link to="/admin/requests" class="nav-item" active-class="active" @click="closeSidebar">
              <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
              All Requests
            </router-link>
            <router-link to="/admin/users" class="nav-item" active-class="active" @click="closeSidebar">
              <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
              </svg>
              Users
            </router-link>
            <router-link to="/admin/settings" class="nav-item" active-class="active" @click="closeSidebar">
              <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M2 12h2M20 12h2"/>
              </svg>
              Settings
            </router-link>
          </template>

          <div class="nav-section-label">Account</div>
          <router-link to="/profile" class="nav-item" active-class="active" @click="closeSidebar">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Profile
          </router-link>
          <router-link to="/activity-logs" class="nav-item" active-class="active" @click="closeSidebar">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            Activity Logs
          </router-link>
        </nav>

        <div class="sidebar-footer">
          <div class="user-card">
            <div class="user-avatar">{{ userInitial }}</div>
            <div class="user-meta">
              <div class="user-name">{{ authStore.user?.name }}</div>
              <div class="user-email">{{ authStore.user?.email }}</div>
            </div>
          </div>
          <button @click="handleLogout" class="logout-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
          </button>
        </div>
      </aside>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

defineProps({ title: { type: String, default: 'Dashboard' } })

const router = useRouter()
const authStore = useAuthStore()
const isOpen = ref(false)

const userInitial = computed(() => authStore.user?.name?.[0]?.toUpperCase() || '?')

const toggleSidebar = () => { isOpen.value = !isOpen.value }
const closeSidebar = () => { isOpen.value = false }

const handleLogout = async () => {
  await authStore.logout()
  closeSidebar()
  router.push('/login')
}
</script>

<style scoped>
/* Mobile header */
.mobile-header {
  display: none;
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
  padding: 0 1rem;
  height: 52px;
  align-items: center;
  gap: 0.75rem;
  position: sticky;
  top: 0;
  z-index: 100;
}

.hamburger-btn {
  background: none;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--transition);
  flex-shrink: 0;
}

.hamburger-btn:hover {
  background: var(--bg-hover);
  color: var(--text-primary);
}

.mobile-brand {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary);
}

.brand-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--accent);
}

.mobile-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--accent);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Overlay */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  z-index: 998;
  backdrop-filter: blur(2px);
}

/* Drawer */
.mobile-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 260px;
  background: var(--bg-surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  z-index: 999;
  overflow-y: auto;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 1rem;
  border-bottom: 1px solid var(--border);
}

.brand-icon {
  width: 30px;
  height: 30px;
  background: var(--accent);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.brand-name {
  flex: 1;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--text-primary);
}

.close-btn {
  background: none;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-muted);
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--transition);
}

.close-btn:hover {
  background: var(--bg-hover);
  color: var(--text-primary);
}

.sidebar-nav {
  flex: 1;
  padding: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-section-label {
  font-size: 0.68rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  padding: 0.65rem 0.5rem 0.2rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.65rem 0.75rem;
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: background var(--transition), color var(--transition);
}

.nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
.nav-item.active {
  background: var(--accent-glow);
  color: var(--accent);
  border: 1px solid var(--accent-border);
}

.nav-icon { width: 16px; height: 16px; flex-shrink: 0; }

.sidebar-footer {
  padding: 0.75rem;
  border-top: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.65rem;
  background: var(--bg-elevated);
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--accent);
  color: #fff;
  font-size: 0.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-meta { min-width: 0; }
.user-name {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.user-email {
  font-size: 0.72rem;
  color: var(--text-muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.6rem;
  background: var(--danger-bg);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: var(--radius-sm);
  color: var(--danger);
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
}

.logout-btn:hover { background: var(--danger); color: #fff; }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.25s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(-100%); }

@media (max-width: 768px) {
  .mobile-header { display: flex; }
}
</style>
