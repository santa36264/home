<template>
  <div :class="['spinner-wrap', size]">
    <div class="spinner"></div>
    <p v-if="text" class="spinner-text">{{ text }}</p>
  </div>
</template>

<script setup>
defineProps({
  size: { type: String, default: 'medium', validator: v => ['small','medium','large'].includes(v) },
  text: { type: String, default: '' }
})
</script>

<style scoped>
.spinner-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  gap: 0.75rem;
}

.spinner {
  border-radius: 50%;
  border: 2px solid var(--border);
  border-top-color: var(--accent);
  animation: spin 0.7s linear infinite;
}

.spinner-wrap.small  .spinner { width: 20px; height: 20px; }
.spinner-wrap.medium .spinner { width: 36px; height: 36px; }
.spinner-wrap.large  .spinner { width: 52px; height: 52px; border-width: 3px; }

.spinner-text {
  font-size: 0.85rem;
  color: var(--text-muted);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
