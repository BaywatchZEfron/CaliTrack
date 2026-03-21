<template>
  <AppLayout>
    <div class="dashboard">

      <!-- KPI strip -->
      <div class="kpi-strip">
        <MetricCard
          label="Entrenamientos (mes)"
          :value="data.entrenamientos_mes"
          trend="↑ 2 vs mes anterior"
          trend-type="up"
        />
        <MetricCard
          label="Semanas activas"
          :value="data.semanas_activas"
          unit="/ 12"
          trend-type="neutral"
        />
        <MetricCard
          label="Score semanal"
          :value="data.score_semanal"
          unit="/ 100"
          trend="↑ 12 pts"
          trend-type="up"
        />
        <MetricCard
          label="Peso corporal"
          :value="data.peso_corporal"
          unit="kg"
          trend="↓ 0.5 esta semana"
          trend-type="down"
        />
      </div>

      <div class="dash-grid">
        <!-- Columna izquierda -->
        <div class="dash-left">

          <!-- Workout de hoy -->
          <div class="card workout-hero">
            <div class="hero-bg">
              <div class="hero-grid" />
              <div class="hero-label">
                <div class="hero-date">Hoy · Lunes 19 mar</div>
                <div class="hero-title">Push Day</div>
                <div class="hero-sub">Fondos · Handstand · Dips</div>
              </div>
            </div>
            <div class="hero-body">
              <div class="workout-stats">
                <div class="w-stat">
                  <div class="w-stat-val">3.240<span class="w-stat-unit"> kg</span></div>
                  <div class="w-stat-label">Volumen total</div>
                  <div class="w-stat-trend">↑ 8%</div>
                </div>
                <div class="w-stat">
                  <div class="w-stat-val">27<span class="w-stat-unit"> reps</span></div>
                  <div class="w-stat-label">Total reps</div>
                  <div class="w-stat-trend">9 series</div>
                </div>
                <div class="w-stat">
                  <div class="w-stat-val">7.8<span class="w-stat-unit"> RPE</span></div>
                  <div class="w-stat-label">Intensidad media</div>
                  <div class="w-stat-trend" style="color: var(--green)">Óptimo</div>
                </div>
              </div>
              <RouterLink to="/progress" class="report-btn">
                Ver informe completo →
              </RouterLink>
            </div>
          </div>

          <!-- Gráfica de progresión -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Progresión</div>
              <div class="exercise-pills">
                <button
                  v-for="ex in ejercicios"
                  :key="ex"
                  class="ex-pill"
                  :class="{ active: ejercicioActivo === ex }"
                  @click="cambiarEjercicio(ex)"
                >
                  {{ ex.split(' /')[0] }}
                </button>
              </div>
            </div>
            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- Peso corporal -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Peso corporal</div>
              <span class="card-action">+ Añadir →</span>
            </div>
            <div class="bw-row">
              <div class="bw-current">
                {{ data.peso_corporal }}<span class="bw-unit"> kg</span>
              </div>
              <div class="bw-meta">
                <div class="bw-goal-row">
                  <span class="bw-goal-item">Objetivo: <strong>65 kg</strong></span>
                  <span class="bw-goal-item" style="color: var(--accent)">Progreso: <strong>40%</strong></span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill" style="width: 40%" />
                </div>
                <div class="progress-labels"><span>70.5</span><span>65 kg</span></div>
              </div>
            </div>
            <div class="chart-wrap-sm">
              <Line :data="bodyWeightChartData" :options="bodyWeightOptions" />
            </div>
          </div>

        </div>

        <!-- Columna derecha -->
        <div class="dash-right">

          <!-- Plan de hoy -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Plan de hoy</div>
              <span style="font-size: 11px; color: var(--text2)">Lun 19</span>
            </div>
            <div class="plan-list">
              <div class="plan-item">
                <div class="plan-dot" style="background: var(--accent)" />
                <div>
                  <div class="plan-title">Push Day</div>
                  <div class="plan-sub">18:00 · 45 min estimados</div>
                </div>
                <RouterLink to="/log" class="plan-btn">Iniciar</RouterLink>
              </div>
              <div class="plan-item">
                <div class="plan-dot" style="background: var(--green)" />
                <div>
                  <div class="plan-title">Movilidad</div>
                  <div class="plan-sub">20:00 · 20 min</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Smart Insights -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Smart Insights</div>
            </div>
            <InsightItem
              v-for="(insight, i) in data.insights"
              :key="i"
              :tipo="insight.tipo"
              :texto="insight.texto"
            />
          </div>

          <!-- Review semanal -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Review semanal</div>
              <span class="card-action">Ver completo →</span>
            </div>
            <div class="score-row">
              <div class="weekly-score">{{ data.score_semanal }}</div>
              <div>
                <div style="font-size: 12px; color: var(--green)">↑ 12 pts vs semana anterior</div>
                <div style="font-size: 11px; color: var(--text2)">Score esta semana</div>
              </div>
            </div>
            <div class="weekly-rows">
              <div class="weekly-row">
                <span>Rendimiento</span>
                <span style="color: var(--green)">82% ↑12%</span>
              </div>
              <div class="weekly-row">
                <span>Consistencia</span>
                <span>76% ↓3%</span>
              </div>
              <div class="weekly-row">
                <span>Sueño medio</span>
                <span style="color: var(--amber)">{{ data.sleep_media }}h</span>
              </div>
              <div class="weekly-row">
                <span>Entrenamientos</span>
                <span>3 / 4 planificados</span>
              </div>
            </div>
          </div>

          <!-- Sueño -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Sueño esta semana</div>
              <span class="card-action">Añadir →</span>
            </div>
            <div style="margin-bottom: 12px;">
              <span class="bw-current" style="font-size: 22px">{{ data.sleep_media }}</span>
              <span class="bw-unit"> h/noche media</span>
              <span style="font-size: 12px; color: var(--amber); margin-left: 8px;">Por debajo del óptimo</span>
            </div>
            <div class="sleep-bars">
              <div class="sleep-bar-row">
                <span class="sleep-label">Sueño profundo</span>
                <div class="sleep-track"><div class="sleep-fill" style="width: 70%; background: var(--accent)" /></div>
                <span class="sleep-pct">70%</span>
              </div>
              <div class="sleep-bar-row">
                <span class="sleep-label">Despierto</span>
                <div class="sleep-track"><div class="sleep-fill" style="width: 12%; background: var(--red)" /></div>
                <span class="sleep-pct">12%</span>
              </div>
              <div class="sleep-bar-row">
                <span class="sleep-label">REM</span>
                <div class="sleep-track"><div class="sleep-fill" style="width: 18%; background: var(--green)" /></div>
                <span class="sleep-pct">18%</span>
              </div>
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
import MetricCard from '@/components/ui/MetricCard.vue'
import InsightItem from '@/components/ui/InsightItem.vue'
import { useDashboard } from '@/composables/useDashboard'
import { mockExercises } from '@/data/mockData'

