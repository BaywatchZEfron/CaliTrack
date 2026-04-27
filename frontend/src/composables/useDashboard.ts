import { ref, computed, onMounted } from 'vue'
import { dashboardService } from '@/services/dashboardService'
import { workoutService } from '@/services/workoutService'
import type { DashboardData, BodyWeightEntry } from '@/types'

export function useDashboard() {
  const data = ref<DashboardData | null>(null)
  const bodyWeight = ref<BodyWeightEntry[]>([])
  const isLoading = ref(false)

  onMounted(async () => {
    isLoading.value = true
    try {
      data.value = await dashboardService.getDashboard()
    } finally {
      isLoading.value = false
    }
  })

  // Ejercicio activo — viene directamente del dashboard
  const activeExercise = computed(() => data.value?.active_exercise ?? null)

  // Progresión del ejercicio activo
  const activeProgression = computed(() => data.value?.progression ?? [])

  // Último valor de progresión
  const currentValue = computed(() => {
    const points = activeProgression.value
    return points[points.length - 1]?.value ?? 0
  })

  // Diferencia con 4 semanas atrás
  const improvement4weeks = computed(() => {
    const points = activeProgression.value
    if (points.length < 2) return 0
    const last = points[points.length - 1]
    const prev = points[0]
    if (!last || !prev) return 0
    return last.value - prev.value
  })

  // Volumen del último entrenamiento
  const lastWorkoutVolume = computed(() => {
    const last = data.value?.recent_workouts[0]
    if (!last) return 0
    return last.workout_exercises.reduce((total, we) => {
      return total + we.sets.reduce((acc, s) => {
        const load = s.weight_kg > 0 ? s.weight_kg : 1
        return acc + s.reps * load
      }, 0)
    }, 0)
  })

  return {
    data,
    bodyWeight,
    activeExercise,
    activeProgression,
    currentValue,
    improvement4weeks,
    lastWorkoutVolume,
    isLoading,
  }
}