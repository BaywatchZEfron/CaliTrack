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
/* ── Layout general ── */
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ── KPI strip ── */
.kpi-strip {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

/* ── Grid principal ── */
.dash-grid {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 20px;
  align-items: start;
}
.dash-left  { display: flex; flex-direction: column; gap: 20px; }
.dash-right { display: flex; flex-direction: column; gap: 20px; }

/* ── Cards ── */
.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 14px;
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
  font-size: 13px;
  font-weight: 600;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.card-action {
  font-size: 12px;
  color: var(--accent);
  cursor: pointer;
}
.card-action:hover { text-decoration: underline; }

/* ── Workout hero ── */
.workout-hero {
  overflow: hidden;
  padding: 0;
}
.hero-bg {
  height: 160px;
  background: linear-gradient(135deg, #0d1f42 0%, #0a1628 40%, #111827 100%);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
  padding: 20px;
}
.hero-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 60% 80% at 80% 50%, rgba(61,142,248,0.18) 0%, transparent 70%);
}
.hero-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(61,142,248,0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(61,142,248,0.06) 1px, transparent 1px);
  background-size: 32px 32px;
}
.hero-label { position: relative; z-index: 1; }
.hero-date  { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 4px; }
.hero-title {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.5px;
  line-height: 1.1;
}
.hero-sub { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }

.hero-body { padding: 20px; }
.workout-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}
.w-stat {
  background: var(--bg3);
  border-radius: 10px;
  padding: 12px;
  text-align: center;
}
.w-stat-val {
  font-family: var(--font-head);
  font-size: 18px;
  font-weight: 700;
  color: var(--text);
}
.w-stat-unit  { font-size: 10px; color: var(--text3); }
.w-stat-label { font-size: 11px; color: var(--text2); margin-top: 2px; }

.report-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 11px;
  background: transparent;
  border: 1px solid var(--border2);
  border-radius: 8px;
  color: var(--text2);
  font-size: 13px;
  text-decoration: none;
  transition: all 0.15s;
}
.report-btn:hover {
  background: var(--bg3);
  color: var(--text);
  border-color: var(--accent);
}

/* ── Gráfica ── */
.chart-wrap { position: relative; height: 180px; }

/* ── Exercise pills ── */
.exercise-pills { display: flex; gap: 8px; }
.ex-pill {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  cursor: pointer;
  border: 1px solid var(--border2);
  background: transparent;
  color: var(--text2);
  font-family: var(--font-body);
  transition: all 0.15s;
}
.ex-pill.active {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}

/* ── Peso corporal ── */
.bw-row { display: flex; align-items: baseline; gap: 6px; }
.bw-current {
  font-family: var(--font-head);
  font-size: 36px;
  font-weight: 800;
  color: var(--text);
  line-height: 1;
}
.bw-unit { font-size: 14px; color: var(--text2); }

/* ── Plan / acceso rápido ── */
.plan-list { display: flex; flex-direction: column; gap: 10px; }
.plan-item {
  background: var(--bg3);
  border-radius: 10px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1px solid var(--border);
}
.plan-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}
.plan-title { font-size: 13px; color: var(--text); font-weight: 500; }
.plan-sub   { font-size: 11px; color: var(--text2); margin-top: 2px; }
.plan-btn {
  margin-left: auto;
  padding: 4px 10px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 11px;
  cursor: pointer;
  text-decoration: none;
  font-family: var(--font-body);
  white-space: nowrap;
}

/* ── Review semanal ── */
.score-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 14px;
}
.weekly-score {
  font-family: var(--font-head);
  font-size: 26px;
  font-weight: 800;
  color: var(--accent);
}
.weekly-rows { display: flex; flex-direction: column; }
.weekly-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 7px 0;
  border-bottom: 1px solid var(--border);
  font-size: 12px;
  color: var(--text2);
}
.weekly-row:last-child { border-bottom: none; }
.weekly-row span:last-child { color: var(--text); font-weight: 500; }

/* ── Mobile ── */
@media (max-width: 900px) {
  .dash-grid { grid-template-columns: 1fr; }
  .kpi-strip { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .kpi-strip { grid-template-columns: 1fr 1fr; }
}
</style>