import type { DashboardData, Workout, BodyWeightEntry, Exercise } from '@/types'

// ─── USER ────────────────────────────────────────────────────────────────────

export const mockUser = {
  id: 1,
  name: 'Javier',
  email: 'javier@calitrack.com',
  age: 22,
  height_cm: 178,
  weight_kg: 68,
  level: 'intermediate' as const,
  goal: 'strength' as const,
  created_at: '2026-01-01T00:00:00.000000Z',
  updated_at: '2026-01-01T00:00:00.000000Z',
}

// ─── EXERCISES ───────────────────────────────────────────────────────────────

export const mockExercises: Exercise[] = [
  {
    id: 1, name: 'Pull-up',
    muscle_group: 'back', category: 'pull',
    load_type: 'bodyweight', is_default: true, user_id: null,
  },
  {
    id: 2, name: 'Dip',
    muscle_group: 'chest', category: 'push',
    load_type: 'bodyweight', is_default: true, user_id: null,
  },
  {
    id: 3, name: 'Handstand Push-up',
    muscle_group: 'shoulders', category: 'push',
    load_type: 'bodyweight', is_default: true, user_id: null,
  },
  {
    id: 4, name: 'Muscle-up',
    muscle_group: 'back', category: 'pull',
    load_type: 'bodyweight', is_default: true, user_id: null,
  },
  {
    id: 5, name: 'Pistol Squat',
    muscle_group: 'legs', category: 'legs',
    load_type: 'bodyweight', is_default: true, user_id: null,
  },
]

// ─── WORKOUTS ────────────────────────────────────────────────────────────────

export const mockWorkouts: Workout[] = [
  {
    id: 1,
    user_id: 1,
    date: '2026-03-17T00:00:00.000000Z',
    notes: null,
    duration_minutes: 45,
    created_at: '2026-03-17T10:00:00.000000Z',
    updated_at: '2026-03-17T10:00:00.000000Z',
    workout_exercises: [
      {
        id: 1, workout_id: 1, exercise_id: 1,
        order_index: 1, load_type: 'bodyweight',
        rest_time: 90, intensity_target: null, notes: null,
        exercise: {
          id: 1, name: 'Pull-up', muscle_group: 'back',
          category: 'pull', load_type: 'bodyweight',
          is_default: true, user_id: null,
        },
        sets: [
          { id: 1, workout_exercise_id: 1, set_number: 1, reps: 12, weight_kg: 0, is_assistance: false, rpe: 7 },
          { id: 2, workout_exercise_id: 1, set_number: 2, reps: 10, weight_kg: 0, is_assistance: false, rpe: 8 },
          { id: 3, workout_exercise_id: 1, set_number: 3, reps: 9,  weight_kg: 0, is_assistance: false, rpe: 9 },
        ],
      },
    ],
  },
  {
    id: 2,
    user_id: 1,
    date: '2026-03-15T00:00:00.000000Z',
    notes: null,
    duration_minutes: 40,
    created_at: '2026-03-15T10:00:00.000000Z',
    updated_at: '2026-03-15T10:00:00.000000Z',
    workout_exercises: [
      {
        id: 2, workout_id: 2, exercise_id: 2,
        order_index: 1, load_type: 'bodyweight',
        rest_time: 60, intensity_target: null, notes: null,
        exercise: {
          id: 2, name: 'Dip', muscle_group: 'chest',
          category: 'push', load_type: 'bodyweight',
          is_default: true, user_id: null,
        },
        sets: [
          { id: 4, workout_exercise_id: 2, set_number: 1, reps: 15, weight_kg: 0, is_assistance: false, rpe: 7 },
          { id: 5, workout_exercise_id: 2, set_number: 2, reps: 13, weight_kg: 0, is_assistance: false, rpe: 8 },
          { id: 6, workout_exercise_id: 2, set_number: 3, reps: 11, weight_kg: 0, is_assistance: false, rpe: 8 },
        ],
      },
    ],
  },
]

// ─── BODY WEIGHT ─────────────────────────────────────────────────────────────

export const mockBodyWeight: BodyWeightEntry[] = [
  { id: 1, user_id: 1, date: '2026-04-07T00:00:00.000000Z', weight_kg: 70.5, notes: null },
  { id: 2, user_id: 1, date: '2026-04-08T00:00:00.000000Z', weight_kg: 70.2, notes: null },
  { id: 3, user_id: 1, date: '2026-04-09T00:00:00.000000Z', weight_kg: 69.8, notes: null },
  { id: 4, user_id: 1, date: '2026-04-10T00:00:00.000000Z', weight_kg: 70.0, notes: null },
  { id: 5, user_id: 1, date: '2026-04-11T00:00:00.000000Z', weight_kg: 69.5, notes: null },
  { id: 6, user_id: 1, date: '2026-04-12T00:00:00.000000Z', weight_kg: 68.8, notes: null },
  { id: 7, user_id: 1, date: '2026-04-13T00:00:00.000000Z', weight_kg: 68.0, notes: null },
]

// ─── DASHBOARD ───────────────────────────────────────────────────────────────

export const mockDashboard: DashboardData = {
  workouts_this_month: 14,
  active_weeks: 11,
  weekly_score: 84,
  current_weight: 68.0,
  active_exercise: 'Pull-up',
  progression: [
    { week: 'W1', value: 7 },
    { week: 'W2', value: 8 },
    { week: 'W3', value: 8 },
    { week: 'W4', value: 9 },
    { week: 'W5', value: 10 },
    { week: 'W6', value: 10 },
    { week: 'W7', value: 11 },
    { week: 'now', value: 12 },
  ],
  recent_workouts: mockWorkouts,
  insights: [
    { type: 'success', text: 'Weekly volume +8% vs last week — sustained progression' },
    { type: 'warning', text: 'Average RPE this week: 8.2 — consider reducing intensity tomorrow' },
    { type: 'info',    text: 'Handstand Push-up: no session in 9 days — plan one soon' },
    { type: 'danger',  text: 'Sleep this week: 6.1h average — below optimal' },
  ],
  sleep_avg: 6.1,
}

// ─── PROGRESS ────────────────────────────────────────────────────────────────

export const mockProgressByExercise: Record<string, { week: string; value: number }[]> = {
  'Pull-up': [
    { week: 'W1', value: 7  }, { week: 'W2', value: 8  },
    { week: 'W3', value: 8  }, { week: 'W4', value: 9  },
    { week: 'W5', value: 10 }, { week: 'W6', value: 10 },
    { week: 'W7', value: 11 }, { week: 'now', value: 12 },
  ],
  'Dip': [
    { week: 'W1', value: 10 }, { week: 'W2', value: 11 },
    { week: 'W3', value: 12 }, { week: 'W4', value: 12 },
    { week: 'W5', value: 13 }, { week: 'W6', value: 14 },
    { week: 'W7', value: 14 }, { week: 'now', value: 15 },
  ],
  'Handstand Push-up': [
    { week: 'W1', value: 20 }, { week: 'W2', value: 25 },
    { week: 'W3', value: 28 }, { week: 'W4', value: 30 },
    { week: 'W5', value: 35 }, { week: 'W6', value: 38 },
    { week: 'W7', value: 40 }, { week: 'now', value: 45 },
  ],
}