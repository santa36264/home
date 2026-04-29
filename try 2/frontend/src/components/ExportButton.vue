<template>
  <div class="export-wrap">
    <button @click="toggleDropdown" class="export-btn" :disabled="loading">
      <svg v-if="!loading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
      </svg>
      <LoadingSpinner v-else size="small" />
      <span>Export</span>
      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline :points="showDropdown ? '18 15 12 9 6 15' : '6 9 12 15 18 9'"/>
      </svg>
    </button>

    <div v-if="showDropdown" class="dropdown">
      <button @click="exportData('csv')" class="dropdown-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
        <div>
          <div class="item-title">Export CSV</div>
          <div class="item-sub">Download as spreadsheet</div>
        </div>
      </button>
      <button @click="exportData('summary')" class="dropdown-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        <div>
          <div class="item-title">Summary Report</div>
          <div class="item-sub">Statistics overview</div>
        </div>
      </button>
    </div>

    <div v-if="showDropdown" class="dropdown-overlay" @click="closeDropdown"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import LoadingSpinner from './LoadingSpinner.vue'
import { useToast } from '../composables/useToast'
import api from '../services/api'

const props = defineProps({
  type: { type: String, default: 'requests', validator: v => ['requests','users'].includes(v) }
})

const { success, error: showError } = useToast()
const showDropdown = ref(false)
const loading = ref(false)

const toggleDropdown = () => { showDropdown.value = !showDropdown.value }
const closeDropdown  = () => { showDropdown.value = false }

const exportData = async (format) => {
  loading.value = true
  closeDropdown()
  try {
    const endpoint = props.type === 'users'
      ? '/admin/export/users'
      : format === 'summary' ? '/export/summary' : '/export/requests'

    const response = await api.get(endpoint, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    const cd = response.headers['content-disposition']
    let filename = `export_${Date.now()}.csv`
    if (cd) { const m = cd.match(/filename="?(.+)"?/); if (m) filename = m[1] }
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    success('Export Ready', `Saved as ${filename}`)
  } catch {
    showError('Export Failed', 'Could not export data. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.export-wrap { position: relative; }

.export-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.55rem 0.9rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
}

.export-btn:hover:not(:disabled) {
  background: var(--bg-hover);
  border-color: var(--border-light);
  color: var(--text-primary);
}

.export-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.dropdown {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  min-width: 200px;
  z-index: 1001;
  overflow: hidden;
  animation: dropIn 0.15s ease;
}

@keyframes dropIn {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  border-bottom: 1px solid var(--border);
  color: var(--text-secondary);
  cursor: pointer;
  text-align: left;
  transition: background var(--transition), color var(--transition);
}

.dropdown-item:last-child { border-bottom: none; }
.dropdown-item:hover { background: var(--bg-hover); color: var(--text-primary); }

.item-title { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
.item-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }

.dropdown-overlay { position: fixed; inset: 0; z-index: 1000; }
</style>
