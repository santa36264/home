<template>
  <AdminLayout title="System Settings">
    <MobileSidebar title="Settings" />

    <div class="settings-page">
      <div class="page-header">
        <div>
          <h2 class="page-title">System Configuration</h2>
          <p class="page-subtitle">Customize your system settings and preferences</p>
        </div>
        <button @click="resetSettings" class="btn-reset" :disabled="loading">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
          Reset to Default
        </button>
      </div>

      <div v-if="loading && !settings" class="loading-state">
        <LoadingSpinner size="large" text="Loading settings..." />
      </div>

      <div v-else class="settings-container">
        <form @submit.prevent="saveSettings">
          <div class="settings-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
              </div>
              <h3>General Settings</h3>
            </div>
            <div class="form-group">
              <label for="system_name">System Name</label>
              <input id="system_name" v-model="settings.system_name" type="text" placeholder="Request Workflow System" maxlength="255" />
              <span class="help-text">This name appears in the header and login page.</span>
            </div>
            <div class="form-group">
              <label for="registration_mode">Registration Mode</label>
              <select id="registration_mode" v-model="settings.registration_mode">
                <option value="public">Public — Anyone can register</option>
                <option value="admin_only">Admin Only — Only admins can create users</option>
              </select>
              <span class="help-text">Control who can create new accounts.</span>
            </div>
          </div>

          <div class="settings-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              </div>
              <h3>Display Settings</h3>
            </div>
            <div class="form-group">
              <label for="items_per_page">Items Per Page</label>
              <select id="items_per_page" v-model="settings.items_per_page">
                <option value="10">10</option><option value="15">15</option>
                <option value="25">25</option><option value="50">50</option><option value="100">100</option>
              </select>
            </div>
            <div class="form-group">
              <label for="date_format">Date Format</label>
              <select id="date_format" v-model="settings.date_format">
                <option value="Y-m-d">YYYY-MM-DD</option>
                <option value="m/d/Y">MM/DD/YYYY</option>
                <option value="d/m/Y">DD/MM/YYYY</option>
                <option value="F j, Y">Month Day, Year</option>
              </select>
            </div>
            <div class="form-group">
              <label for="timezone">Timezone</label>
              <select id="timezone" v-model="settings.timezone">
                <option value="UTC">UTC</option>
                <option value="America/New_York">Eastern Time (US)</option>
                <option value="America/Chicago">Central Time (US)</option>
                <option value="America/Los_Angeles">Pacific Time (US)</option>
                <option value="Europe/London">London</option>
                <option value="Europe/Paris">Paris</option>
                <option value="Asia/Tokyo">Tokyo</option>
                <option value="Asia/Dubai">Dubai</option>
              </select>
            </div>
          </div>

          <div class="settings-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
              </div>
              <h3>Notification Settings</h3>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="enableNotifications" class="checkbox-input" />
                <div class="checkbox-text">
                  <strong>Enable Notifications</strong>
                  <span class="help-text">Show toast notifications for actions and updates</span>
                </div>
              </label>
            </div>
          </div>

          <div class="settings-section section-highlight">
            <div class="section-header">
              <div class="section-icon section-icon-accent">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <h3>Quick Presets</h3>
            </div>
            <div class="quick-settings">
              <div class="info-banner">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Quickly adapt this system to your problem by changing the system name and registration mode.</span>
              </div>
              <div class="presets-grid">
                <button type="button" @click="applyPreset('hospital')" class="preset-btn">🏥 Hospital System</button>
                <button type="button" @click="applyPreset('school')" class="preset-btn">🎓 School Portal</button>
                <button type="button" @click="applyPreset('work')" class="preset-btn">💼 Work Management</button>
                <button type="button" @click="applyPreset('inventory')" class="preset-btn">📦 Inventory System</button>
              </div>
            </div>
          </div>

          <div v-if="error" class="form-error">{{ error }}</div>
          <div v-if="successMessage" class="form-success">{{ successMessage }}</div>

          <div class="form-actions">
            <button type="button" @click="loadSettings" class="btn-secondary" :disabled="saving">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="saving">
              <svg v-if="!saving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
              <LoadingSpinner v-else size="small" />
              Save Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminLayout from '../../components/AdminLayout.vue'
