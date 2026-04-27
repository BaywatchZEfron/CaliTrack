<template>
  <AppLayout>
    <div class="progress-page">

      <div v-if="isLoading" style="color: var(--text2); padding: 40px; text-align: center;">
        Cargando progresión...
      </div>

      <div v-else-if="progressList.length === 0" style="color: var(--text2); padding: 40px; text-align: center;">
        No hay ejercicios registrados todavía. Ve a <RouterLink to="/log">Log</RouterLink> y registra tu primer entrenamiento.
      </div>

      <div v-else class="progress-grid">

        <!-- Lista de ejercicios -->
        <div class="ejercicios-col">
          <div class="card-title" style="margin-bottom: 12px">Ejercicios</div>
          <div class="ejercicio-list">
            <div
              v-for="(item, index) in progressList"
              :key="item.exercise.id"
              class="ejercicio-item"
              :class="{ active: activeExercise?.id === item.exercise.id }"
              @click="changeExercise(index)"
            >
              <div class="ex-icon">{{ icons[item.exercise.name] ?? '💪' }}</div>
              <div class="ex-info">
                <div class="ex-nombre">{{ item.exercise.name }}</div>
                <div class="ex-sub">{{ item.points.length }} sesiones</div>
              </div>
              <div class="ex-right">
                <div class="ex-val">
                  {{ item.current_max }}
                  <span class="ex-unit">reps</span>
                </div>
                <div class="ex-trend" v-if="item.points.length >= 2">
                  {{
                    (() => {
                      const last = item.points[item.points.length - 1]?.value ?? 0
                      const prev = item.points[item.points.length - 2]?.value ?? 0
                      const diff = last - prev
                      return diff > 0 ? `↑ +${diff}` : diff < 0 ? `↓ ${diff}` : '→ igual'
                    })()
                  }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detalle -->
        <div v-if="activeProgress" class="detalle-col">

          <!-- KPIs -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">{{ activeExercise?.name }}</div>
              <span v-if="improvement > 0" class="pr-badge">PR activo</span>
            </div>
            <div class="kpi-row">
              <div class="kpi-item">
                <div class="kpi-label">Actual</div>
                <div class="kpi-val">{{ currentValue }}</div>
                <div class="kpi-sub">reps</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Objetivo</div>
                <div class="kpi-val">{{ goal }}</div>
                <div class="kpi-sub">reps</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Progreso</div>
                <div class="kpi-val" style="color: var(--accent)">{{ goalPercentage }}%</div>
                <div class="kpi-sub">al objetivo</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Mejora total</div>
                <div class="kpi-val" style="color: var(--green)">+{{ improvement }}</div>
                <div class="kpi-sub">desde inicio</div>
              </div>
            </div>
            <div class="progress-track" style="margin-top: 14px">
              <div class="progress-fill" :style="{ width: goalPercentage + '%' }" />
            </div>
            <div class="progress-labels">
              <span>{{ startValue }} (inicio)</span>
              <span>{{ goal }} (meta)</span>
            </div>
          </div>

          <!-- Gráfica -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Evolución</div>
              <span style="font-size: 11px; color: var(--text2)">
                {{ progression.length }} semanas
              </span>
            </div>
            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- Historial de workouts con este ejercicio -->
          <div class="card">
            <div class="card-title" style="margin-bottom: 14px">Historial</div>
            <div v-if="activeProgress.points.length > 0">
              <div
                v-for="(point, i) in [...activeProgress.points].reverse()"
                :key="i"
                class="hist-item"
              >
                <div class="hist-header">
                  <span class="hist-fecha">{{ point.week }}</span>
                  <span class="hist-badge">Máx: {{ point.value }} reps</span>
                </div>
              </div>
            </div>
            <div v-else class="empty-msg">
              No hay registros todavía.
            </div>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
} from 'chart.js'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useProgress } from '@/composables/useProgress'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  progressList,
  activeExercise,
  activeProgress,
  progression,
  currentValue,
  startValue,
  improvement,
  goal,
  goalPercentage,
  isLoading,
  changeExercise,
} = useProgress()

const icons: Record<string, string> = {
  'Pull-up': '🧗',
  'Dip': '🏋️',
  'Handstand Push-up': '🤸',
  'Muscle-up': '💪',
  'Pistol Squat': '🦵',
  'Push-up': '💪',
  'Diamond Push-up': '💎',
}

function formatDate(dateStr: string): string {
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

const chartData = computed(() => ({
  labels: progression.value.map(p => p.week),
  datasets: [{
    data: progression.value.map(p => p.value),
    borderColor: '#3d8ef8',
    backgroundColor: 'rgba(61,142,248,0.1)',
    borderWidth: 2.5,
    pointBackgroundColor: '#111318',
    pointBorderColor: '#3d8ef8',
    pointBorderWidth: 2,
    pointRadius: 4,
    tension: 0.3,
    fill: true,
  }],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { color: '#1e222d' }, ticks: { color: '#4e5566', font: { size: 11 } } },
    y: { grid: { color: '#1e222d' }, ticks: { color: '#4e5566', font: { size: 11 } } },
  },
}
</script>

<style scoped>
/* 🔒 STYLE ORIGINAL INTACTO */
.progress-page { display: flex; flex-direction: column; gap: 20px; }
.progress-grid { display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start; }
.card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px; }
.card:last-child { margin-bottom: 0; }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-title { font-family: var(--font-head); font-size: 12px; font-weight: 600; color: var(--text2); text-transform: uppercase; letter-spacing: 0.06em; }

/* resto igual… */
</style>