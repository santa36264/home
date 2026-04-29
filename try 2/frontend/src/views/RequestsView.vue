<template>
  <AdminLayout title="Requests">
    <MobileSidebar title="My Requests" />

    <div class="requests-page">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <SearchInput v-model="searchQuery" placeholder="Search requests..." />
          <FilterDropdown
            v-model="filterStatus"
            label="Status"
            placeholder="All Statuses"
            :options="statusOptions"
          />
          <ExportButton type="requests" />
        </div>
        <button @click="showCreateModal = true" class="btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          New Request
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <LoadingSpinner size="large" text="Loading requests..." />
      </div>

      <!-- Empty State -->
      <div v-else-if="requests.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <h3>No Requests Found</h3>
        <p>{{ searchQuery || filterStatus ? 'Try adjusting your filters' : 'Create your first request to get started' }}</p>
      </div>

      <!-- Requests Grid -->
      <div v-else>
        <div class="requests-grid">
          <div v-for="request in requests" :key="request.id" class="request-card">            <div class="card-header">
              <h3 class="card-title">{{ request.title }}</h3>
              <span class="status-chip" :class="`status-${request.status}`">{{ request.status }}</span>
            </div>
            <div class="card-body">
              <p class="description">{{ request.description }}</p>
              <div class="request-details">
                <div class="detail-row">
                  <span class="detail-label">Quantity</span>
                  <span class="detail-value">{{ request.quantity }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Unit Price</span>
                  <span class="detail-value">${{ request.unit_price }}</span>
                </div>
                <div class="detail-row total-row">
                  <span class="detail-label">Total</span>
                  <span class="detail-value total-value">${{ request.total }}</span>
                </div>
              </div>
              <div v-if="request.admin_note" class="admin-note">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ request.admin_note }}</span>
              </div>
            </div>
            <div class="card-footer">
              <span class="date">{{ formatDate(request.created_at) }}</span>
              <div class="card-actions">
                <button
                  v-if="request.status === 'pending'"
                  @click="editRequest(request)"
                  class="icon-btn icon-btn-warning"
                  title="Edit"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button
                  v-if="request.status === 'pending'"
                  @click="confirmDelete(request)"
                  class="icon-btn icon-btn-danger"
                  title="Delete"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <Pagination
          v-if="totalItems > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :per-page="perPage"
          :total="totalItems"
          @change="currentPage = $event"
          @per-page-change="handlePerPageChange"
        />
      </div>

      <!-- Create Request Modal -->
      <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Create New Request</h2>
            <button @click="closeCreateModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <form @submit.prevent="createRequest">
            <div class="modal-body">
              <div class="form-group">
                <label for="title">Title</label>
                <input id="title" v-model="newRequest.title" type="text" required placeholder="Enter request title" />
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" v-model="newRequest.description" required rows="4" placeholder="Describe your request"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="quantity">Quantity *</label>
                  <input id="quantity" v-model.number="newRequest.quantity" type="number" required min="1" step="1" placeholder="Enter quantity" @input="validateQuantity" />
                  <span v-if="quantityError" class="field-error">{{ quantityError }}</span>
                </div>
                <div class="form-group">
                  <label for="unit_price">Unit Price ($) *</label>
                  <input id="unit_price" v-model.number="newRequest.unit_price" type="number" step="0.01" required min="0.01" placeholder="0.00" @input="validateUnitPrice" />
                  <span v-if="unitPriceError" class="field-error">{{ unitPriceError }}</span>
                </div>
              </div>
              <div class="total-display">
                <div class="total-label">Total Amount</div>
                <div class="total-amount">${{ calculateTotal }}</div>
                <div class="total-breakdown">{{ newRequest.quantity || 0 }} × ${{ (newRequest.unit_price || 0).toFixed(2) }}</div>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeCreateModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="creating" class="btn-primary">
                {{ creating ? 'Creating...' : 'Create Request' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Request Modal -->
      <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Edit Request</h2>
            <button @click="closeEditModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <form @submit.prevent="updateRequest">
            <div class="modal-body">
              <div class="form-group">
                <label for="edit-title">Title</label>
                <input id="edit-title" v-model="editingRequest.title" type="text" required placeholder="Enter request title" />
              </div>
              <div class="form-group">
                <label for="edit-description">Description</label>
                <textarea id="edit-description" v-model="editingRequest.description" required rows="4" placeholder="Describe your request"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="edit-quantity">Quantity *</label>
                  <input id="edit-quantity" v-model.number="editingRequest.quantity" type="number" required min="1" step="1" placeholder="Enter quantity" @input="validateEditQuantity" />
                  <span v-if="editQuantityError" class="field-error">{{ editQuantityError }}</span>
                </div>
                <div class="form-group">
                  <label for="edit-unit_price">Unit Price ($) *</label>
                  <input id="edit-unit_price" v-model.number="editingRequest.unit_price" type="number" step="0.01" required min="0.01" placeholder="0.00" @input="validateEditUnitPrice" />
                  <span v-if="editUnitPriceError" class="field-error">{{ editUnitPriceError }}</span>
                </div>
              </div>
              <div class="total-display">
                <div class="total-label">Total Amount</div>
                <div class="total-amount">${{ calculateEditTotal }}</div>
                <div class="total-breakdown">{{ editingRequest.quantity || 0 }} × ${{ (editingRequest.unit_price || 0).toFixed(2) }}</div>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeEditModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="updating" class="btn-primary">
                {{ updating ? 'Updating...' : 'Update Request' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
        <div class="modal modal-sm" @click.stop>
          <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button @click="closeDeleteModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="modal-body">
            <p class="delete-msg">Are you sure you want to delete this request?</p>
            <p class="delete-warn">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button @click="closeDeleteModal" class="btn-secondary">Cancel</button>
            <button @click="deleteRequest" :disabled="deleting" class="btn-danger">
              {{ deleting ? 'Deleting...' : 'Delete Request' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import MobileSidebar from '../components/MobileSidebar.vue'
import SearchInput from '../components/SearchInput.vue'
import FilterDropdown from '../components/FilterDropdown.vue'
import Pagination from '../components/Pagination.vue'
import ExportButton from '../components/ExportButton.vue'
import { useToast } from '../composables/useToast'
import api from '../services/api'

const { success, error: showError } = useToast()

const requests = ref([])
const loading = ref(false)
const error = ref('')

const searchQuery = ref('')
const filterStatus = ref('')
const currentPage = ref(1)
const perPage = ref(15)
const totalItems = ref(0)
const totalPages = ref(1)

const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'approved', label: 'Approved' },
  { value: 'rejected', label: 'Rejected' }
]

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage
  currentPage.value = 1
}

const quantityError = ref('')
const unitPriceError = ref('')
const editQuantityError = ref('')
const editUnitPriceError = ref('')

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

const creating = ref(false)
const updating = ref(false)
const deleting = ref(false)

const newRequest = ref({ title: '', description: '', quantity: 1, unit_price: 0 })
const editingRequest = ref({ id: null, title: '', description: '', quantity: 1, unit_price: 0 })
const requestToDelete = ref(null)

const calculateTotal = computed(() => {
  const quantity = newRequest.value.quantity || 0
  const unitPrice = newRequest.value.unit_price || 0
  return (quantity * unitPrice).toFixed(2)
})

const calculateEditTotal = computed(() => {
  const quantity = editingRequest.value.quantity || 0
  const unitPrice = editingRequest.value.unit_price || 0
  return (quantity * unitPrice).toFixed(2)
})

const validateQuantity = () => {
  quantityError.value = ''
  if (newRequest.value.quantity < 1) { quantityError.value = 'Quantity must be at least 1'; newRequest.value.quantity = 1 }
  if (newRequest.value.quantity && !Number.isInteger(newRequest.value.quantity)) { newRequest.value.quantity = Math.floor(newRequest.value.quantity) }
}

const validateUnitPrice = () => {
  unitPriceError.value = ''
  if (newRequest.value.unit_price < 0) { unitPriceError.value = 'Price cannot be negative'; newRequest.value.unit_price = 0 }
}

const validateEditQuantity = () => {
  editQuantityError.value = ''
  if (editingRequest.value.quantity < 1) { editQuantityError.value = 'Quantity must be at least 1'; editingRequest.value.quantity = 1 }
  if (editingRequest.value.quantity && !Number.isInteger(editingRequest.value.quantity)) { editingRequest.value.quantity = Math.floor(editingRequest.value.quantity) }
}

const validateEditUnitPrice = () => {
  editUnitPriceError.value = ''
  if (editingRequest.value.unit_price < 0) { editUnitPriceError.value = 'Price cannot be negative'; editingRequest.value.unit_price = 0 }
}

const fetchRequests = async () => {
  loading.value = true
  try {
    const response = await api.get('/requests', {
      params: {
        page: currentPage.value,
        per_page: perPage.value,
        search: searchQuery.value || undefined,
        status: filterStatus.value || undefined,
      }
    })
    const res = response.data
    requests.value = res.data ?? res
    totalItems.value = res.total ?? res.length ?? 0
    totalPages.value = res.last_page ?? 1
  } catch (err) {
    error.value = 'Failed to load requests'
    console.error(err)
  } finally {
    loading.value = false
  }
}

// Watchers — declared AFTER fetchRequests to avoid temporal dead zone
let searchTimer = null
watch(searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { currentPage.value = 1; fetchRequests() }, 350)
})
watch(filterStatus, () => { currentPage.value = 1; fetchRequests() })
watch(currentPage, fetchRequests)

const createRequest = async () => {
  error.value = ''
  quantityError.value = ''
  unitPriceError.value = ''
  if (newRequest.value.quantity < 1) { quantityError.value = 'Quantity must be at least 1'; return }
  if (newRequest.value.unit_price <= 0) { unitPriceError.value = 'Unit price must be greater than 0'; return }
  creating.value = true
  try {
    await api.post('/requests', newRequest.value)
    success('Request Created', 'Your request has been submitted successfully')
    closeCreateModal()
    await fetchRequests()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create request'
    showError('Creation Failed', error.value)
  } finally {
    creating.value = false
  }
}

const editRequest = (request) => {
  editingRequest.value = { id: request.id, title: request.title, description: request.description, quantity: request.quantity, unit_price: parseFloat(request.unit_price) }
  showEditModal.value = true
}

const updateRequest = async () => {
  error.value = ''
  editQuantityError.value = ''
  editUnitPriceError.value = ''
  if (editingRequest.value.quantity < 1) { editQuantityError.value = 'Quantity must be at least 1'; return }
  if (editingRequest.value.unit_price <= 0) { editUnitPriceError.value = 'Unit price must be greater than 0'; return }
  updating.value = true
  try {
    await api.put(`/requests/${editingRequest.value.id}`, { title: editingRequest.value.title, description: editingRequest.value.description, quantity: editingRequest.value.quantity, unit_price: editingRequest.value.unit_price })
    success('Request Updated', 'Your request has been updated successfully')
    closeEditModal()
    await fetchRequests()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update request'
    showError('Update Failed', error.value)
  } finally {
    updating.value = false
  }
}

const confirmDelete = (request) => {
  requestToDelete.value = request
  showDeleteModal.value = true
}

const deleteRequest = async () => {
  deleting.value = true
  try {
    await api.delete(`/requests/${requestToDelete.value.id}`)
    success('Request Deleted', 'Your request has been deleted successfully')
    closeDeleteModal()
    await fetchRequests()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete request'
    showError('Delete Failed', error.value)
  } finally {
    deleting.value = false
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
  newRequest.value = { title: '', description: '', quantity: 1, unit_price: 0 }
  error.value = ''
  quantityError.value = ''
  unitPriceError.value = ''
}

const closeEditModal = () => {
  showEditModal.value = false
  editingRequest.value = { id: null, title: '', description: '', quantity: 1, unit_price: 0 }
  error.value = ''
  editQuantityError.value = ''
  editUnitPriceError.value = ''
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  requestToDelete.value = null
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

onMounted(() => { fetchRequests() })
</script>

<style scoped>
.requests-page {
  padding: 2rem;
}

/* Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}
.header-left {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  flex-wrap: wrap;
  flex: 1;
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
  white-space: nowrap;
}
.btn-primary:hover:not(:disabled) {
  background: var(--accent-hover);
  box-shadow: 0 0 0 3px var(--accent-glow);
  transform: translateY(-1px);
}
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  padding: 0.625rem 1.25rem;
  background: var(--bg-elevated);
  color: var(--text-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition), border-color var(--transition);
}
.btn-secondary:hover:not(:disabled) {
  background: var(--bg-hover);
  border-color: var(--border-light);
  color: var(--text-primary);
}
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-danger {
  padding: 0.625rem 1.25rem;
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid var(--danger);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition);
}
.btn-danger:hover:not(:disabled) { background: var(--danger); color: #fff; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

/* Icon buttons */
.icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background var(--transition), transform var(--transition);
}
.icon-btn-warning {
  background: var(--warning-bg);
  color: var(--warning);
}
.icon-btn-warning:hover { background: var(--warning); color: #fff; transform: scale(1.1); }
.icon-btn-danger {
  background: var(--danger-bg);
  color: var(--danger);
}
.icon-btn-danger:hover { background: var(--danger); color: #fff; transform: scale(1.1); }

/* States */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
}
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
}
.empty-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
  color: var(--text-muted);
}
.empty-state h3 { margin: 0 0 0.5rem; color: var(--text-primary); }
.empty-state p { margin: 0; color: var(--text-secondary); }

/* Grid */
.requests-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

/* Card */
.request-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);
}
.request-card:hover {
  border-color: var(--border-light);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.25rem 1.25rem 0;
  gap: 0.75rem;
}
.card-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  flex: 1;
  line-height: 1.4;
}

