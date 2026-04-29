<template>
  <div class="pagination">
    <div class="page-info">
      Showing <strong>{{ from }}</strong>–<strong>{{ to }}</strong> of <strong>{{ total }}</strong>
    </div>

    <div class="page-controls">
      <button @click="$emit('change', currentPage - 1)" :disabled="currentPage === 1" class="page-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>

      <button
        v-for="page in visiblePages"
        :key="page"
        @click="page !== '...' && $emit('change', page)"
        :class="['page-num', { active: page === currentPage, dots: page === '...' }]"
        :disabled="page === '...'"
      >{{ page }}</button>

      <button @click="$emit('change', currentPage + 1)" :disabled="currentPage === totalPages" class="page-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>
    </div>

    <div class="per-page">
      <span>Show</span>
      <select :value="perPage" @change="$emit('per-page-change', Number($event.target.value))" class="per-page-select">
        <option :value="10">10</option>
        <option :value="25">25</option>
        <option :value="50">50</option>
        <option :value="100">100</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalPages:  { type: Number, required: true },
  perPage:     { type: Number, default: 15 },
  total:       { type: Number, required: true }
})

defineEmits(['change', 'per-page-change'])

const from = computed(() => (props.currentPage - 1) * props.perPage + 1)
const to   = computed(() => Math.min(props.currentPage * props.perPage, props.total))

const visiblePages = computed(() => {
  const pages = []
  const max = 5
  if (props.totalPages <= max) {
    for (let i = 1; i <= props.totalPages; i++) pages.push(i)
  } else if (props.currentPage <= 3) {
    for (let i = 1; i <= 4; i++) pages.push(i)
    pages.push('...'); pages.push(props.totalPages)
  } else if (props.currentPage >= props.totalPages - 2) {
    pages.push(1); pages.push('...')
    for (let i = props.totalPages - 3; i <= props.totalPages; i++) pages.push(i)
  } else {
    pages.push(1); pages.push('...')
    pages.push(props.currentPage - 1); pages.push(props.currentPage); pages.push(props.currentPage + 1)
    pages.push('...'); pages.push(props.totalPages)
  }
  return pages
})
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  border-top: 1px solid var(--border);
  gap: 1rem;
  flex-wrap: wrap;
}

.page-info {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.page-info strong { color: var(--text-secondary); }

.page-controls {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.page-btn, .page-num {
  min-width: 32px;
  height: 32px;
  padding: 0 0.4rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition);
}

.page-btn:hover:not(:disabled),
.page-num:hover:not(:disabled):not(.dots) {
  background: var(--bg-hover);
  border-c7olor: var(--border-light);
  color: var(--text-primary);
}

.page-btn:disabled, .page-num:disabled { opacity: 0.4; cursor: not-allowed; }

.page-num.active {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}

.page-num.dots { border: none; background: none; cursor: default; }

.per-page {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: var(--text-muted);
}

.per-page-select {
  padding: 0.3rem 0.5rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.8rem;
  cursor: pointer;
}

.per-page-select:focus { outline: none; border-color: var(--accent); }

@media (max-width: 768px) {
  .pagination { flex-direction: column; align-items: stretch; }
  .page-controls { justify-content: center; }
  .per-page { justify-content: center; }
}
</style>
