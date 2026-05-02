import api from './api'
import type { Workout, Exercise } from '@/types'

export const workoutService = {

  async getExercises(): Promise<Exercise[]> {
    const response = await api.get('/exercises')
    return response.data
  },

  async getWorkouts(): Promise<Workout[]> {
    const response = await api.get('/workouts')
    return response.data
  },

  async createWorkout(payload: {
    date: string
    notes: string | null
    duration_minutes: number | null
    exercises: {
      exercise_id: number
      order_index: number
      load_type: string
      rest_time: number | null
      notes: string | null
      sets: {
        set_number: number
        reps: number
        weight_kg: number
        rpe: number | null
      }[]
    }[]
  }): Promise<Workout> {
    const response = await api.post('/workouts', payload)
    return response.data
  },

  async deleteWorkout(id: number): Promise<void> {
    await api.delete(`/workouts/${id}`)
  },
}