/* Status chips */
.status-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  white-space: nowrap;
  flex-shrink: 0;
}
.status-pending {
  background: var(--warning-bg);
  color: var(--warning);
  border: 1px solid rgba(245,158,11,0.3);
}
.status-approved {
  background: var(--success-bg);
  color: var(--success);
  border: 1px solid rgba(34,197,94,0.3);
}
.status-rejected {
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid rgba(239,68,68,0.3);
}

.card-body {
  padding: 1rem 1.25rem;
}
.description {
  margin: 0 0 1rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  line-height: 1.6;
}

.request-details {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  padding: 0.75rem;
  background: var(--bg-elevated);
  border-radius: var(--radius-sm);
  margin-bottom: 0.75rem;
}
.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.detail-label {
  font-size: 0.8rem;
  color: var(--text-muted);
}
.detail-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}
.total-row { border-top: 1px solid var(--border); padding-top: 0.4rem; margin-top: 0.1rem; }
.total-value { color: var(--accent); font-size: 1rem; }

.admin-note {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  padding: 0.625rem 0.75rem;
  background: var(--info-bg);
  border-left: 3px solid var(--info);
  border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
  font-size: 0.8rem;
  color: var(--info);
  line-height: 1.5;
}
.admin-note svg { flex-shrink: 0; margin-top: 1px; }

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1.25rem;
  background: var(--bg-elevated);
  border-top: 1px solid var(--border);
}
.date { font-size: 0.78rem; color: var(--text-muted); }
.card-actions { display: flex; gap: 0.4rem; }

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
  max-width: 580px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-lg);
  animation: slideUp 0.2s ease;
}
@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.modal-sm { max-width: 420px; }

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

