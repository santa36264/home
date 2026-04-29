<template>
  <AdminLayout title="User Management">
    <MobileSidebar title="User Management" />

    <div class="users-page">
      <div class="page-header">
        <div class="header-left">
          <SearchInput v-model="searchQuery" placeholder="Search users..." />
          <FilterDropdown v-model="filterRole" label="Role" placeholder="All Roles" :options="roleOptions" />
          <ExportButton type="users" />
        </div>
        <button @click="showCreateModal = true" class="btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Create User
        </button>
      </div>

      <div v-if="loading" class="loading-state"><LoadingSpinner size="large" text="Loading users..." /></div>

      <div v-else-if="users.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>No Users Found</h3>
        <p>{{ searchQuery || filterRole ? 'Try adjusting your filters' : 'No users in the system' }}</p>
      </div>

      <div v-else class="table-container">
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Role</th>
                <th>Created By</th><th>Created At</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td class="col-id">#{{ user.id }}</td>
                <td class="col-name">{{ user.name }}</td>
                <td class="col-email">{{ user.email }}</td>
                <td><span class="role-chip" :class="`role-${user.role}`">{{ user.role }}</span></td>
                <td>
                  <span v-if="user.created_by_admin" class="origin-chip origin-admin">Admin</span>
                  <span v-else class="origin-chip origin-self">Self</span>
                </td>
                <td class="col-date">{{ formatDate(user.created_at) }}</td>
                <td>
                  <div class="action-btns">
                    <button @click="editUser(user)" class="icon-btn icon-btn-warning" title="Edit">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button @click="confirmDelete(user)" class="icon-btn icon-btn-danger" title="Delete" :disabled="user.id === currentUserId">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <Pagination :current-page="currentPage" :total-pages="totalPages" :per-page="perPage" :total="totalItems" @change="currentPage = $event" @per-page-change="handlePerPageChange" />
      </div>

      <!-- Create Modal -->
      <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Create New User</h2>
            <button @click="closeCreateModal" class="close-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
          </div>
          <form @submit.prevent="createUser">
            <div class="modal-body">
              <div class="form-group"><label>Name</label><input v-model="newUser.name" type="text" required placeholder="Enter name" /></div>
              <div class="form-group"><label>Email</label><input v-model="newUser.email" type="email" required placeholder="Enter email" /></div>
              <div class="form-group"><label>Password</label><input v-model="newUser.password" type="password" required minlength="8" placeholder="Minimum 8 characters" /></div>
              <div class="form-group">
                <label>Role</label>
                <select v-model="newUser.role" required>
                  <option value="user">User</option><option value="admin">Admin</option>
                </select>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeCreateModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="creating" class="btn-primary">{{ creating ? 'Creating...' : 'Create User' }}</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Modal -->
      <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Edit User</h2>
            <button @click="closeEditModal" class="close-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
          </div>
          <form @submit.prevent="updateUser">
            <div class="modal-body">
              <div class="form-group"><label>Name</label><input v-model="editingUser.name" type="text" required placeholder="Enter name" /></div>
              <div class="form-group"><label>Email</label><input v-model="editingUser.email" type="email" required placeholder="Enter email" /></div>
              <div class="form-group"><label>Password <span class="label-hint">(leave blank to keep current)</span></label><input v-model="editingUser.password" type="password" minlength="8" placeholder="Enter new password" /></div>
              <div class="form-group">
                <label>Role</label>
                <select v-model="editingUser.role" required>
                  <option value="user">User</option><option value="admin">Admin</option>
                </select>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeEditModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="updating" class="btn-primary">{{ updating ? 'Updating...' : 'Update User' }}</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Modal -->
      <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
        <div class="modal modal-sm" @click.stop>
          <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button @click="closeDeleteModal" class="close-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
          </div>
          <div class="modal-body">
            <p class="delete-msg">Delete user <strong>{{ userToDelete?.name }}</strong>?</p>
            <p class="delete-warn">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button @click="closeDeleteModal" class="btn-secondary">Cancel</button>
            <button @click="deleteUser" :disabled="deleting" class="btn-danger">{{ deleting ? 'Deleting...' : 'Delete User' }}</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useAuthStore } from '../../stores/auth'
