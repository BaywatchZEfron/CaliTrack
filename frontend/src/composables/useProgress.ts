import { ref, computed, onMounted } from 'vue'
import { progressService } from '@/services/progressService'
import type { ExerciseProgress } from '@/types'

export function useProgress() {
  const progressList = ref<ExerciseProgress[]>([])
  const activeIndex = ref(0)
  const isLoading = ref(false)

  onMounted(async () => {
    isLoading.value = true
    try {
      progressList.value = await progressService.getProgress()
    } finally {
      isLoading.value = false
    }
  })

  // Ejercicio activo — el seleccionado en la lista lateral
  const activeProgress = computed(() => progressList.value[activeIndex.value] ?? null)
  const activeExercise = computed(() => activeProgress.value?.exercise ?? null)

  const progression = computed(() => activeProgress.value?.points ?? [])

  const currentValue = computed(() => activeProgress.value?.current_max ?? 0)

  const goal = computed(() => activeProgress.value?.goal ?? 0)

  const startValue = computed(() => progression.value[0]?.value ?? 0)

  const improvement = computed(() => currentValue.value - startValue.value)

  const goalPercentage = computed(() => {
    const range = goal.value - startValue.value
    const progress = currentValue.value - startValue.value
    if (range <= 0) return 0
    return Math.min(100, Math.round((progress / range) * 100))
  })

  function changeExercise(index: number) {
    activeIndex.value = index
  }

  return {
    progressList,
    activeExercise,
    activeProgress,
    progression,
    currentValue,
    startValue,
    improvement,
    goal,
    goalPercentage,
    isLoading,
    changeExercise,
  }
}