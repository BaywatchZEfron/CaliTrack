import { ref, computed } from 'vue'
import { mockExercises, mockWorkouts } from '@/data/mockData'
import type { Exercise, Workout, WorkoutSet } from '@/types'

// Tipo local para el formulario — más simple que WorkoutSet completo
interface FormSet {
  set_number: number
  reps: number
  weight_kg: number
  rpe: number
}

export function useWorkoutLog() {
  const date = ref(new Date().toISOString().split('T')[0])
  const bodyWeight = ref<number>(68.0)
  const selectedExercise = ref<Exercise>(mockExercises[0]!)
  const notes = ref('')

  // Series del formulario — empieza con 3 vacías
  const sets = ref<FormSet[]>([
    { set_number: 1, reps: 0, weight_kg: 0, rpe: 0 },
    { set_number: 2, reps: 0, weight_kg: 0, rpe: 0 },
    { set_number: 3, reps: 0, weight_kg: 0, rpe: 0 },
  ])

  // Métricas calculadas en tiempo real
  const totalReps = computed(() =>
    sets.value.reduce((acc, s) => acc + (s.reps || 0), 0)
  )

  const totalVolume = computed(() =>
    sets.value.reduce((acc, s) => {
      const load = s.weight_kg > 0 ? s.weight_kg : bodyWeight.value
      return acc + (s.reps || 0) * load
    }, 0)
  )

  const avgRpe = computed(() => {
    const withRpe = sets.value.filter(s => s.rpe > 0)
    if (withRpe.length === 0) return 0
    const sum = withRpe.reduce((acc, s) => acc + s.rpe, 0)
    return Math.round((sum / withRpe.length) * 10) / 10
  })

  // Historial local — en el futuro vendrá de la API
  const history = ref<Workout[]>(mockWorkouts)

  function addSet() {
    sets.value.push({
      set_number: sets.value.length + 1,
      reps: 0,
      weight_kg: 0,
      rpe: 0,
    })
  }

  function removeSet(index: number) {
    if (sets.value.length <= 1) return
    sets.value.splice(index, 1)
    sets.value.forEach((s, i) => { s.set_number = i + 1 })
  }

  function saveWorkout() {
    // Construimos el workout con la estructura real de la API
    const newWorkout: Workout = {
      id: Date.now(),
      user_id: 1,
      date: new Date(date.value ?? new Date()).toISOString(),
      notes: notes.value || null,
      duration_minutes: null,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      workout_exercises: [
        {
          id: Date.now(),
          workout_id: Date.now(),
          exercise_id: selectedExercise.value.id,
          order_index: 1,
          load_type: selectedExercise.value.load_type,
          rest_time: null,
          intensity_target: null,
          notes: null,
          exercise: selectedExercise.value,
          sets: sets.value.map((s, i): WorkoutSet => ({
            id: i + 1,
            workout_exercise_id: Date.now(),
            set_number: s.set_number,
            reps: s.reps,
            weight_kg: s.weight_kg,
            is_assistance: false,
            rpe: s.rpe || null,
          })),
        },
      ],
    }

    history.value.unshift(newWorkout)

    // Resetear formulario
    sets.value = [
      { set_number: 1, reps: 0, weight_kg: 0, rpe: 0 },
      { set_number: 2, reps: 0, weight_kg: 0, rpe: 0 },
      { set_number: 3, reps: 0, weight_kg: 0, rpe: 0 },
    ]
    notes.value = ''

    return newWorkout
  }

  return {
    date,
    bodyWeight,
    selectedExercise,
    notes,
    sets,
    totalReps,
    totalVolume,
    avgRpe,
    history,
    addSet,
    removeSet,
    saveWorkout,
  }
}