import LoadingSpinner from '../../components/LoadingSpinner.vue'
import MobileSidebar from '../../components/MobileSidebar.vue'
import { useToast } from '../../composables/useToast'
import api from '../../services/api'

const { success, error: showError } = useToast()

const settings = ref({ system_name: '', registration_mode: 'public', items_per_page: '15', date_format: 'Y-m-d', timezone: 'UTC', enable_notifications: 'true' })
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const successMessage = ref('')

const enableNotifications = computed({
  get: () => settings.value.enable_notifications === 'true',
  set: (val) => settings.value.enable_notifications = val ? 'true' : 'false'
})

const loadSettings = async () => {
  loading.value = true; error.value = ''
  try { const r = await api.get('/admin/settings'); settings.value = r.data }
  catch (err) { error.value = 'Failed to load settings'; showError('Load Failed', error.value) }
  finally { loading.value = false }
}

const saveSettings = async () => {
  saving.value = true; error.value = ''; successMessage.value = ''
  try {
    const r = await api.put('/admin/settings', settings.value)
    settings.value = r.data.settings
    successMessage.value = 'Settings saved successfully!'
    success('Settings Saved', 'Your changes have been saved successfully')
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) { error.value = err.response?.data?.message || 'Failed to save settings'; showError('Save Failed', error.value) }
  finally { saving.value = false }
}

const resetSettings = async () => {
  if (!confirm('Reset all settings to default?')) return
  loading.value = true; error.value = ''; successMessage.value = ''
  try {
    const r = await api.post('/admin/settings/reset')
    settings.value = r.data.settings
    successMessage.value = 'Settings reset to default!'
    success('Settings Reset', 'All settings have been reset to default values')
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) { error.value = err.response?.data?.message || 'Failed to reset settings'; showError('Reset Failed', error.value) }
  finally { loading.value = false }
}

const applyPreset = (preset) => {
  const presets = {
    hospital: { system_name: 'Hospital Management System', registration_mode: 'admin_only' },
    school:   { system_name: 'School Portal', registration_mode: 'admin_only' },
    work:     { system_name: 'Work Management System', registration_mode: 'public' },
    inventory:{ system_name: 'Inventory Management', registration_mode: 'admin_only' },
  }
  Object.assign(settings.value, presets[preset])
  success('Preset Applied', `${settings.value.system_name} preset applied. Don't forget to save!`)
}

onMounted(() => { loadSettings() })
</script>

<style scoped>
.settings-page { padding: 2rem; max-width: 1100px; margin: 0 auto; }

.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap; }
.page-title { margin: 0 0 0.25rem; font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
.page-subtitle { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }

.btn-reset {
  display: inline-flex; align-items: center; gap: 0.5rem;
  padding: 0.625rem 1.25rem; background: var(--bg-elevated); color: var(--warning);
  border: 1px solid rgba(245,158,11,0.35); border-radius: var(--radius-sm);
  cursor: pointer; font-size: 0.875rem; font-weight: 500; white-space: nowrap;
  transition: all var(--transition);
}
.btn-reset:hover:not(:disabled) { background: var(--warning); color: #fff; border-color: var(--warning); }
.btn-reset:disabled { opacity: 0.5; cursor: not-allowed; }

.loading-state { text-align: center; padding: 4rem 2rem; }

.settings-container { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }

.settings-section { padding: 1.75rem 2rem; border-bottom: 1px solid var(--border); }
.settings-section:last-of-type { border-bottom: none; }
.section-highlight { background: var(--bg-elevated); }

.section-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
.section-icon {
  display: flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; background: var(--bg-elevated); border: 1px solid var(--border);
  border-radius: var(--radius-sm); color: var(--text-secondary); flex-shrink: 0;
}
.section-icon-accent { background: var(--accent-glow); border-color: var(--accent-border); color: var(--accent); }
.section-header h3 { margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary); }

.form-group { margin-bottom: 1.25rem; }
.form-group:last-child { margin-bottom: 0; }
.form-group label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); }
.form-group input[type="text"], .form-group select {
  width: 100%; padding: 0.625rem 0.875rem; background: var(--bg-elevated);
  border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary);
  font-size: 0.9rem; font-family: inherit; box-sizing: border-box;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.form-group input[type="text"]:focus, .form-group select:focus {
  outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow);
}
.form-group select option { background: var(--bg-elevated); color: var(--text-primary); }
.help-text { display: block; margin-top: 0.4rem; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }

