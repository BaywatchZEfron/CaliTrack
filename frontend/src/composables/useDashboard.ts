import { ref, computed } from 'vue'
import { mockDashboard, mockBodyWeight, mockProgressByExercise } from '@/data/mockData'
import type { DashboardData, BodyWeightEntry } from '@/types'

export function useDashboard() {
  const data = ref<DashboardData>(mockDashboard)
  const bodyWeight = ref<BodyWeightEntry[]>(mockBodyWeight)
  const activeExercise = ref('Pull-up')
  const isLoading = ref(false)

  // Progresión del ejercicio activo
  const activeProgression = computed(() => {
    return mockProgressByExercise[activeExercise.value] ?? []
  })

  // Último valor de progresión
  const currentValue = computed(() => {
    const points = activeProgression.value
    return points[points.length - 1]?.value ?? 0
  })

  // Diferencia con 4 semanas atrás
  const improvement4weeks = computed(() => {
    const points = activeProgression.value
    if (points.length < 5) return 0
    const last = points[points.length - 1]
    const prev = points[points.length - 5]
    if (!last || !prev) return 0
    return last.value - prev.value
})

  // Volumen del último entrenamiento
  // Un workout ahora tiene workout_exercises > sets en lugar de series planas
  const lastWorkoutVolume = computed(() => {
    const last = data.value.recent_workouts[0]
    if (!last) return 0
    return last.workout_exercises.reduce((total, we) => {
      return total + we.sets.reduce((acc, s) => {
        const load = s.weight_kg > 0 ? s.weight_kg : 1
        // Si no hay carga extra usamos 1 como multiplicador neutro
        // En el futuro aquí irá el peso corporal real del usuario
        return acc + s.reps * load
      }, 0)
    }, 0)
  })

  function changeExercise(name: string) {
    activeExercise.value = name
  }

  return {
    data,
    bodyWeight,
    activeExercise,
    activeProgression,
    currentValue,
    improvement4weeks,
    lastWorkoutVolume,
    isLoading,
    changeExercise,
  }
}