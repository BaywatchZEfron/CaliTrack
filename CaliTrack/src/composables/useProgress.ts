import { ref, computed } from 'vue'
import { mockProgressByExercise, mockExercises, mockDashboard } from '@/data/mockData'

export function useProgress() {
  const ejercicioActivo = ref(mockExercises[0].nombre)

  const progresion = computed(() =>
    mockProgressByExercise[ejercicioActivo.value] ?? []
  )

  const valorActual = computed(() => {
    const puntos = progresion.value
    return puntos[puntos.length - 1]?.valor ?? 0
  })

  const valorInicio = computed(() => progresion.value[0]?.valor ?? 0)

  const mejora = computed(() => valorActual.value - valorInicio.value)

  const objetivo = computed(() => Math.ceil(valorActual.value * 1.25))

  const porcentajeObjetivo = computed(() => {
    const rango = objetivo.value - valorInicio.value
    const avance = valorActual.value - valorInicio.value
    if (rango <= 0) return 0
    return Math.round((avance / rango) * 100)
  })

  const workoutsDelEjercicio = computed(() =>
    mockDashboard.workouts_recientes.filter(
      w => w.ejercicio === ejercicioActivo.value
    )
  )

  function cambiarEjercicio(nombre: string) {
    ejercicioActivo.value = nombre
  }

  return {
    ejercicioActivo,
    progresion,
    valorActual,
    valorInicio,
    mejora,
    objetivo,
    porcentajeObjetivo,
    workoutsDelEjercicio,
    cambiarEjercicio,
  }
}
