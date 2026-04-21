import { ref, computed, onMounted } from 'vue'
import { workoutService } from '@/services/workoutService'
import type { Exercise, Workout } from '@/types'

interface FormSet {
  set_number: number
  reps: number
  weight_kg: number
  rpe: number
}

export function useWorkoutLog() {
  const date = ref(new Date().toISOString().split('T')[0])
  const bodyWeight = ref<number>(70)
  const notes = ref('')
  const isLoading = ref(false)
  const isSubmitting = ref(false)

  // Ejercicios cargados desde la API
  const exercises = ref<Exercise[]>([])
  const selectedExercise = ref<Exercise | null>(null)

  // Series del formulario
  const sets = ref<FormSet[]>([
    { set_number: 1, reps: 0, weight_kg: 0, rpe: 0 },
    { set_number: 2, reps: 0, weight_kg: 0, rpe: 0 },
    { set_number: 3, reps: 0, weight_kg: 0, rpe: 0 },
  ])

  // Historial cargado desde la API
  const history = ref<Workout[]>([])

  // Carga inicial al montar el composable
  onMounted(async () => {
    isLoading.value = true
    try {
      const [exerciseList, workoutList] = await Promise.all([
        workoutService.getExercises(),
        workoutService.getWorkouts(),
      ])
      // Promise.all → hace las dos llamadas en paralelo, más rápido que secuencial

      exercises.value = exerciseList
      selectedExercise.value = exerciseList[0] ?? null
      history.value = workoutList
    } finally {
      isLoading.value = false
    }
  })

  // Métricas en tiempo real
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

  async function saveWorkout(): Promise<Workout | null> {
    if (!selectedExercise.value) return null

    isSubmitting.value = true
    try {
      const newWorkout = await workoutService.createWorkout({
        date: date.value ?? new Date().toISOString().split('T')[0],
        notes: notes.value || null,
        duration_minutes: null,
        exercises: [
          {
            exercise_id: selectedExercise.value.id,
            order_index: 1,
            load_type: selectedExercise.value.load_type,
            rest_time: null,
            notes: null,
            sets: sets.value.map(s => ({
              set_number: s.set_number,
              reps: s.reps,
              weight_kg: s.weight_kg,
              rpe: s.rpe || null,
            })),
          },
        ],
      })

      history.value.unshift(newWorkout)
      // unshift → añade al principio para que aparezca primero en el historial

      // Resetear formulario
      sets.value = [
        { set_number: 1, reps: 0, weight_kg: 0, rpe: 0 },
        { set_number: 2, reps: 0, weight_kg: 0, rpe: 0 },
        { set_number: 3, reps: 0, weight_kg: 0, rpe: 0 },
      ]
      notes.value = ''

      return newWorkout
    } finally {
      isSubmitting.value = false
    }
  }

  function isPR(workout: Workout): boolean {
    if (!selectedExercise.value) return false
    const sameEx = history.value.filter(w =>
      w.workout_exercises.some(we => we.exercise_id === selectedExercise.value!.id)
    )
    if (sameEx.length === 0) return false
    const maxReps = Math.max(...sameEx.map(w =>
      Math.max(...w.workout_exercises.flatMap(we => we.sets.map(s => s.reps)))
    ))
    const theseReps = Math.max(...workout.workout_exercises.flatMap(we => we.sets.map(s => s.reps)))
    return theseReps === maxReps
  }

  return {
    date,
    bodyWeight,
    selectedExercise,
    notes,
    sets,
    exercises,
    totalReps,
    totalVolume,
    avgRpe,
    history,
    isLoading,
    isSubmitting,
    addSet,
    removeSet,
    saveWorkout,
    isPR,
  }
}