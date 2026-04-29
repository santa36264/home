<template>
  <div :class="['stat-card', color]">
    <div class="stat-icon-wrap">
      <span class="stat-icon">{{ icon }}</span>
    </div>
    <div class="stat-body">
      <div class="stat-label">{{ title }}</div>
      <div class="stat-value">{{ value }}</div>
      <div v-if="trend !== null" class="stat-trend" :class="trend >= 0 ? 'up' : 'down'">
        <span>{{ trend >= 0 ? '↑' : '↓' }}</span>
        {{ Math.abs(trend) }}%
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon:  { type: String, required: true },
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  trend: { type: Number, default: null },
  color: { type: String, default: 'blue', validator: v => ['blue','green','orange','purple','red'].includes(v) }
})
</script>

<style scoped>
.stat-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform var(--transition), box-shadow var(--transition);
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.stat-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.stat-card.blue  .stat-icon-wrap { background: rgba(79,142,247,0.12); border: 1px solid rgba(79,142,247,0.2); }
.stat-card.green .stat-icon-wrap { background: rgba(34,197,94,0.12);  border: 1px solid rgba(34,197,94,0.2); }
.stat-card.orange .stat-icon-wrap { background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.2); }
.stat-card.purple .stat-icon-wrap { background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.2); }
.stat-card.red   .stat-icon-wrap { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.2); }

.stat-body { flex: 1; min-width: 0; }

.stat-label {
  font-size: 0.78rem;
  font-weight: 500;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.3rem;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.stat-trend {
  display: inline-flex;
  align-items: center;
  gap: 0.2rem;
  margin-top: 0.4rem;
  font-size: 0.78rem;
  font-weight: 600;
}

.stat-trend.up   { color: var(--success); }
.stat-trend.down { color: var(--danger); }

/* Left accent bar */
.stat-card.blue   { border-left: 3px solid var(--accent); }
.stat-card.green  { border-left: 3px solid var(--success); }
.stat-card.orange { border-left: 3px solid var(--warning); }
.stat-card.purple { border-left: 3px solid #a855f7; }
.stat-card.red    { border-left: 3px solid var(--danger); }
</style>