.checkbox-label {
  display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;
  padding: 1rem; background: var(--bg-elevated); border: 1px solid var(--border);
  border-radius: var(--radius-sm); transition: border-color var(--transition), background var(--transition);
}
.checkbox-label:hover { background: var(--bg-hover); border-color: var(--border-light); }
.checkbox-input { margin-top: 2px; width: 16px; height: 16px; cursor: pointer; accent-color: var(--accent); flex-shrink: 0; }
.checkbox-text { display: flex; flex-direction: column; gap: 0.2rem; }
.checkbox-text strong { color: var(--text-primary); font-size: 0.9rem; }
.checkbox-text .help-text { margin-top: 0; }

.quick-settings { display: flex; flex-direction: column; gap: 1rem; }
.info-banner {
  display: flex; align-items: flex-start; gap: 0.5rem; padding: 0.75rem 1rem;
  background: var(--bg-surface); border: 1px solid var(--accent-border); border-left: 3px solid var(--accent);
  border-radius: var(--radius-sm); color: var(--text-secondary); font-size: 0.875rem; line-height: 1.5;
}
.info-banner svg { flex-shrink: 0; color: var(--accent); margin-top: 1px; }

.presets-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.75rem; }
.preset-btn {
  padding: 0.875rem 1rem; background: var(--bg-surface); border: 1px solid var(--border);
  border-radius: var(--radius-sm); color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;
  cursor: pointer; transition: all var(--transition); text-align: left;
}
.preset-btn:hover { background: var(--accent); border-color: var(--accent); color: #fff; transform: translateY(-2px); box-shadow: var(--shadow-md); }

.form-error { margin: 0 2rem 1rem; padding: 0.75rem 1rem; background: var(--danger-bg); border: 1px solid rgba(239,68,68,0.3); border-left: 3px solid var(--danger); border-radius: var(--radius-sm); color: var(--danger); font-size: 0.875rem; }
.form-success { margin: 0 2rem 1rem; padding: 0.75rem 1rem; background: var(--success-bg); border: 1px solid rgba(34,197,94,0.3); border-left: 3px solid var(--success); border-radius: var(--radius-sm); color: var(--success); font-size: 0.875rem; }

.form-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1.25rem 2rem; background: var(--bg-elevated); border-top: 1px solid var(--border); }

.btn-primary {
  display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem;
  background: var(--accent); color: #fff; border: none; border-radius: var(--radius-sm);
  cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: background var(--transition), box-shadow var(--transition), transform var(--transition);
}
.btn-primary:hover:not(:disabled) { background: var(--accent-hover); box-shadow: 0 0 0 3px var(--accent-glow); transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.btn-secondary {
  padding: 0.625rem 1.25rem; background: var(--bg-surface); color: var(--text-secondary);
  border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer;
  font-size: 0.9rem; font-weight: 500; transition: background var(--transition);
}
.btn-secondary:hover:not(:disabled) { background: var(--bg-hover); color: var(--text-primary); }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

@media (max-width: 768px) {
  .settings-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: stretch; }
  .btn-reset { width: 100%; justify-content: center; }
  .settings-section { padding: 1.25rem; }
  .presets-grid { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; padding: 1.25rem; }
  .btn-secondary, .btn-primary { width: 100%; justify-content: center; }
  .form-error, .form-success { margin: 0 0 1rem; }
}
</style>
