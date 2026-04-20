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
              :class="{ active: activeExercise.id === ex.id }"
              @click="changeExercise(ex)"
            >
              <div class="ex-icon">{{ icons[ex.name] ?? '💪' }}</div>

              <div class="ex-info">
                <div class="ex-nombre">{{ ex.name }}</div>
                <div class="ex-sub">
                  {{ lastSession(ex.name) }}
                </div>
              </div>

              <div class="ex-right">
                <div class="ex-val">
                  {{ lastValue(ex.name) }}
                  <span class="ex-unit">reps</span>
                </div>
                <div class="ex-trend">
                  {{ trend(ex.name) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detalle -->
        <div class="detalle-col">

          <!-- KPIs -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">{{ activeExercise.name }}</div>
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
                <div class="kpi-val" style="color: var(--accent)">
                  {{ goalPercentage }}%
                </div>
                <div class="kpi-sub">al objetivo</div>
              </div>

              <div class="kpi-item">
                <div class="kpi-label">Mejora total</div>
                <div class="kpi-val" style="color: var(--green)">
                  +{{ improvement }}
                </div>
                <div class="kpi-sub">desde inicio</div>
              </div>
            </div>

            <div class="progress-track" style="margin-top: 14px">
              <div
                class="progress-fill"
                :style="{ width: goalPercentage + '%' }"
              />
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
                {{ progression.length }} sesiones
              </span>
            </div>

            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- Historial -->
          <div class="card">
            <div class="card-title" style="margin-bottom: 14px">Historial</div>

            <div v-if="exerciseWorkouts.length > 0">
              <div
                v-for="workout in exerciseWorkouts"
                :key="workout.id"
                class="hist-item"
              >
                <div class="hist-header">
                  <span class="hist-fecha">
                    {{ formatDate(workout.date) }}
                  </span>
                  <span v-if="isPR(workout)" class="pr-badge">PR</span>
                </div>

                <div class="hist-series">
                  <span
                    v-for="s in workout.workout_exercises[0]?.sets"
                    :key="s.set_number"
                    class="hist-badge"
                  >
                    S{{ s.set_number }}: {{ s.reps }}r · RPE {{ s.rpe }}
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
import { mockExercises, mockProgressByExercise } from '@/data/mockData'
import type { Workout } from '@/types'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  activeExercise,
  progression,
  currentValue,
  startValue,
  improvement,
  goal,
  goalPercentage,
  exerciseWorkouts,
  changeExercise,
} = useProgress()

const icons: Record<string, string> = {
  'Pull-up': '🧗',
  'Dip': '🏋️',
  'Handstand Push-up': '🤸',
  'Muscle-up': '💪',
  'Pistol Squat': '🦵',
}

function lastValue(name: string): number {
  const points = mockProgressByExercise[name]
  return points ? (points[points.length - 1]?.value ?? 0) : 0
}

function lastSession(name: string): string {
  const points = mockProgressByExercise[name]
  if (!points || points.length === 0) return 'Sin registros'
  return `${points.length} sesiones`
}

function trend(name: string): string {
  const points = mockProgressByExercise[name]
  if (!points || points.length < 2) return ''
  const last = points[points.length - 1]?.value ?? 0
  const prev = points[points.length - 2]?.value ?? 0
  const diff = last - prev
  if (diff > 0) return `↑ +${diff}`
  if (diff < 0) return `↓ ${diff}`
  return '→ igual'
}

function formatDate(dateStr: string): string {
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

function isPR(workout: Workout): boolean {
  const sameEx = exerciseWorkouts.value.filter(w =>
    w.workout_exercises.some(we => we.exercise_id === activeExercise.value.id)
  )
  const maxReps = Math.max(...sameEx.map(w =>
    Math.max(...w.workout_exercises.flatMap(we => we.sets.map(s => s.reps)))
  ))
  const theseReps = Math.max(...workout.workout_exercises.flatMap(we => we.sets.map(s => s.reps)))
  return theseReps === maxReps && sameEx[0]?.id === workout.id
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