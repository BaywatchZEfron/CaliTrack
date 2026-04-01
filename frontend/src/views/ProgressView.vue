<template>
  <AppLayout>
    <div class="progress-page">

      <div class="progress-grid">

        <!-- Lista de ejercicios -->
        <div class="ejercicios-col">
          <div class="card-title" style="margin-bottom: 12px">Ejercicios</div>
          <div class="ejercicio-list">
            <div
              v-for="ex in mockExercises"
              :key="ex.id"
              class="ejercicio-item"
              :class="{ active: ejercicioActivo === ex.nombre }"
              @click="cambiarEjercicio(ex.nombre)"
            >
              <div class="ex-icon">{{ iconos[ex.nombre] ?? '💪' }}</div>
              <div class="ex-info">
                <div class="ex-nombre">{{ ex.nombre }}</div>
                <div class="ex-sub">
                  {{ ultimaSesion(ex.nombre) }}
                </div>
              </div>
              <div class="ex-right">
                <div class="ex-val">
                  {{ ultimoValor(ex.nombre) }}
                  <span class="ex-unit">{{ ex.unidad }}</span>
                </div>
                <div class="ex-trend">
                  {{ tendencia(ex.nombre) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detalle del ejercicio activo -->
        <div class="detalle-col">

          <!-- KPIs -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">{{ ejercicioActivo }}</div>
              <span v-if="mejora > 0" class="pr-badge">PR activo</span>
            </div>
            <div class="kpi-row">
              <div class="kpi-item">
                <div class="kpi-label">Actual</div>
                <div class="kpi-val">{{ valorActual }}</div>
                <div class="kpi-sub">{{ unidadActiva }}</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Objetivo</div>
                <div class="kpi-val">{{ objetivo }}</div>
                <div class="kpi-sub">{{ unidadActiva }}</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Progreso</div>
                <div class="kpi-val" style="color: var(--accent)">{{ porcentajeObjetivo }}%</div>
                <div class="kpi-sub">al objetivo</div>
              </div>
              <div class="kpi-item">
                <div class="kpi-label">Mejora total</div>
                <div class="kpi-val" style="color: var(--green)">+{{ mejora }}</div>
                <div class="kpi-sub">desde inicio</div>
              </div>
            </div>
            <div class="progress-track" style="margin-top: 14px">
              <div class="progress-fill" :style="{ width: porcentajeObjetivo + '%' }" />
            </div>
            <div class="progress-labels">
              <span>{{ valorInicio }} (inicio)</span>
              <span>{{ objetivo }} (meta)</span>
            </div>
          </div>

          <!-- Gráfica -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Evolución</div>
              <span style="font-size: 11px; color: var(--text2)">
                {{ progresion.length }} sesiones
              </span>
            </div>
            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- Historial de sesiones -->
          <div class="card">
            <div class="card-title" style="margin-bottom: 14px">Historial</div>
            <div v-if="workoutsDelEjercicio.length > 0">
              <div
                v-for="workout in workoutsDelEjercicio"
                :key="workout.id"
                class="hist-item"
              >
                <div class="hist-header">
                  <span class="hist-fecha">{{ formatFecha(workout.fecha) }}</span>
                  <span v-if="esPR(workout)" class="pr-badge">PR</span>
                </div>
                <div class="hist-series">
                  <span
                    v-for="s in workout.series"
                    :key="s.serie"
                    class="hist-badge"
                  >
                    S{{ s.serie }}: {{ s.reps }}r · RPE {{ s.rpe }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="empty-msg">
              No hay registros de este ejercicio todavía.
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
import { mockExercises, mockProgressByExercise, mockDashboard } from '@/data/mockData'
import type { Workout } from '@/types'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  ejercicioActivo,
  progresion,
  valorActual,
  valorInicio,
  mejora,
  objetivo,
  porcentajeObjetivo,
  workoutsDelEjercicio,
  cambiarEjercicio,
} = useProgress()

const iconos: Record<string, string> = {
  'Dominadas / Pull-ups': '🧗',
  'Fondos / Dips': '🏋️',
  'Handstand': '🤸',
  'Muscle-up': '💪',
  'Pistol squat': '🦵',
}

const unidadActiva = computed(() =>
  mockExercises.find(e => e.nombre === ejercicioActivo.value)?.unidad ?? 'reps'
)

// Helpers para la lista de ejercicios
function ultimoValor(nombre: string): number {
  const puntos = mockProgressByExercise[nombre]
  return puntos ? puntos[puntos.length - 1].valor : 0
}

function ultimaSesion(nombre: string): string {
  const workout = mockDashboard.workouts_recientes.find(w => w.ejercicio === nombre)
  if (!workout) return 'Sin registros'
  return 'Última: ' + formatFecha(workout.fecha)
}

function tendencia(nombre: string): string {
  const puntos = mockProgressByExercise[nombre]
  if (!puntos || puntos.length < 2) return ''
  const diff = puntos[puntos.length - 1].valor - puntos[puntos.length - 2].valor
  if (diff > 0) return `↑ +${diff}`
  if (diff < 0) return `↓ ${diff}`
  return '→ igual'
}

function formatFecha(fecha: string): string {
  const d = new Date(fecha + 'T00:00:00')
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

function esPR(workout: Workout): boolean {
  const mismo = mockDashboard.workouts_recientes.filter(w => w.ejercicio === workout.ejercicio)
  const maxReps = Math.max(...mismo.map(w => Math.max(...w.series.map(s => s.reps))))
  const repsEste = Math.max(...workout.series.map(s => s.reps))
  return repsEste === maxReps && mismo[0]?.id === workout.id
}

// Chart
const chartData = computed(() => ({
  labels: progresion.value.map(p => p.semana),
  datasets: [{
    data: progresion.value.map(p => p.valor),
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
    x: {
      grid: { color: '#1e222d' },
      ticks: { color: '#4e5566', font: { size: 11 } },
    },
    y: {
      grid: { color: '#1e222d' },
      ticks: { color: '#4e5566', font: { size: 11 } },
    },
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

.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px;
  margin-bottom: 16px;
}
.card:last-child { margin-bottom: 0; }
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

/* Lista ejercicios */
.ejercicio-list { display: flex; flex-direction: column; gap: 8px; }
.ejercicio-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 12px 14px;
  cursor: pointer;
  transition: all 0.15s;
}
.ejercicio-item:hover { border-color: var(--border2); }
.ejercicio-item.active {
  border-color: var(--accent);
  background: rgba(61,142,248,0.05);
}
.ex-icon {
  width: 36px; height: 36px;
  background: var(--bg3);
  border-radius: 8px;
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
.kpi-label {
  font-size: 10px;
  color: var(--text3);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.kpi-val {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}
.kpi-sub { font-size: 10px; color: var(--text2); margin-top: 2px; }

/* Progress bar */
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
}
.progress-labels {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  color: var(--text3);
  margin-top: 4px;
}

/* Chart */
.chart-wrap { height: 200px; }

/* Historial */
.hist-item {
  padding: 12px 0;
  border-bottom: 1px solid var(--border);
}
.hist-item:last-child { border-bottom: none; padding-bottom: 0; }
.hist-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.hist-fecha { font-size: 13px; font-weight: 500; color: var(--text); }
.hist-series { display: flex; gap: 6px; flex-wrap: wrap; }
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
.empty-msg {
  font-size: 13px;
  color: var(--text3);
  text-align: center;
  padding: 20px 0;
}

@media (max-width: 900px) {
  .progress-grid { grid-template-columns: 1fr; }
  .kpi-row { grid-template-columns: repeat(2, 1fr); }
}
</style>