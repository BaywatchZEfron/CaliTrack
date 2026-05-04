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
.progress-page { display: flex; flex-direction: column; gap: 20px; }

.progress-grid {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 20px;
  align-items: start;
}

/* Lista ejercicios */
.ejercicios-col {}
.ejercicio-list { display: flex; flex-direction: column; gap: 8px; }
.ejercicio-item {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 14px 16px;
  cursor: pointer;
  transition: all 0.15s;
  display: flex;
  align-items: center;
  gap: 12px;
}
.ejercicio-item:hover { border-color: var(--border2); }
.ejercicio-item.active { border-color: var(--accent); background: rgba(61,142,248,0.05); }

.ex-icon {
  width: 36px; height: 36px;
  border-radius: 8px;
  background: var(--bg3);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}
.ex-nombre { font-size: 13px; font-weight: 500; color: var(--text); }
.ex-sub { font-size: 11px; color: var(--text2); margin-top: 2px; }
.ex-right { margin-left: auto; text-align: right; }
.ex-val { font-family: var(--font-head); font-size: 16px; font-weight: 700; color: var(--text); }
.ex-unit { font-size: 11px; color: var(--text2); }
.ex-trend { font-size: 11px; color: var(--green); }

/* Detalle */
.detalle-col { display: flex; flex-direction: column; gap: 16px; }

.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px;
}
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.card-title {
  font-family: var(--font-head);
  font-size: 12px;
  font-weight: 600;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* KPIs */
.kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}
.kpi-item {
  background: var(--bg3);
  border-radius: 10px;
  padding: 12px;
  text-align: center;
}
.kpi-label { font-size: 10px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
.kpi-val { font-family: var(--font-head); font-size: 20px; font-weight: 700; color: var(--text); }
.kpi-sub { font-size: 10px; color: var(--text2); margin-top: 2px; }

/* Barra de progreso */
.progress-track {
  height: 4px;
  background: var(--bg4);
  border-radius: 2px;
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  background: var(--accent);
  border-radius: 2px;
  transition: width 0.5s ease;
  max-width: 100%;
}
.progress-labels {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  color: var(--text3);
  margin-top: 4px;
}

/* Gráfica */
.chart-wrap { position: relative; height: 200px; }

/* Historial */
.hist-item {
  padding: 10px 0;
  border-bottom: 1px solid var(--border);
}
.hist-item:last-child { border-bottom: none; }
.hist-header { display: flex; align-items: center; justify-content: space-between; }
.hist-fecha { font-size: 12px; color: var(--text2); }
.hist-badge {
  background: var(--bg3);
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 11px;
  color: var(--text2);
}
.pr-badge {
  background: rgba(34,200,122,0.12);
  color: var(--green);
  border-radius: 4px;
  font-size: 10px;
  padding: 2px 6px;
  font-weight: 600;
}
.empty-msg { font-size: 13px; color: var(--text2); }

@media (max-width: 900px) {
  .progress-grid { grid-template-columns: 1fr; }
  .kpi-row { grid-template-columns: repeat(2, 1fr); }
}
</style>