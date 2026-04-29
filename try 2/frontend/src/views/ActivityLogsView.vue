<template>
  <AdminLayout title="Activity Logs">
    <MobileSidebar title="Activity Logs" />

    <div class="activity-logs-page">
      <!-- Header -->
      <div class="page-header">
        <div>
          <h2 class="page-title">Activity Logs</h2>
          <p class="page-subtitle">Track all actions and events in the system</p>
        </div>
      </div>

      <!-- Stat Cards -->
      <div v-if="stats" class="stats-grid">
        <StatCard title="Total Activities" :value="stats.total" icon="📊" color="blue" />
        <StatCard title="Today" :value="stats.today" icon="📅" color="green" />
        <StatCard title="This Week" :value="stats.this_week" icon="📈" color="purple" />
        <StatCard title="This Month" :value="stats.this_month" icon="📆" color="orange" />
      </div>

      <!-- Filters -->
      <div class="filters-bar">
        <SearchInput v-model="searchQuery" placeholder="Search activities..." />
        <FilterDropdown v-model="filterAction" label="Action" placeholder="All Actions" :options="actionOptions" />
        <div class="date-group">
          <div class="date-field">
            <label>From</label>
            <input type="date" v-model="dateFrom" />
          </div>
          <div class="date-field">
            <label>To</label>
            <input type="date" v-model="dateTo" />
          </div>
        </div>
        <button @click="clearFilters" class="btn-clear">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Clear
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <LoadingSpinner size="large" text="Loading activity logs..." />
      </div>

      <!-- Empty -->
      <div v-else-if="logs.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <h3>No Activity Logs Found</h3>
        <p>{{ searchQuery || filterAction ? 'Try adjusting your filters' : 'No activities recorded yet' }}</p>
      </div>

      <!-- Logs List -->
      <div v-else class="logs-container">
        <div class="logs-list">
          <div v-for="log in logs" :key="log.id" class="log-item">
            <div class="log-icon-wrap" :class="getActionClass(log.action)">
              <component :is="'span'" v-html="getActionSvg(log.action)" class="log-svg"></component>
            </div>
            <div class="log-content">
              <div class="log-top">
                <span class="log-action">{{ log.action }}</span>
                <span class="log-time">{{ formatTime(log.created_at) }}</span>
              </div>
              <div class="log-description">{{ log.description }}</div>
              <div class="log-meta">
                <span v-if="log.user" class="meta-item">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  {{ log.user.name }}
                </span>
                <span v-if="log.model" class="meta-item">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                  {{ log.model }}
                </span>
                <span v-if="log.ip_address" class="meta-item">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                  {{ log.ip_address }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="pagination-bar">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="page-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Previous
          </button>
          <span class="page-info">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="page-btn">
            Next
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import MobileSidebar from '../components/MobileSidebar.vue'
import StatCard from '../components/StatCard.vue'
import SearchInput from '../components/SearchInput.vue'
import FilterDropdown from '../components/FilterDropdown.vue'
import { useToast } from '../composables/useToast'
import api from '../services/api'

const { error: showError } = useToast()

const logs = ref([])
const stats = ref(null)
const loading = ref(false)
const searchQuery = ref('')
const filterAction = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const pagination = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 })

const actionOptions = [
  { value: 'login', label: 'Login' },
  { value: 'logout', label: 'Logout' },
  { value: 'register', label: 'Register' },
  { value: 'create', label: 'Create' },
  { value: 'update', label: 'Update' },
  { value: 'delete', label: 'Delete' },
  { value: 'approve', label: 'Approve' },
  { value: 'reject', label: 'Reject' },
]

const loadLogs = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      search: searchQuery.value || undefined,
      action: filterAction.value || undefined,
      date_from: dateFrom.value || undefined,
      date_to: dateTo.value || undefined,
    }
    const response = await api.get('/activity-logs', { params })
    logs.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (err) {
    showError('Load Failed', 'Failed to load activity logs')
    console.error(err)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await api.get('/activity-logs/stats')
    stats.value = response.data
  } catch (err) {
    console.error('Failed to load stats:', err)
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) { loadLogs(page) }
}

const clearFilters = () => {
  searchQuery.value = ''
  filterAction.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  loadLogs(1)
}

