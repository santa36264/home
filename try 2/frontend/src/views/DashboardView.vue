<template>
  <div class="dashboard-shell">
    <!-- Mobile nav -->
    <MobileSidebar title="Dashboard" class="mobile-only" />

    <!-- Desktop sidebar -->
    <aside class="sidebar desktop-only">
      <div class="sidebar-brand">
        <div class="brand-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <span>WorkflowOS</span>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <router-link to="/dashboard" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
          </svg>
          Dashboard
        </router-link>
        <router-link to="/requests" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
          My Requests
        </router-link>
        <template v-if="authStore.user?.role === 'admin'">
          <div class="nav-section-label">Admin</div>
          <router-link to="/admin/requests" class="nav-item" active-class="active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
            All Requests
          </router-link>
          <router-link to="/admin/users" class="nav-item" active-class="active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
            </svg>
            Users
          </router-link>
          <router-link to="/admin/settings" class="nav-item" active-class="active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
            </svg>
            Settings
          </router-link>
        </template>
        <div class="nav-section-label">Account</div>
        <router-link to="/profile" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Profile
        </router-link>
        <router-link to="/activity-logs" class="nav-item" active-class="active">
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
            <div class="user-role-tag" :class="authStore.user?.role">{{ authStore.user?.role }}</div>
          </div>
        </div>
        <button @click="handleLogout" class="logout-btn" title="Logout">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </button>
      </div>
    </aside>

    <!-- Content -->
    <div class="main-area">
      <header class="topbar desktop-only">
        <h1 class="page-title">Dashboard</h1>
        <div class="topbar-right">
          <div class="topbar-user">
            <div class="user-avatar sm">{{ userInitial }}</div>
            <span class="topbar-name">{{ authStore.user?.name }}</span>
            <span class="user-role-tag" :class="authStore.user?.role">{{ authStore.user?.role }}</span>
          </div>
          <button @click="handleLogout" class="topbar-logout">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
          </button>
        </div>
      </header>

      <div class="page-content">
        <div v-if="loading" class="loading-center">
          <LoadingSpinner size="large" text="Loading dashboard…" />
        </div>

        <template v-else>
          <!-- Welcome banner -->
          <div class="welcome-banner">
            <div class="welcome-text">
              <h2>Good to see you, {{ firstName }}!</h2>
              <p>Here's an overview of {{ authStore.user?.role === 'admin' ? 'the system' : 'your requests' }} today.</p>
            </div>
            <div class="welcome-badge" :class="authStore.user?.role">
              {{ authStore.user?.role === 'admin' ? 'Administrator' : 'User' }}
            </div>
          </div>

          <!-- Stats -->
          <div class="stats-grid">
            <StatCard icon="📊" title="Total Requests" :value="stats.total_requests || 0" color="blue" />
            <StatCard icon="⏳" title="Pending"        :value="stats.pending_requests || 0" color="orange" />
            <StatCard icon="✅" title="Approved"       :value="stats.approved_requests || 0" color="green" />
            <StatCard v-if="authStore.user?.role === 'admin'" icon="👥" title="Total Users" :value="stats.total_users || 0" color="purple" />
            <StatCard v-else icon="❌" title="Rejected" :value="stats.rejected_requests || 0" color="red" />
          </div>

          <!-- Recent activity -->
          <div class="section-card">
            <div class="section-header">
              <h3>Recent Activity</h3>
              <router-link to="/requests" class="view-all">View all →</router-link>
            </div>

            <div v-if="recentActivity.length === 0" class="empty-state">
              <div class="empty-icon">�</div>
              <p>No recent activity yet</p>
            </div>

            <div v-else class="activity-list">
              <div v-for="item in recentActivity" :key="item.id" class="activity-row">
                <div class="activity-status-dot" :class="item.status"></div>
                <div class="activity-info">
                  <div class="activity-title">
                    <span v-if="authStore.user?.role === 'admin'">
                      <strong>{{ item.user }}</strong> — {{ item.title }}
                    </span>
                    <span v-else>{{ item.title }}</span>
                  </div>
                  <div class="activity-meta">
                    <span class="status-chip" :class="item.status">{{ item.status }}</span>
                    <span class="activity-time">{{ item.created_at }}</span>
                  </div>
                </div>
                <div class="activity-amount">${{ item.total }}</div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import StatCard from '../components/StatCard.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import MobileSidebar from '../components/MobileSidebar.vue'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const stats = ref({})
const recentActivity = ref([])

const userInitial = computed(() => authStore.user?.name?.[0]?.toUpperCase() || '?')
const firstName = computed(() => authStore.user?.name?.split(' ')[0] || 'there')

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const res = await api.get('/dashboard/stats')
    stats.value = res.data.stats
    recentActivity.value = res.data.recent_activity
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(async () => {
  await authStore.fetchUser()
  await fetchDashboardData()
})
</script>

