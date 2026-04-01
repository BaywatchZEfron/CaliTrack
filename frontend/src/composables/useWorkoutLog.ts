import { ref, computed, reactive } from 'vue'
import { mockExercises, mockDashboard } from '@/data/mockData'
import type { WorkoutSet, Workout } from '@/types'

export function useWorkoutLog() {

  // Estado del formulario
  const fecha = ref(new Date().toISOString().split('T')[0])
  const pesoCorporal = ref<number>(68.0)
  const ejercicioSeleccionado = ref(mockExercises[0].nombre)
  const notas = ref('')

  // Series — empieza con 3 vacías
  const series = ref<WorkoutSet[]>([
    { serie: 1, reps: 0, carga_kg: 0, rpe: 0 },
    { serie: 2, reps: 0, carga_kg: 0, rpe: 0 },
    { serie: 3, reps: 0, carga_kg: 0, rpe: 0 },
  ])

  // Métricas calculadas en tiempo real
  const totalReps = computed(() =>
    series.value.reduce((acc, s) => acc + (s.reps || 0), 0)
  )

  const volumenTotal = computed(() =>
    series.value.reduce((acc, s) => {
      const carga = s.carga_kg > 0 ? s.carga_kg : pesoCorporal.value
      return acc + (s.reps || 0) * carga
    }, 0)
  )

  const rpeMedia = computed(() => {
    const seriesConRpe = series.value.filter(s => s.rpe > 0)
    if (seriesConRpe.length === 0) return 0
    const suma = seriesConRpe.reduce((acc, s) => acc + s.rpe, 0)
    return Math.round((suma / seriesConRpe.length) * 10) / 10
  })

  // Historial de entrenamientos guardados en esta sesión
  const historial = ref<Workout[]>(mockDashboard.workouts_recientes)

  // Acciones
  function añadirSerie() {
    series.value.push({
      serie: series.value.length + 1,
      reps: 0,
      carga_kg: 0,
      rpe: 0,
    })
  }

  function eliminarSerie(index: number) {
    if (series.value.length <= 1) return
    series.value.splice(index, 1)
    // Renumerar
    series.value.forEach((s, i) => { s.serie = i + 1 })
  }

  function guardarEntrenamiento() {
    const nuevo: Workout = {
      id: Date.now(),
      fecha: fecha.value,
      ejercicio: ejercicioSeleccionado.value,
      peso_corporal: pesoCorporal.value,
      series: [...series.value],
      notas: notas.value,
    }
    historial.value.unshift(nuevo)

    // Resetear formulario
    series.value = [
      { serie: 1, reps: 0, carga_kg: 0, rpe: 0 },
      { serie: 2, reps: 0, carga_kg: 0, rpe: 0 },
      { serie: 3, reps: 0, carga_kg: 0, rpe: 0 },
    ]
    notas.value = ''

    return nuevo
  }

  return {
    fecha,
    pesoCorporal,
    ejercicioSeleccionado,
    notas,
    series,
    totalReps,
    volumenTotal,
    rpeMedia,
    historial,
    añadirSerie,
    eliminarSerie,
    guardarEntrenamiento,
  }
}