const getActionSvg = (action) => {
  const svgs = {
    login: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>',
    logout: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    register: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>',
    create: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    update: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    delete: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>',
    approve: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
    reject: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
  }
  return svgs[action] || '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>'
}

const getActionClass = (action) => {
  const classes = {
    login: 'icon-success',
    logout: 'icon-info',
    register: 'icon-success',
    create: 'icon-success',
    update: 'icon-warning',
    delete: 'icon-danger',
    approve: 'icon-success',
    reject: 'icon-danger',
  }
  return classes[action] || 'icon-default'
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  if (seconds < 60) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

watch([filterAction, dateFrom, dateTo], () => { loadLogs(1) })

let searchTimer = null
watch(searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => loadLogs(1), 350)
})

onMounted(() => { loadLogs(); loadStats() })
</script>

<style scoped>
.activity-logs-page { padding: 2rem; max-width: 1400px; margin: 0 auto; }

.page-header { margin-bottom: 1.75rem; }
.page-title { margin: 0 0 0.25rem; font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
.page-subtitle { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.75rem;
}

/* Filters */
.filters-bar {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.75rem;
  flex-wrap: wrap;
  align-items: flex-end;
}
.date-group { display: flex; gap: 0.5rem; }
.date-field { display: flex; flex-direction: column; gap: 0.3rem; }
.date-field label { font-size: 0.78rem; font-weight: 500; color: var(--text-secondary); }
.date-field input[type="date"] {
  padding: 0.5rem 0.75rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.875rem;
  cursor: pointer;
  transition: border-color var(--transition), box-shadow var(--transition);
  color-scheme: dark;
}
.date-field input[type="date"]:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

.btn-clear {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 0.875rem;
  background: var(--bg-elevated);
  color: var(--text-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all var(--transition);
}
.btn-clear:hover { background: var(--bg-hover); color: var(--text-primary); border-color: var(--border-light); }

/* States */
.loading-state { text-align: center; padding: 4rem 2rem; }
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
}
.empty-icon { display: flex; justify-content: center; margin-bottom: 1rem; color: var(--text-muted); }
.empty-state h3 { margin: 0 0 0.5rem; color: var(--text-primary); }
.empty-state p { margin: 0; color: var(--text-secondary); }

/* Logs */
.logs-container {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}
.logs-list { padding: 0.5rem; }

.log-item {
  display: flex;
  gap: 1rem;
  padding: 0.875rem 0.75rem;
  border-radius: var(--radius-sm);
  transition: background var(--transition);
}
.log-item:not(:last-child) { border-bottom: 1px solid var(--border); }
.log-item:hover { background: var(--bg-hover); }

.log-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.log-svg { display: flex; align-items: center; justify-content: center; }

.icon-success { background: var(--success-bg); color: var(--success); }
.icon-warning { background: var(--warning-bg); color: var(--warning); }
.icon-danger { background: var(--danger-bg); color: var(--danger); }
.icon-info { background: var(--info-bg); color: var(--info); }
.icon-default { background: var(--bg-elevated); color: var(--text-muted); }

.log-content { flex: 1; min-width: 0; }
.log-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.3rem;
  gap: 1rem;
}
.log-action {
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--text-primary);
  text-transform: capitalize;
}
.log-time { font-size: 0.78rem; color: var(--text-muted); white-space: nowrap; }
.log-description { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.4rem; line-height: 1.5; }

.log-meta { display: flex; gap: 1rem; flex-wrap: wrap; }
.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.78rem;
  color: var(--text-muted);
}

/* Pagination */
.pagination-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  border-top: 1px solid var(--border);
  background: var(--bg-elevated);
}
.page-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
}
.page-btn:hover:not(:disabled) { background: var(--accent); color: #fff; border-color: var(--accent); }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 0.875rem; color: var(--text-muted); }

@media (max-width: 768px) {
  .activity-logs-page { padding: 1rem; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .filters-bar { flex-direction: column; align-items: stretch; }
  .date-group { flex-direction: column; }
  .log-item { flex-direction: column; }
  .log-top { flex-direction: column; align-items: flex-start; }
  .pagination-bar { flex-direction: column; gap: 0.75rem; }
  .page-btn { width: 100%; justify-content: center; }
}
</style>
