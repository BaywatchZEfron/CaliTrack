import { ref, computed } from 'vue'
import { mockDashboard, mockBodyWeight, mockProgressByExercise } from '@/data/mockData'
import type { DashboardData, BodyWeightEntry } from '@/types'

export function useDashboard() {
  const data = ref<DashboardData>(mockDashboard)
  const bodyWeight = ref<BodyWeightEntry[]>(mockBodyWeight)
  const ejercicioActivo = ref('Dominadas / Pull-ups')
  const isLoading = ref(false)

  // Progresión del ejercicio activo
  const progresionActiva = computed(() => {
    return mockProgressByExercise[ejercicioActivo.value] ?? []
  })

  // Último valor de progresión
  const valorActual = computed(() => {
    const puntos = progresionActiva.value
    return puntos[puntos.length - 1]?.valor ?? 0
  })

  // Diferencia con 4 semanas atrás
  const mejora4semanas = computed(() => {
    const puntos = progresionActiva.value
    if (puntos.length < 5) return 0
    return puntos[puntos.length - 1].valor - puntos[puntos.length - 5].valor
  })

  // Volumen del último entrenamiento
  const volumenUltimoEntrenamiento = computed(() => {
    const ultimo = data.value.workouts_recientes[0]
    if (!ultimo) return 0
    return ultimo.series.reduce((acc, s) => acc + s.reps * (s.carga_kg || 1), 0)
  })

  function cambiarEjercicio(nombre: string) {
    ejercicioActivo.value = nombre
  }

  return {
    data,
    bodyWeight,
    ejercicioActivo,
    progresionActiva,
    valorActual,
    mejora4semanas,
    volumenUltimoEntrenamiento,
    isLoading,
    cambiarEjercicio,
  }
}