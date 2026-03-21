<template>
  <div class="metric-card">
    <div class="metric-label">{{ label }}</div>
    <div class="metric-value">
      {{ value }}<span v-if="unit" class="metric-unit"> {{ unit }}</span>
    </div>
    <div v-if="trend" class="metric-trend" :class="trendClass">
      {{ trend }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  label: string
  value: string | number
  unit?: string
  trend?: string
  trendType?: 'up' | 'down' | 'neutral'
}>()

const trendClass = computed(() => ({
  'trend-up': props.trendType === 'up',
  'trend-down': props.trendType === 'down',
  'trend-neutral': props.trendType === 'neutral',
}))
</script>

<style scoped>
.metric-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 16px;
}
.metric-label {
  font-size: 11px;
  color: var(--text2);
  margin-bottom: 6px;
}
.metric-value {
  font-family: var(--font-head);
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  line-height: 1;
}
.metric-unit { font-size: 14px; color: var(--text2); }
.metric-trend { font-size: 11px; margin-top: 4px; }
.trend-up   { color: var(--green); }
.trend-down { color: var(--red); }
.trend-neutral { color: var(--text3); }
</style>