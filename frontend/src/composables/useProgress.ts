import { ref, computed } from 'vue'
import { mockProgressByExercise, mockExercises, mockWorkouts } from '@/data/mockData'
import type { Exercise } from '@/types'

export function useProgress() {
  const activeExercise = ref<Exercise>(mockExercises[0]!)

  const progression = computed(() =>
    mockProgressByExercise[activeExercise.value.name] ?? []
  )

  const currentValue = computed(() => {
    const points = progression.value
    return points[points.length - 1]?.value ?? 0
  })

  const startValue = computed(() => progression.value[0]?.value ?? 0)

  const improvement = computed(() => currentValue.value - startValue.value)

  const goal = computed(() => Math.ceil(currentValue.value * 1.25))

  const goalPercentage = computed(() => {
    const range = goal.value - startValue.value
    const progress = currentValue.value - startValue.value
    if (range <= 0) return 0
    return Math.round((progress / range) * 100)
  })

  // Workouts que contienen este ejercicio
  // Ahora buscamos dentro de workout_exercises en lugar de un campo plano
  const exerciseWorkouts = computed(() =>
    mockWorkouts.filter(w =>
      w.workout_exercises.some(we => we.exercise_id === activeExercise.value.id)
    )
  )

  function changeExercise(exercise: Exercise) {
    activeExercise.value = exercise
  }

  return {
    activeExercise,
    progression,
    currentValue,
    startValue,
    improvement,
    goal,
    goalPercentage,
    exerciseWorkouts,
    changeExercise,
  }
}