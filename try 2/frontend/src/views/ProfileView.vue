<template>
  <AdminLayout title="My Profile">
    <MobileSidebar title="Profile" />

    <div class="profile-page">
      <!-- Header -->
      <div class="page-header">
        <h2 class="page-title">Profile Settings</h2>
        <p class="page-subtitle">Manage your account information and security</p>
      </div>

      <div v-if="loading" class="loading-state">
        <LoadingSpinner size="large" text="Loading profile..." />
      </div>

      <div v-else class="profile-container">
        <!-- Profile Information -->
        <div class="profile-section">
          <div class="section-header">
            <div class="section-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <h3>Profile Information</h3>
          </div>
          <form @submit.prevent="updateProfile">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input id="name" v-model="profileForm.name" type="text" required placeholder="Enter your full name" maxlength="255" />
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input id="email" v-model="profileForm.email" type="email" required placeholder="Enter your email" maxlength="255" />
            </div>
            <div class="form-group">
              <label>Role</label>
              <div class="role-display">
                <span class="role-chip" :class="`role-${user?.role}`">
                  {{ user?.role === 'admin' ? 'Administrator' : 'User' }}
                </span>
              </div>
              <span class="help-text">Your role cannot be changed from this page</span>
            </div>
            <div v-if="profileError" class="form-error">{{ profileError }}</div>
            <div v-if="profileSuccess" class="form-success">{{ profileSuccess }}</div>
            <div class="form-actions">
              <button type="button" @click="resetProfileForm" class="btn-secondary" :disabled="updatingProfile">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="updatingProfile">
                <svg v-if="!updatingProfile" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <LoadingSpinner v-else size="small" />
                Save Changes
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="profile-section">
          <div class="section-header">
            <div class="section-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h3>Change Password</h3>
          </div>
          <form @submit.prevent="changePassword">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input id="current_password" v-model="passwordForm.current_password" type="password" required placeholder="Enter current password" />
            </div>
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input id="new_password" v-model="passwordForm.new_password" type="password" required minlength="8" placeholder="Enter new password (min 8 characters)" />
            </div>
            <div class="form-group">
              <label for="new_password_confirmation">Confirm New Password</label>
              <input id="new_password_confirmation" v-model="passwordForm.new_password_confirmation" type="password" required minlength="8" placeholder="Confirm new password" />
            </div>
            <div v-if="passwordError" class="form-error">{{ passwordError }}</div>
            <div v-if="passwordSuccess" class="form-success">{{ passwordSuccess }}</div>
            <div class="form-actions">
              <button type="button" @click="resetPasswordForm" class="btn-secondary" :disabled="changingPassword">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="changingPassword">
                <svg v-if="!changingPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                <LoadingSpinner v-else size="small" />
                Change Password
              </button>
            </div>
          </form>
        </div>

        <!-- Account Information -->
        <div class="profile-section section-info">
          <div class="section-header">
            <div class="section-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3>Account Information</h3>
          </div>
          <div class="info-grid">
            <div class="info-item">
              <label>Account Created</label>
              <span>{{ formatDate(user?.created_at) }}</span>
            </div>
            <div class="info-item">
              <label>Last Updated</label>
              <span>{{ formatDate(user?.updated_at) }}</span>
            </div>
            <div class="info-item">
              <label>Account Type</label>
              <span>{{ user?.created_by_admin ? 'Created by Admin' : 'Self-Registered' }}</span>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="profile-section section-danger">
          <div class="section-header">
            <div class="section-icon section-icon-danger">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <h3>Danger Zone</h3>
          </div>
          <div class="danger-content">
            <div class="danger-info">
              <strong>Delete Account</strong>
              <p>Once you delete your account, there is no going back. Please be certain.</p>
            </div>
            <button @click="showDeleteModal = true" class="btn-danger">Delete Account</button>
          </div>
        </div>
      </div>

      <!-- Delete Account Modal -->
      <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Delete Account</h2>
            <button @click="closeDeleteModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <form @submit.prevent="deleteAccount">
            <div class="modal-body">
              <div class="warning-banner">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>This action cannot be undone. All your data will be permanently deleted.</span>
              </div>
              <div class="form-group">
                <label for="delete_password">Enter your password to confirm</label>
                <input id="delete_password" v-model="deleteForm.password" type="password" required placeholder="Enter your password" />
              </div>
              <div v-if="deleteError" class="form-error">{{ deleteError }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeDeleteModal" class="btn-secondary" :disabled="deleting">Cancel</button>
              <button type="submit" class="btn-danger" :disabled="deleting">
                <LoadingSpinner v-if="deleting" size="small" />
                <span v-else>Delete My Account</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import AdminLayout from '../components/AdminLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import MobileSidebar from '../components/MobileSidebar.vue'
import { useToast } from '../composables/useToast'
import api from '../services/api'

const { success, error: showError } = useToast()
const router = useRouter()
const authStore = useAuthStore()

const user = ref(null)
const loading = ref(false)

const profileForm = ref({ name: '', email: '' })
const updatingProfile = ref(false)
const profileError = ref('')
const profileSuccess = ref('')

const passwordForm = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const changingPassword = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const showDeleteModal = ref(false)
const deleteForm = ref({ password: '' })
const deleting = ref(false)
const deleteError = ref('')

const loadProfile = async () => {
  loading.value = true
  try {
    const response = await api.get('/profile')
    user.value = response.data
    profileForm.value = { name: response.data.name, email: response.data.email }
  } catch (err) {
    showError('Load Failed', 'Failed to load profile')
    console.error(err)
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  updatingProfile.value = true
  profileError.value = ''
  profileSuccess.value = ''
  try {
    const response = await api.put('/profile', profileForm.value)
    user.value = response.data.user
    authStore.user = response.data.user
    profileSuccess.value = 'Profile updated successfully!'
    success('Profile Updated', 'Your profile has been updated')
    setTimeout(() => { profileSuccess.value = '' }, 3000)
  } catch (err) {
    profileError.value = err.response?.data?.message || 'Failed to update profile'
    showError('Update Failed', profileError.value)
  } finally {
    updatingProfile.value = false
  }
}

const resetProfileForm = () => {
  profileForm.value = { name: user.value.name, email: user.value.email }
  profileError.value = ''
  profileSuccess.value = ''
}

const changePassword = async () => {
  changingPassword.value = true
  passwordError.value = ''
  passwordSuccess.value = ''
  try {
    await api.post('/profile/change-password', passwordForm.value)
    passwordSuccess.value = 'Password changed successfully!'
    success('Password Changed', 'Your password has been updated')
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
    setTimeout(() => { passwordSuccess.value = '' }, 3000)
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Failed to change password'
    showError('Change Failed', passwordError.value)
  } finally {
    changingPassword.value = false
  }
}

const resetPasswordForm = () => {
  passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
  passwordError.value = ''
  passwordSuccess.value = ''
}

const deleteAccount = async () => {
  deleting.value = true
  deleteError.value = ''
  try {
    await api.delete('/profile', { data: deleteForm.value })
    success('Account Deleted', 'Your account has been deleted')
    await authStore.logout()
    router.push('/login')
  } catch (err) {
    deleteError.value = err.response?.data?.message || 'Failed to delete account'
    showError('Delete Failed', deleteError.value)
  } finally {
    deleting.value = false
  }
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteForm.value = { password: '' }
  deleteError.value = ''
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

onMounted(() => { loadProfile() })
</script>

<style scoped>
.profile-page { padding: 2rem; max-width: 860px; margin: 0 auto; }

.page-header { margin-bottom: 2rem; }
.page-title { margin: 0 0 0.25rem; font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
.page-subtitle { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }

.loading-state { text-align: center; padding: 4rem 2rem; }

.profile-container { display: flex; flex-direction: column; gap: 1.25rem; }

/* Sections */
.profile-section {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.75rem;
}
.section-info {
  background: var(--bg-elevated);
  border-color: var(--border-light);
}
.section-danger {
  border-color: rgba(239,68,68,0.35);
  background: rgba(239,68,68,0.04);
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border);
}
.section-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: var(--accent-glow);
  border: 1px solid var(--accent-border);
  border-radius: var(--radius-sm);
  color: var(--accent);
  flex-shrink: 0;
}
.section-icon-danger { background: var(--danger-bg); border-color: rgba(239,68,68,0.3); color: var(--danger); }
.section-header h3 { margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary); }

/* Forms */
.form-group { margin-bottom: 1.25rem; }
.form-group label {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
}
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
  width: 100%;
  padding: 0.625rem 0.875rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.9rem;
  box-sizing: border-box;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.form-group input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

.role-display { margin-bottom: 0.4rem; }
.role-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.875rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
}
.role-admin { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
.role-user { background: var(--info-bg); color: var(--info); border: 1px solid rgba(56,189,248,0.3); }

.help-text { display: block; font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem; }

.form-error {
  margin: 0.75rem 0;
  padding: 0.75rem 1rem;
  background: var(--danger-bg);
  border: 1px solid rgba(239,68,68,0.3);
  border-left: 3px solid var(--danger);
  border-radius: var(--radius-sm);
  color: var(--danger);
  font-size: 0.875rem;
}
.form-success {
  margin: 0.75rem 0;
  padding: 0.75rem 1rem;
  background: var(--success-bg);
  border: 1px solid rgba(34,197,94,0.3);
  border-left: 3px solid var(--success);
  border-radius: var(--radius-sm);
  color: var(--success);
  font-size: 0.875rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid var(--border);
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition), box-shadow var(--transition), transform var(--transition);
}
.btn-primary:hover:not(:disabled) { background: var(--accent-hover); box-shadow: 0 0 0 3px var(--accent-glow); transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.btn-secondary {
  padding: 0.625rem 1.25rem;
  background: var(--bg-elevated);
  color: var(--text-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition);
}
.btn-secondary:hover:not(:disabled) { background: var(--bg-hover); color: var(--text-primary); }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  white-space: nowrap;
  transition: background var(--transition);
}
.btn-danger:hover:not(:disabled) { background: var(--danger); color: #fff; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

/* Account info */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.25rem;
}
.info-item { display: flex; flex-direction: column; gap: 0.3rem; }
.info-item label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
.info-item span { color: var(--text-primary); font-size: 0.9rem; }

