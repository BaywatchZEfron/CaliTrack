<template>
  <AppLayout>

    <!-- Loading state -->
    <div v-if="isLoading" style="color: var(--text2); padding: 40px; text-align: center;">
      Cargando dashboard...
    </div>

    <div v-else-if="data" class="dashboard">

      <!-- KPI strip -->
      <div class="kpi-strip">
        <MetricCard
          label="Entrenamientos (mes)"
          :value="data.workouts_this_month"
          trend="↑ 2 vs mes anterior"
          trend-type="up"
        />
        <MetricCard
          label="Semanas activas"
          :value="data.active_weeks"
          unit="/ 12"
          trend-type="neutral"
        />
        <MetricCard
          label="Score semanal"
          :value="data.weekly_score"
          unit="/ 100"
          trend-type="neutral"
        />
        <MetricCard
          label="Peso corporal"
          :value="data.current_weight ?? '—'"
          unit="kg"
          trend-type="neutral"
        />
      </div>

      <div class="dash-grid">
        <!-- LEFT -->
        <div class="dash-left">

          <!-- Workout hero — hardcodeado de momento -->
          <div class="card workout-hero">
            <div class="hero-bg">
              <div class="hero-grid" />
              <div class="hero-label">
                <div class="hero-date">Hoy · {{ new Date().toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'short' }) }}</div>
                <div class="hero-title">{{ activeExercise ?? 'Sin actividad' }}</div>
                <div class="hero-sub">Ejercicio más frecuente este mes</div>
              </div>
            </div>

            <div class="hero-body">
              <div class="workout-stats">
                <div class="w-stat">
                  <div class="w-stat-val">{{ data.workouts_this_month }}<span class="w-stat-unit"> sesiones</span></div>
                  <div class="w-stat-label">Este mes</div>
                </div>
                <div class="w-stat">
                  <div class="w-stat-val">{{ data.active_weeks }}<span class="w-stat-unit"> sem</span></div>
                  <div class="w-stat-label">Semanas activas</div>
                </div>
                <div class="w-stat">
                  <div class="w-stat-val">{{ data.weekly_score }}<span class="w-stat-unit"> pts</span></div>
                  <div class="w-stat-label">Score semanal</div>
                </div>
              </div>

              <RouterLink to="/log" class="report-btn">
                Registrar entrenamiento →
              </RouterLink>
            </div>
          </div>

          <!-- PROGRESIÓN -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Progresión</div>
              <div class="exercise-pills">
                <button class="ex-pill active">
                  {{ activeExercise ?? '—' }}
                </button>
              </div>
            </div>
            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- PESO CORPORAL -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Peso corporal</div>
              <span class="card-action">+ Añadir →</span>
            </div>
            <div class="bw-row">
              <div class="bw-current">
                {{ data.current_weight ?? '—' }}<span class="bw-unit"> kg</span>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT -->
        <div class="dash-right">

          <!-- PLAN -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Acceso rápido</div>
            </div>
            <div class="plan-list">
              <div class="plan-item">
                <div class="plan-dot" style="background: var(--accent)" />
                <div>
                  <div class="plan-title">Nuevo entrenamiento</div>
                  <div class="plan-sub">Registra tu sesión de hoy</div>
                </div>
                <RouterLink to="/log" class="plan-btn">Iniciar</RouterLink>
              </div>
              <div class="plan-item">
                <div class="plan-dot" style="background: var(--green)" />
                <div>
                  <div class="plan-title">Ver progresión</div>
                  <div class="plan-sub">Analiza tu evolución</div>
                </div>
                <RouterLink to="/progress" class="plan-btn">Ver</RouterLink>
              </div>
            </div>
          </div>

          <!-- INSIGHTS -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Smart Insights</div>
            </div>
            <InsightItem
              v-for="(insight, i) in data.insights"
              :key="i"
              :tipo="insight.type"
              :texto="insight.text"
            />
          </div>

          <!-- REVIEW SEMANAL -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Review semanal</div>
            </div>
            <div class="score-row">
              <div class="weekly-score">{{ data.weekly_score }}</div>
              <div>
                <div style="font-size: 12px; color: var(--text2)">
                  Score esta semana
                </div>
              </div>
            </div>
            <div class="weekly-rows">
              <div class="weekly-row">
                <span>Entrenamientos este mes</span>
                <span>{{ data.workouts_this_month }}</span>
              </div>
              <div class="weekly-row">
                <span>Semanas activas</span>
                <span>{{ data.active_weeks }} / 12</span>
              </div>
              <div class="weekly-row">
                <span>Sueño medio</span>
                <span style="color: var(--amber)">
                  {{ data.sleep_avg ? data.sleep_avg + 'h' : '—' }}
                </span>
              </div>
            </div>
          </div>

          <!-- SUEÑO -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Sueño esta semana</div>
              <span class="card-action">Añadir →</span>
            </div>
            <div style="margin-bottom: 12px;">
              <span class="bw-current" style="font-size: 22px">
                {{ data.sleep_avg ?? '—' }}
              </span>
              <span class="bw-unit"> h/noche media</span>
              <span
                v-if="data.sleep_avg && data.sleep_avg < 7"
                style="font-size: 12px; color: var(--amber); margin-left: 8px;"
              >
                Por debajo del óptimo
              </span>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Sin datos -->
    <div v-else style="color: var(--text2); padding: 40px; text-align: center;">
      No se pudieron cargar los datos del dashboard.
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
import MetricCard from '@/components/ui/MetricCard.vue'
import InsightItem from '@/components/ui/InsightItem.vue'
import { useDashboard } from '@/composables/useDashboard'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  data,
  bodyWeight,
  activeExercise,
  activeProgression,
  isLoading,
} = useDashboard()

const chartData = computed(() => ({
  labels: activeProgression.value.map(p => p.week),
  datasets: [{
    data: activeProgression.value.map(p => p.value),
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
  plugins: { legend: { display: false }, tooltip: { enabled: true } },
  scales: {
    x: { grid: { color: '#1e222d' }, ticks: { color: '#4e5566', font: { size: 11 } } },
    y: { grid: { color: '#1e222d' }, ticks: { color: '#4e5566', font: { size: 11 } } },
  },
}

const bodyWeightChartData = computed(() => ({
  labels: bodyWeight.value.map(b =>
    new Date(b.date).toLocaleDateString('es-ES', { weekday: 'short' })
  ),
  datasets: [{
    data: bodyWeight.value.map(b => b.weight_kg),
    borderColor: '#3d8ef8',
    backgroundColor: 'transparent',
    borderWidth: 1.8,
    pointRadius: 3,
    pointBackgroundColor: '#3d8ef8',
    tension: 0.3,
  }],
}))

const bodyWeightOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#4e5566', font: { size: 10 } } },
    y: { grid: { color: '#1e222d' }, ticks: { color: '#4e5566', font: { size: 10 } } },
  },
}
</script>

<style scoped>
/* 🔒 STYLE ORIGINAL SIN TOCAR */
</style>