<style scoped>
.dashboard-shell {
  display: flex;
  height: 100vh;
  background: var(--bg-base);
  overflow: hidden;
}

/* Sidebar (same as AdminLayout) */
.sidebar {
  width: 240px;
  flex-shrink: 0;
  background: var(--bg-surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem 1.25rem 1rem;
  border-bottom: 1px solid var(--border);
}

.brand-icon {
  width: 34px;
  height: 34px;
  background: var(--accent);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.sidebar-brand span { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); }

.sidebar-nav {
  flex: 1;
  padding: 1rem 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-section-label {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  padding: 0.75rem 0.5rem 0.25rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.6rem 0.75rem;
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: background var(--transition), color var(--transition);
}

.nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
.nav-item.active { background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-border); }
.nav-icon { width: 16px; height: 16px; flex-shrink: 0; }

.sidebar-footer {
  padding: 0.75rem;
  border-top: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-card { flex: 1; display: flex; align-items: center; gap: 0.6rem; min-width: 0; }

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

.user-avatar.sm { width: 28px; height: 28px; font-size: 0.75rem; }

.user-meta { min-width: 0; display: flex; flex-direction: column; gap: 2px; }

.user-name {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role-tag {
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 1px 6px;
  border-radius: 20px;
  display: inline-block;
  width: fit-content;
}

.user-role-tag.admin { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
.user-role-tag.user  { background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-border); }

.logout-btn {
  width: 32px;
  height: 32px;
  border-radius: var(--radius-sm);
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition);
  flex-shrink: 0;
}

.logout-btn:hover { background: var(--danger-bg); border-color: var(--danger); color: var(--danger); }

/* Main area */
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

.topbar {
  height: 56px;
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  flex-shrink: 0;
}

.page-title { font-size: 1rem; font-weight: 600; color: var(--text-primary); }

.topbar-right { display: flex; align-items: center; gap: 1rem; }
.topbar-user  { display: flex; align-items: center; gap: 0.5rem; }
.topbar-name  { font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }

.topbar-logout {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.85rem;
  background: transparent;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
}

.topbar-logout:hover { background: var(--danger-bg); border-color: var(--danger); color: var(--danger); }

.page-content { flex: 1; overflow-y: auto; padding: 1.5rem; }

.loading-center { display: flex; justify-content: center; align-items: center; min-height: 300px; }

/* Welcome banner */
.welcome-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
  background-image: linear-gradient(135deg, rgba(79,142,247,0.06) 0%, transparent 60%);
}

.welcome-text h2 { font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
.welcome-text p  { font-size: 0.875rem; color: var(--text-muted); }

.welcome-badge {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 0.35rem 0.85rem;
  border-radius: 20px;
}

.welcome-badge.admin { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
.welcome-badge.user  { background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-border); }

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

/* Section card */
.section-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--border);
}

.section-header h3 { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

.view-all { font-size: 0.8rem; color: var(--accent); font-weight: 500; }
.view-all:hover { color: var(--accent-hover); }

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--text-muted);
}

.empty-icon { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5; }
.empty-state p { font-size: 0.875rem; }

/* Activity list */
.activity-list { padding: 0.5rem 0; }

.activity-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid var(--border);
  transition: background var(--transition);
}

.activity-row:last-child { border-bottom: none; }
.activity-row:hover { background: var(--bg-hover); }

.activity-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.activity-status-dot.pending  { background: var(--warning); }
.activity-status-dot.approved { background: var(--success); }
.activity-status-dot.rejected { background: var(--danger); }

.activity-info { flex: 1; min-width: 0; }

.activity-title {
  font-size: 0.875rem;
  color: var(--text-primary);
  font-weight: 500;
  margin-bottom: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.activity-meta { display: flex; align-items: center; gap: 0.75rem; }

.status-chip {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 1px 7px;
  border-radius: 20px;
}

.status-chip.pending  { background: var(--warning-bg); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); }
.status-chip.approved { background: var(--success-bg); color: var(--success); border: 1px solid rgba(34,197,94,0.3); }
.status-chip.rejected { background: var(--danger-bg);  color: var(--danger);  border: 1px solid rgba(239,68,68,0.3); }

.activity-time { font-size: 0.75rem; color: var(--text-muted); }

.activity-amount { font-size: 0.9rem; font-weight: 700; color: var(--accent); flex-shrink: 0; }

@media (max-width: 768px) {
  .dashboard-shell { flex-direction: column; }
  .main-area { width: 100%; }
  .page-content { padding: 1rem; }
  .welcome-banner { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
  .stats-grid { grid-template-columns: 1fr; }
}
</style>
