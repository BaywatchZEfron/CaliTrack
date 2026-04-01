<template>
  <div class="insight-item">
    <div class="insight-dot" :class="`dot-${tipo}`" />
    <div class="insight-text">{{ texto }}</div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  tipo: 'success' | 'warning' | 'info' | 'danger'
  texto: string
}>()
</script>

<style scoped>
.insight-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px 0;
  border-bottom: 1px solid var(--border);
}
.insight-item:last-child { border-bottom: none; }
.insight-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
}
.dot-success { background: var(--green); }
.dot-warning { background: var(--amber); }
.dot-info    { background: var(--accent); }
.dot-danger  { background: var(--red); }
.insight-text { font-size: 12px; color: var(--text2); line-height: 1.5; }
</style>