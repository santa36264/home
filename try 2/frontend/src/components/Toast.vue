<template>
  <transition name="toast">
    <div v-if="visible" :class="['toast', type]">
      <div class="toast-icon">
        <svg v-if="type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <svg v-else-if="type === 'error'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        <svg v-else-if="type === 'warning'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div class="toast-body">
        <div class="toast-title">{{ title }}</div>
        <div v-if="message" class="toast-msg">{{ message }}</div>
      </div>
      <button @click="close" class="toast-close" aria-label="Close">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  type:     { type: String, default: 'success', validator: v => ['success','error','warning','info'].includes(v) },
  title:    { type: String, required: true },
  message:  { type: String, default: '' },
  duration: { type: Number, default: 3500 }
})

const emit = defineEmits(['close'])
const visible = ref(false)

const close = () => {
  visible.value = false
  setTimeout(() => emit('close'), 300)
}

onMounted(() => {
  visible.value = true
  if (props.duration > 0) setTimeout(close, props.duration)
})
</script>

<style scoped>
.toast {
  position: fixed;
  top: 1.25rem;
  right: 1.25rem;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.9rem 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  z-index: 9999;
  min-width: 280px;
  max-width: 420px;
}

.toast.success {
   border-left: 3px solid 
   var(--success); 
  }
.toast.error   { border-left: 3px solid var(--danger); }
.toast.warning { border-left: 3px solid var(--warning); }
.toast.info    { border-left: 3px solid var(--info); }

.toast-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.toast.success .toast-icon { background: var(--success-bg); color: var(--success); }
.toast.error   .toast-icon { background: var(--danger-bg);  color: var(--danger); }
.toast.warning .toast-icon { background: var(--warning-bg); color: var(--warning); }
.toast.info    .toast-icon { background: var(--info-bg);    color: var(--info); }

.toast-body { flex: 1; min-width: 0; }

.toast-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.15rem;
}

.toast-msg {
  font-size: 0.8rem;
  color: var(--text-muted);
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 0.2rem;
  border-radius: 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition);
  flex-shrink: 0;
}

.toast-close:hover { background: var(--bg-hover); color: var(--text-primary); }

.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { transform: translateX(110%); opacity: 0; }
.toast-leave-to   { transform: translateY(-12px); opacity: 0; }
</style>
