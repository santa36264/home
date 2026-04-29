<template>
  <div class="filter-wrap">
    <label v-if="label" class="filter-label">{{ label }}</label>
    <select
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="filter-select"
    >
      <option value="">{{ placeholder }}</option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'All' },
  options: { type: Array, required: true }
})
defineEmits(['update:modelValue'])
</script>

<style scoped>
.filter-wrap {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.filter-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--text-muted);
}

.filter-select {
  padding: 0.55rem 2rem 0.55rem 0.75rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.875rem;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238b95a8' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.65rem center;
  min-width: 140px;
  transition: border-color var(--transition), box-shadow var(--transition);
}

.filter-select:hover { border-color: var(--border-light); }
.filter-select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

.filter-select option { background: var(--bg-elevated); color: var(--text-primary); }

@media (max-width: 768px) { .filter-select { min-width: 100%; } }
</style>