// Registrar los módulos de Chart.js que usamos
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  data,
  bodyWeight,
  ejercicioActivo,
  progresionActiva,
  cambiarEjercicio,
} = useDashboard()

// Lista de ejercicios para las pills
const ejercicios = mockExercises.slice(0, 3).map(e => e.nombre)

// Configuración de la gráfica de progresión
const chartData = computed(() => ({
  labels: progresionActiva.value.map(p => p.semana),
  datasets: [{
    data: progresionActiva.value.map(p => p.valor),
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

// Gráfica de peso corporal
const bodyWeightChartData = computed(() => ({
  labels: bodyWeight.value.map(b => b.fecha),
  datasets: [{
    data: bodyWeight.value.map(b => b.peso),
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
.dashboard { display: flex; flex-direction: column; gap: 20px; }

.kpi-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

.dash-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
.dash-left { display: flex; flex-direction: column; gap: 20px; }
.dash-right { display: flex; flex-direction: column; gap: 20px; }

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
  flex-wrap: wrap;
  gap: 8px;
}
.card-title {
  font-family: var(--font-head);
  font-size: 12px;
  font-weight: 600;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.card-action { font-size: 12px; color: var(--accent); cursor: pointer; }

/* Workout hero */
.workout-hero { padding: 0; overflow: hidden; }
.hero-bg {
  height: 150px;
  background: linear-gradient(135deg, #0d1f42 0%, #0a1628 50%, #111827 100%);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
  padding: 20px;
}
.hero-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(61,142,248,0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(61,142,248,0.06) 1px, transparent 1px);
  background-size: 32px 32px;
}
.hero-label { position: relative; z-index: 1; }
.hero-date { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 4px; }
.hero-title { font-family: var(--font-head); font-size: 22px; font-weight: 800; color: #fff; }
.hero-sub { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }

.hero-body { padding: 20px; }
.workout-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
.w-stat { background: var(--bg3); border-radius: 10px; padding: 12px; text-align: center; }
.w-stat-val { font-family: var(--font-head); font-size: 18px; font-weight: 700; color: var(--text); }
.w-stat-unit { font-size: 11px; color: var(--text3); }
.w-stat-label { font-size: 11px; color: var(--text2); margin-top: 2px; }
.w-stat-trend { font-size: 10px; color: var(--text3); margin-top: 2px; }
.report-btn {
  display: block;
  width: 100%;
  padding: 10px;
  text-align: center;
  background: transparent;
  border: 1px solid var(--border2);
  border-radius: 8px;
  color: var(--text2);
  text-decoration: none;
  font-size: 13px;
  transition: all 0.15s;
}
.report-btn:hover { background: var(--bg3); color: var(--text); border-color: var(--accent); }

/* Pills */
.exercise-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.ex-pill {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  cursor: pointer;
  border: 1px solid var(--border2);
  background: transparent;
  color: var(--text2);
  font-family: var(--font-body);
  transition: all 0.15s;
}
.ex-pill.active { background: var(--accent); border-color: var(--accent); color: #fff; }
.ex-pill:hover:not(.active) { border-color: var(--accent); color: var(--accent); }

/* Charts */
.chart-wrap { height: 180px; }
.chart-wrap-sm { height: 60px; margin-top: 12px; }

/* Body weight */
.bw-row { display: flex; align-items: flex-end; gap: 16px; }
.bw-current { font-family: var(--font-head); font-size: 32px; font-weight: 800; color: var(--text); line-height: 1; }
.bw-unit { font-size: 13px; color: var(--text2); }
.bw-meta { flex: 1; padding-bottom: 4px; }
.bw-goal-row { display: flex; gap: 16px; font-size: 12px; color: var(--text2); margin-bottom: 6px; }
.progress-track { height: 4px; background: var(--bg4); border-radius: 2px; overflow: hidden; }
.progress-fill { height: 100%; background: var(--accent); border-radius: 2px; }
.progress-labels { display: flex; justify-content: space-between; font-size: 10px; color: var(--text3); margin-top: 3px; }

/* Plan */
.plan-list { display: flex; flex-direction: column; gap: 8px; }
.plan-item {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--bg3);
  border-radius: 10px;
  padding: 10px 12px;
  border: 1px solid var(--border);
}
.plan-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.plan-title { font-size: 13px; color: var(--text); font-weight: 500; }
.plan-sub { font-size: 11px; color: var(--text2); }
.plan-btn {
  margin-left: auto;
  padding: 4px 10px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 11px;
  text-decoration: none;
  font-family: var(--font-body);
}

/* Weekly score */
.score-row { display: flex; align-items: center; gap: 14px; margin-bottom: 12px; }
.weekly-score { font-family: var(--font-head); font-size: 36px; font-weight: 800; color: var(--accent); line-height: 1; }
.weekly-rows { display: flex; flex-direction: column; }
.weekly-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  padding: 7px 0;
  border-bottom: 1px solid var(--border);
  color: var(--text2);
}
.weekly-row:last-child { border-bottom: none; }
.weekly-row span:last-child { color: var(--text); font-weight: 500; }

/* Sleep */
.sleep-bars { display: flex; flex-direction: column; gap: 10px; }
.sleep-bar-row { display: flex; align-items: center; gap: 10px; }
.sleep-label { font-size: 12px; color: var(--text2); width: 90px; flex-shrink: 0; }
.sleep-track { flex: 1; height: 6px; background: var(--bg4); border-radius: 3px; overflow: hidden; }
.sleep-fill { height: 100%; border-radius: 3px; }
.sleep-pct { font-size: 11px; color: var(--text2); width: 30px; text-align: right; }

/* Responsive */
@media (max-width: 1100px) {
  .dash-grid { grid-template-columns: 1fr; }
  .dash-right { display: grid; grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
  .kpi-strip { grid-template-columns: 1fr 1fr; }
  .dash-right { grid-template-columns: 1fr; }
}
</style>