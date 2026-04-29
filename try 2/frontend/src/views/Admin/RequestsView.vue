<template>
  <AdminLayout title="All Requests">
    <MobileSidebar title="All Requests" />

    <div class="admin-requests-page">
      <!-- Header -->
      <div class="page-header">
        <div class="header-left">
          <SearchInput v-model="searchQuery" placeholder="Search requests, users..." />
          <FilterDropdown v-model="filterStatus" label="Status" placeholder="All Statuses" :options="statusOptions" />
          <ExportButton type="requests" />
        </div>
      </div>

      <!-- Status Tabs -->
      <div class="status-tabs">
        <button @click="filterStatus = ''" :class="['tab-btn', { active: filterStatus === '' }]">
          All <span class="tab-count">{{ totalItems }}</span>
        </button>
        <button @click="filterStatus = 'pending'" :class="['tab-btn', 'tab-pending', { active: filterStatus === 'pending' }]">
          Pending <span class="tab-count">{{ pendingCount }}</span>
        </button>
        <button @click="filterStatus = 'approved'" :class="['tab-btn', 'tab-approved', { active: filterStatus === 'approved' }]">
          Approved <span class="tab-count">{{ approvedCount }}</span>
        </button>
        <button @click="filterStatus = 'rejected'" :class="['tab-btn', 'tab-rejected', { active: filterStatus === 'rejected' }]">
          Rejected <span class="tab-count">{{ rejectedCount }}</span>
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <LoadingSpinner size="large" text="Loading requests..." />
      </div>

      <!-- Empty -->
      <div v-else-if="requests.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <h3>No {{ filterStatus ? filterStatus : '' }} requests found</h3>
        <p>{{ searchQuery ? 'Try adjusting your search or filters' : '' }}</p>
      </div>

      <!-- Table -->
      <div v-else class="table-container">
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in requests" :key="request.id">
                <td class="col-id">#{{ request.id }}</td>
                <td>
                  <div class="user-cell">
                    <span class="user-name">{{ request.user?.name }}</span>
                    <span class="user-email">{{ request.user?.email }}</span>
                  </div>
                </td>
                <td>
                  <div class="title-cell">
                    <span class="title-text">{{ request.title }}</span>
                    <span class="desc-text">{{ request.description }}</span>
                  </div>
                </td>
                <td>{{ request.quantity }}</td>
                <td>${{ request.unit_price }}</td>
                <td class="col-total">${{ request.total }}</td>
                <td>
                  <span class="status-chip" :class="`status-${request.status}`">{{ request.status }}</span>
                </td>
                <td class="col-date">{{ formatDate(request.created_at) }}</td>
                <td>
                  <div class="action-btns">
                    <button @click="viewRequest(request)" class="icon-btn icon-btn-info" title="View Details">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    <button v-if="request.status === 'pending'" @click="approveRequest(request)" class="icon-btn icon-btn-success" title="Approve">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                    <button v-if="request.status === 'pending'" @click="rejectRequest(request)" class="icon-btn icon-btn-danger" title="Reject">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination
          :current-page="currentPage"
          :total-pages="totalPages"
          :per-page="perPage"
          :total="totalItems"
          @change="currentPage = $event"
          @per-page-change="handlePerPageChange"
        />
      </div>

      <!-- View Modal -->
      <div v-if="showViewModal" class="modal-overlay" @click="closeViewModal">
        <div class="modal modal-lg" @click.stop>
          <div class="modal-header">
            <h2>Request Details</h2>
            <button @click="closeViewModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="detail-grid">
              <div class="detail-item">
                <label>Request ID</label>
                <span>#{{ viewingRequest?.id }}</span>
              </div>
              <div class="detail-item">
                <label>Status</label>
                <span class="status-chip" :class="`status-${viewingRequest?.status}`">{{ viewingRequest?.status }}</span>
              </div>
              <div class="detail-item">
                <label>User</label>
                <span>{{ viewingRequest?.user?.name }} ({{ viewingRequest?.user?.email }})</span>
              </div>
              <div class="detail-item">
                <label>Created</label>
                <span>{{ formatDate(viewingRequest?.created_at) }}</span>
              </div>
            </div>
            <div class="detail-section">
              <label>Title</label>
              <p>{{ viewingRequest?.title }}</p>
            </div>
            <div class="detail-section">
              <label>Description</label>
              <p>{{ viewingRequest?.description }}</p>
            </div>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Quantity</label>
                <span>{{ viewingRequest?.quantity }}</span>
              </div>
              <div class="detail-item">
                <label>Unit Price</label>
                <span>${{ viewingRequest?.unit_price }}</span>
              </div>
              <div class="detail-item">
                <label>Total</label>
                <span class="total-highlight">${{ viewingRequest?.total }}</span>
              </div>
            </div>
            <div v-if="viewingRequest?.admin_note" class="detail-section">
              <label>Admin Note</label>
              <p class="admin-note-text">{{ viewingRequest?.admin_note }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button v-if="viewingRequest?.status === 'pending'" @click="approveRequest(viewingRequest)" class="btn-success">Approve Request</button>
            <button v-if="viewingRequest?.status === 'pending'" @click="rejectRequest(viewingRequest)" class="btn-danger">Reject Request</button>
            <button @click="closeViewModal" class="btn-secondary">Close</button>
          </div>
        </div>
      </div>

      <!-- Approve Modal -->
      <div v-if="showApproveModal" class="modal-overlay" @click="closeApproveModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Approve Request</h2>
            <button @click="closeApproveModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <form @submit.prevent="confirmApprove">
            <div class="modal-body">
              <p class="modal-desc">Are you sure you want to approve this request?</p>
              <div class="form-group">
                <label for="approve-note">Admin Note (Optional)</label>
                <textarea id="approve-note" v-model="approveNote" rows="4" placeholder="Add a note for the user..."></textarea>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeApproveModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="processing" class="btn-success">
                {{ processing ? 'Approving...' : 'Approve Request' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h2>Reject Request</h2>
            <button @click="closeRejectModal" class="close-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <form @submit.prevent="confirmReject">
            <div class="modal-body">
              <p class="modal-desc">Are you sure you want to reject this request?</p>
              <div class="form-group">
                <label for="reject-note">Reason for Rejection (Required)</label>
                <textarea id="reject-note" v-model="rejectNote" rows="4" placeholder="Explain why this request is being rejected..."></textarea>
              </div>
              <div v-if="error" class="form-error">{{ error }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeRejectModal" class="btn-secondary">Cancel</button>
              <button type="submit" :disabled="processing" class="btn-danger">
                {{ processing ? 'Rejecting...' : 'Reject Request' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
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

const requests = ref([])
const loading = ref(false)
const error = ref('')

const searchQuery = ref('')
const filterStatus = ref('')
const currentPage = ref(1)
const perPage = ref(15)
const totalItems = ref(0)
const totalPages = ref(1)

const pendingCount = ref(0)
const approvedCount = ref(0)
const rejectedCount = ref(0)

const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'approved', label: 'Approved' },
  { value: 'rejected', label: 'Rejected' }
]

const handlePerPageChange = (newPerPage) => {
  perPage.value = newPerPage
  currentPage.value = 1
}

const showViewModal = ref(false)
const showApproveModal = ref(false)
const showRejectModal = ref(false)

const viewingRequest = ref(null)
const requestToProcess = ref(null)
const approveNote = ref('')
const rejectNote = ref('')
const processing = ref(false)

const fetchRequests = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/requests', {
      params: {
        page: currentPage.value,
        per_page: perPage.value,
        search: searchQuery.value || undefined,
        status: filterStatus.value || undefined,
      }
    })
    const res = response.data
    requests.value = res.data ?? res
    totalItems.value = res.total ?? 0
    totalPages.value = res.last_page ?? 1

    if (res.meta) {
      pendingCount.value  = res.meta.pending_count  ?? 0
      approvedCount.value = res.meta.approved_count ?? 0
      rejectedCount.value = res.meta.rejected_count ?? 0
    } else {
      pendingCount.value  = requests.value.filter(r => r.status === 'pending').length
      approvedCount.value = requests.value.filter(r => r.status === 'approved').length
      rejectedCount.value = requests.value.filter(r => r.status === 'rejected').length
    }
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

const viewRequest = (request) => {
  viewingRequest.value = request
  showViewModal.value = true
}

const approveRequest = (request) => {
  requestToProcess.value = request
  showApproveModal.value = true
  closeViewModal()
}

const rejectRequest = (request) => {
  requestToProcess.value = request
  showRejectModal.value = true
  closeViewModal()
}

const confirmApprove = async () => {
  error.value = ''
  processing.value = true
  try {
    await api.post(`/admin/requests/${requestToProcess.value.id}/approve`, { admin_note: approveNote.value || null })
    success('Request Approved', 'The request has been approved successfully')
    closeApproveModal()
    await fetchRequests()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to approve request'
    showError('Approval Failed', error.value)
  } finally {
    processing.value = false
  }
}

const confirmReject = async () => {
  error.value = ''
  processing.value = true
  try {
    await api.post(`/admin/requests/${requestToProcess.value.id}/reject`, {
      admin_note: rejectNote.value || null
    })
    success('Request Rejected', 'The request has been rejected')
    closeRejectModal()
    await fetchRequests()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to reject request'
    showError('Rejection Failed', error.value)
  } finally {
    processing.value = false
  }
}

const closeViewModal = () => { showViewModal.value = false; viewingRequest.value = null }
const closeApproveModal = () => { showApproveModal.value = false; requestToProcess.value = null; approveNote.value = ''; error.value = '' }
const closeRejectModal = () => { showRejectModal.value = false; requestToProcess.value = null; rejectNote.value = ''; error.value = '' }

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

onMounted(() => { fetchRequests() })
</script>

<style scoped>
.admin-requests-page { padding: 2rem; }

.page-header { margin-bottom: 1.25rem; }
.header-left { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }

/* Status tabs */
.status-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}
.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
}
.tab-btn:hover { background: var(--bg-hover); border-color: var(--border-light); color: var(--text-primary); }
.tab-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
.tab-pending.active { background: var(--warning); border-color: var(--warning); }
.tab-approved.active { background: var(--success); border-color: var(--success); }
.tab-rejected.active { background: var(--danger); border-color: var(--danger); }
.tab-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 5px;
  background: rgba(255,255,255,0.2);
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}
.tab-btn:not(.active) .tab-count { background: var(--bg-base); color: var(--text-muted); }

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

