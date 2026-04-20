<template>
  <AppLayout>
    <div class="dashboard">

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
          trend="↑ 12 pts"
          trend-type="up"
        />
        <MetricCard
          label="Peso corporal"
          :value="data.current_weight ?? '—'"
          unit="kg"
          trend="↓ 0.5 esta semana"
          trend-type="down"
        />
      </div>

      <div class="dash-grid">
        <!-- LEFT -->
        <div class="dash-left">

          <!-- Workout hero (sin tocar, sigue hardcodeado) -->
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

          <!-- PROGRESIÓN -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Progresión</div>

              <div class="exercise-pills">
                <button
                  v-for="ex in exercises"
                  :key="ex"
                  class="ex-pill"
                  :class="{ active: activeExercise === ex }"
                  @click="changeExercise(ex)"
                >
                  {{ ex }}
                </button>
              </div>
            </div>

            <div class="chart-wrap">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- PESO -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Peso corporal</div>
              <span class="card-action">+ Añadir →</span>
            </div>

            <div class="bw-row">
              <div class="bw-current">
                {{ data.current_weight }}<span class="bw-unit"> kg</span>
              </div>

              <div class="bw-meta">
                <div class="bw-goal-row">
                  <span class="bw-goal-item">Objetivo: <strong>65 kg</strong></span>
                  <span class="bw-goal-item" style="color: var(--accent)">
                    Progreso: <strong>40%</strong>
                  </span>
                </div>

                <div class="progress-track">
                  <div class="progress-fill" style="width: 40%" />
                </div>

                <div class="progress-labels">
                  <span>70.5</span>
                  <span>65 kg</span>
                </div>
              </div>
            </div>

            <div class="chart-wrap-sm">
              <Line :data="bodyWeightChartData" :options="bodyWeightOptions" />
            </div>
          </div>

        </div>

        <!-- RIGHT -->
        <div class="dash-right">

          <!-- PLAN -->
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

          <!-- REVIEW -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Review semanal</div>
              <span class="card-action">Ver completo →</span>
            </div>

            <div class="score-row">
              <div class="weekly-score">{{ data.weekly_score }}</div>
              <div>
                <div style="font-size: 12px; color: var(--green)">
                  ↑ 12 pts vs semana anterior
                </div>
                <div style="font-size: 11px; color: var(--text2)">
                  Score esta semana
                </div>
              </div>
            </div>

            <div class="weekly-rows">
              <div class="weekly-row">
                <span>Sueño medio</span>
                <span style="color: var(--amber)">
                  {{ data.sleep_avg }}h
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
                {{ data.sleep_avg }}
              </span>
              <span class="bw-unit"> h/noche media</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
/* TU SCRIPT NUEVO TAL CUAL */
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

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip)

const {
  data,
  bodyWeight,
  activeExercise,
  activeProgression,
  changeExercise,
} = useDashboard()

const exercises = mockExercises.slice(0, 3).map(e => e.name)

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

const chartOptions = { responsive: true, maintainAspectRatio: false }

const bodyWeightChartData = computed(() => ({
  labels: bodyWeight.value.map(b => new Date(b.date).toLocaleDateString('es-ES', { weekday: 'short' })),
  datasets: [{
    data: bodyWeight.value.map(b => b.weight_kg),
    borderColor: '#3d8ef8',
  }],
}))

const bodyWeightOptions = { responsive: true, maintainAspectRatio: false }
</script>

<style scoped>
/* 🔒 STYLE ORIGINAL SIN TOCAR */
</style>