/* Danger zone */
.danger-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
}
.danger-info strong { display: block; color: var(--danger); font-size: 0.95rem; margin-bottom: 0.3rem; }
.danger-info p { margin: 0; color: var(--text-secondary); font-size: 0.875rem; }

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  animation: fadeIn 0.15s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  width: 90%;
  max-width: 460px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-lg);
  animation: slideUp 0.2s ease;
}
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
}
.modal-header h2 { margin: 0; font-size: 1.1rem; color: var(--text-primary); }

.close-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: none;
  border: none;
  border-radius: var(--radius-sm);
  color: var(--text-muted);
  cursor: pointer;
  transition: background var(--transition), color var(--transition);
}
.close-btn:hover { background: var(--bg-hover); color: var(--text-primary); }

.modal-body { padding: 1.5rem; }
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border);
  background: var(--bg-elevated);
  border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

.warning-banner {
  display: flex;
  align-items: flex-start;
  gap: 0.625rem;
  padding: 0.875rem 1rem;
  background: var(--warning-bg);
  border: 1px solid rgba(245,158,11,0.3);
  border-left: 3px solid var(--warning);
  border-radius: var(--radius-sm);
  color: var(--warning);
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1.25rem;
}
.warning-banner svg { flex-shrink: 0; margin-top: 1px; }

@media (max-width: 768px) {
  .profile-page { padding: 1rem; }
  .profile-section { padding: 1.25rem; }
  .danger-content { flex-direction: column; align-items: stretch; }
  .btn-danger { width: 100%; justify-content: center; }
  .form-actions { flex-direction: column; }
  .btn-secondary, .btn-primary { width: 100%; justify-content: center; }
  .modal-footer { flex-direction: column; }
  .modal-footer .btn-secondary, .modal-footer .btn-danger { width: 100%; justify-content: center; }
}
</style>