/* Table */
.table-container {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}
.table-scroll { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; min-width: 900px; }
.data-table th {
  padding: 0.875rem 1rem;
  text-align: left;
  background: var(--bg-elevated);
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 1;
}
.data-table td {
  padding: 0.875rem 1rem;
  border-bottom: 1px solid var(--border);
  color: var(--text-primary);
  font-size: 0.875rem;
  vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: var(--bg-hover); }

.col-id { color: var(--text-muted); font-size: 0.8rem; }
.col-total { color: var(--accent); font-weight: 700; }
.col-date { color: var(--text-secondary); font-size: 0.8rem; white-space: nowrap; }

.user-cell { display: flex; flex-direction: column; gap: 0.15rem; }
.user-name { font-weight: 600; color: var(--text-primary); }
.user-email { font-size: 0.78rem; color: var(--text-muted); }

.title-cell { display: flex; flex-direction: column; gap: 0.15rem; max-width: 260px; }
.title-text { font-weight: 600; color: var(--text-primary); }
.desc-text { font-size: 0.78rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

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
}
.status-pending { background: var(--warning-bg); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); }
.status-approved { background: var(--success-bg); color: var(--success); border: 1px solid rgba(34,197,94,0.3); }
.status-rejected { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }

/* Action buttons */
.action-btns { display: flex; gap: 0.35rem; }
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
.icon-btn-info { background: var(--info-bg); color: var(--info); }
.icon-btn-info:hover { background: var(--info); color: #fff; transform: scale(1.1); }
.icon-btn-success { background: var(--success-bg); color: var(--success); }
.icon-btn-success:hover { background: var(--success); color: #fff; transform: scale(1.1); }
.icon-btn-danger { background: var(--danger-bg); color: var(--danger); }
.icon-btn-danger:hover { background: var(--danger); color: #fff; transform: scale(1.1); }

/* Buttons */
.btn-primary {
  padding: 0.625rem 1.25rem;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition);
}
.btn-primary:hover:not(:disabled) { background: var(--accent-hover); }
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
  transition: background var(--transition);
}
.btn-secondary:hover { background: var(--bg-hover); color: var(--text-primary); }

.btn-success {
  padding: 0.625rem 1.25rem;
  background: var(--success-bg);
  color: var(--success);
  border: 1px solid rgba(34,197,94,0.3);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition);
}
.btn-success:hover:not(:disabled) { background: var(--success); color: #fff; }
.btn-success:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-danger {
  padding: 0.625rem 1.25rem;
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background var(--transition);
}
.btn-danger:hover:not(:disabled) { background: var(--danger); color: #fff; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

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
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-lg { max-width: 760px; }

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

.modal-desc { margin: 0 0 1.25rem; color: var(--text-secondary); }

/* Detail grid */
.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 1.25rem;
  padding: 1rem;
  background: var(--bg-elevated);
  border-radius: var(--radius-sm);
}
.detail-item { display: flex; flex-direction: column; gap: 0.3rem; }
.detail-item label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
.detail-item span { color: var(--text-primary); font-size: 0.9rem; }
.total-highlight { color: var(--accent); font-weight: 700; font-size: 1.1rem; }

.detail-section { margin-bottom: 1.25rem; }
.detail-section label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
.detail-section p { margin: 0; color: var(--text-primary); line-height: 1.6; font-size: 0.9rem; }
.admin-note-text {
  padding: 0.75rem;
  background: var(--info-bg);
  border-left: 3px solid var(--info);
  border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
  color: var(--info);
}

/* Forms */
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); }
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
  resize: vertical;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.form-group textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-error {
  margin-top: 0.75rem;
  padding: 0.75rem 1rem;
  background: var(--danger-bg);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: var(--radius-sm);
  color: var(--danger);
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .admin-requests-page { padding: 1rem; }
  .status-tabs { flex-direction: column; }
  .tab-btn { width: 100%; justify-content: space-between; }
  .header-left { flex-direction: column; }
}
</style>