import AdminLayout from '../../components/AdminLayout.vue'
import LoadingSpinner from '../../components/LoadingSpinner.vue'
import MobileSidebar from '../../components/MobileSidebar.vue'
import SearchInput from '../../components/SearchInput.vue'
import FilterDropdown from '../../components/FilterDropdown.vue'
import Pagination from '../../components/Pagination.vue'
import ExportButton from '../../components/ExportButton.vue'
import { useToast } from '../../composables/useToast'
import api from '../../services/api'

const { success, error: showError } = useToast()
const authStore = useAuthStore()

const users = ref([])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const filterRole = ref('')
const currentPage = ref(1)
const perPage = ref(15)
const totalItems = ref(0)
const totalPages = ref(1)

const roleOptions = [{ value: 'admin', label: 'Admin' }, { value: 'user', label: 'User' }]

const handlePerPageChange = (n) => { perPage.value = n; currentPage.value = 1 }

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const creating = ref(false)
const updating = ref(false)
const deleting = ref(false)
const newUser = ref({ name: '', email: '', password: '', role: 'user' })
const editingUser = ref({ id: null, name: '', email: '', password: '', role: 'user' })
const userToDelete = ref(null)
const currentUserId = computed(() => authStore.user?.id)

const fetchUsers = async () => {
  loading.value = true
  try {
    const r = await api.get('/admin/users', {
      params: {
        page: currentPage.value,
        per_page: perPage.value,
        search: searchQuery.value || undefined,
        role: filterRole.value || undefined,
      }
    })
    const res = r.data
    users.value = res.data ?? res
    totalItems.value = res.total ?? res.length ?? 0
    totalPages.value = res.last_page ?? 1
  } catch (err) { error.value = 'Failed to load users'; console.error(err) }
  finally { loading.value = false }
}

// Watchers — declared AFTER fetchUsers to avoid temporal dead zone
let searchTimer = null
watch(searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { currentPage.value = 1; fetchUsers() }, 350)
})
watch(filterRole, () => { currentPage.value = 1; fetchUsers() })
watch(currentPage, fetchUsers)

const createUser = async () => {
  error.value = ''; creating.value = true
  try { await api.post('/admin/users', newUser.value); success('User Created', 'New user has been created successfully'); closeCreateModal(); await fetchUsers() }
  catch (err) { error.value = err.response?.data?.message || 'Failed to create user'; showError('Creation Failed', error.value) }
  finally { creating.value = false }
}

const editUser = (user) => { editingUser.value = { id: user.id, name: user.name, email: user.email, password: '', role: user.role }; showEditModal.value = true }

const updateUser = async () => {
  error.value = ''; updating.value = true
  try {
    const p = { name: editingUser.value.name, email: editingUser.value.email, role: editingUser.value.role }
    if (editingUser.value.password) p.password = editingUser.value.password
    await api.put(`/admin/users/${editingUser.value.id}`, p)
    success('User Updated', 'User information has been updated successfully'); closeEditModal(); await fetchUsers()
  } catch (err) { error.value = err.response?.data?.message || 'Failed to update user'; showError('Update Failed', error.value) }
  finally { updating.value = false }
}

const confirmDelete = (user) => { userToDelete.value = user; showDeleteModal.value = true }

const deleteUser = async () => {
  deleting.value = true
  try { await api.delete(`/admin/users/${userToDelete.value.id}`); success('User Deleted', 'User has been deleted successfully'); closeDeleteModal(); await fetchUsers() }
  catch (err) { error.value = err.response?.data?.message || 'Failed to delete user'; showError('Delete Failed', error.value) }
  finally { deleting.value = false }
}

const closeCreateModal = () => { showCreateModal.value = false; newUser.value = { name: '', email: '', password: '', role: 'user' }; error.value = '' }
const closeEditModal = () => { showEditModal.value = false; editingUser.value = { id: null, name: '', email: '', password: '', role: 'user' }; error.value = '' }
const closeDeleteModal = () => { showDeleteModal.value = false; userToDelete.value = null }
const formatDate = (d) => { const dt = new Date(d); return dt.toLocaleDateString() + ' ' + dt.toLocaleTimeString() }

onMounted(() => { fetchUsers() })
</script>

<style scoped>
.users-page { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap; }
.header-left { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; flex: 1; }

