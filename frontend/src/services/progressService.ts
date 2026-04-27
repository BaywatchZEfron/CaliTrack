import api from './api'
import type { ExerciseProgress } from '@/types'

export const progressService = {
  async getProgress(): Promise<ExerciseProgress[]> {
    const response = await api.get('/progress')
    return response.data
  },
}