/* Forms */
.form-group { margin-bottom: 1.25rem; }
.form-group:last-child { margin-bottom: 0; }
.form-group label {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.625rem 0.875rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.9rem;
  font-family: inherit;
  box-sizing: border-box;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}
.form-group textarea { resize: vertical; }

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.field-error { display: block; margin-top: 0.3rem; font-size: 0.8rem; color: var(--danger); }
.form-error {
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  background: var(--danger-bg);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: var(--radius-sm);
  color: var(--danger);
  font-size: 0.875rem;
}

/* Total display */
.total-display {
  padding: 1.25rem;
  background: var(--bg-elevated);
  border: 1px solid var(--accent-border);
  border-radius: var(--radius-md);
  text-align: center;
  margin-top: 1rem;
}
.total-label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; }
.total-amount { font-size: 2rem; font-weight: 700; color: var(--accent); margin-bottom: 0.25rem; }
.total-breakdown { font-size: 0.8rem; color: var(--text-secondary); }

/* Delete modal */
.delete-msg { margin: 0 0 0.5rem; color: var(--text-primary); }
.delete-warn { margin: 0; color: var(--warning); font-size: 0.875rem; font-weight: 500; }

/* Responsive */
@media (max-width: 768px) {
  .requests-page { padding: 1rem; }
  .page-header { flex-direction: column; align-items: stretch; }
  .header-left { flex-direction: column; }
  .btn-primary { width: 100%; justify-content: center; }
  .requests-grid { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
}
</style>