.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: var(--accent); color: #fff; border: none; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.9rem; font-weight: 500; white-space: nowrap; transition: background var(--transition), box-shadow var(--transition), transform var(--transition); }
.btn-primary:hover:not(:disabled) { background: var(--accent-hover); box-shadow: 0 0 0 3px var(--accent-glow); transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary { padding: 0.625rem 1.25rem; background: var(--bg-elevated); color: var(--text-secondary); border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: background var(--transition); }
.btn-secondary:hover { background: var(--bg-hover); color: var(--text-primary); }

.btn-danger { padding: 0.625rem 1.25rem; background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-sm); cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: background var(--transition); }
.btn-danger:hover:not(:disabled) { background: var(--danger); color: #fff; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

.loading-state { text-align: center; padding: 4rem 2rem; }
.empty-state { text-align: center; padding: 4rem 2rem; background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-md); }
.empty-icon { display: flex; justify-content: center; margin-bottom: 1rem; color: var(--text-muted); }
.empty-state h3 { margin: 0 0 0.5rem; color: var(--text-primary); }
.empty-state p { margin: 0; color: var(--text-secondary); }

.table-container { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }
.table-scroll { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; min-width: 800px; }
.data-table th { padding: 0.875rem 1rem; text-align: left; background: var(--bg-elevated); color: var(--text-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); white-space: nowrap; }
.data-table td { padding: 0.875rem 1rem; border-bottom: 1px solid var(--border); color: var(--text-primary); font-size: 0.875rem; vertical-align: middle; }
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: var(--bg-hover); }

.col-id { color: var(--text-muted); font-size: 0.8rem; }
.col-name { font-weight: 600; }
.col-email { color: var(--text-secondary); }
.col-date { color: var(--text-secondary); font-size: 0.8rem; white-space: nowrap; }

.role-chip { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.role-admin { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
.role-user { background: var(--info-bg); color: var(--info); border: 1px solid rgba(56,189,248,0.3); }

.origin-chip { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.origin-admin { background: rgba(168,85,247,0.12); color: #a855f7; border: 1px solid rgba(168,85,247,0.3); }
.origin-self { background: var(--bg-elevated); color: var(--text-muted); border: 1px solid var(--border); }

.action-btns { display: flex; gap: 0.35rem; }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: none; border-radius: var(--radius-sm); cursor: pointer; transition: background var(--transition), transform var(--transition); }
.icon-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.icon-btn-warning { background: var(--warning-bg); color: var(--warning); }
.icon-btn-warning:hover:not(:disabled) { background: var(--warning); color: #fff; transform: scale(1.1); }
.icon-btn-danger { background: var(--danger-bg); color: var(--danger); }
.icon-btn-danger:hover:not(:disabled) { background: var(--danger); color: #fff; transform: scale(1.1); }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 1000; animation: fadeIn 0.15s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-lg); animation: slideUp 0.2s ease; }
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-sm { max-width: 420px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
.modal-header h2 { margin: 0; font-size: 1.1rem; color: var(--text-primary); }
.close-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: none; border: none; border-radius: var(--radius-sm); color: var(--text-muted); cursor: pointer; transition: background var(--transition), color var(--transition); }
.close-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
.modal-body { padding: 1.5rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: var(--bg-elevated); border-radius: 0 0 var(--radius-lg) var(--radius-lg); }

.form-group { margin-bottom: 1.25rem; }
.form-group:last-child { margin-bottom: 0; }
.form-group label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); }
.label-hint { font-weight: 400; color: var(--text-muted); font-size: 0.8rem; }
.form-group input, .form-group select { width: 100%; padding: 0.625rem 0.875rem; background: var(--bg-elevated); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 0.9rem; font-family: inherit; box-sizing: border-box; transition: border-color var(--transition), box-shadow var(--transition); }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-group select option { background: var(--bg-elevated); color: var(--text-primary); }
.form-error { margin-top: 0.75rem; padding: 0.75rem 1rem; background: var(--danger-bg); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-sm); color: var(--danger); font-size: 0.875rem; }

.delete-msg { margin: 0 0 0.5rem; color: var(--text-primary); }
.delete-msg strong { color: var(--accent); }
.delete-warn { margin: 0; color: var(--warning); font-size: 0.875rem; font-weight: 500; }

@media (max-width: 768px) {
  .users-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: stretch; }
  .header-left { flex-direction: column; }
  .btn-primary { width: 100%; justify-content: center; }
}
</style>
