import type { DashboardData, Workout, BodyWeightEntry, Exercise } from '@/types'

export const mockUser = {
  id: 1,
  nombre: 'Javier',
  nivel: 'Intermedio' as const,
  peso_actual: 68.0,
  altura: 178,
  objetivo: 'fuerza' as const,
}

export const mockExercises: Exercise[] = [
  { id: 1, nombre: 'Dominadas / Pull-ups', tipo: 'reps', unidad: 'reps' },
  { id: 2, nombre: 'Fondos / Dips', tipo: 'reps', unidad: 'reps' },
  { id: 3, nombre: 'Handstand', tipo: 'tiempo', unidad: 'seg' },
  { id: 4, nombre: 'Muscle-up', tipo: 'reps', unidad: 'reps' },
  { id: 5, nombre: 'Pistol squat', tipo: 'reps', unidad: 'reps' },
]

export const mockDashboard: DashboardData = {
  entrenamientos_mes: 14,
  semanas_activas: 11,
  score_semanal: 84,
  peso_corporal: 68.0,
  ejercicio_activo: 'Dominadas / Pull-ups',
  progresion: [
    { semana: 'S1', valor: 7 },
    { semana: 'S2', valor: 8 },
    { semana: 'S3', valor: 8 },
    { semana: 'S4', valor: 9 },
    { semana: 'S5', valor: 10 },
    { semana: 'S6', valor: 10 },
    { semana: 'S7', valor: 11 },
    { semana: 'hoy', valor: 12 },
  ],
  workouts_recientes: [
    {
      id: 1,
      fecha: '2025-03-17',
      ejercicio: 'Dominadas / Pull-ups',
      peso_corporal: 68.0,
      series: [
        { serie: 1, reps: 12, carga_kg: 0, rpe: 7 },
        { serie: 2, reps: 10, carga_kg: 0, rpe: 8 },
        { serie: 3, reps: 9, carga_kg: 0, rpe: 9 },
      ],
    },
    {
      id: 2,
      fecha: '2025-03-15',
      ejercicio: 'Fondos / Dips',
      peso_corporal: 68.2,
      series: [
        { serie: 1, reps: 15, carga_kg: 0, rpe: 7 },
        { serie: 2, reps: 13, carga_kg: 0, rpe: 8 },
        { serie: 3, reps: 11, carga_kg: 0, rpe: 8 },
      ],
    },
    {
      id: 3,
      fecha: '2025-03-11',
      ejercicio: 'Handstand',
      peso_corporal: 68.5,
      series: [
        { serie: 1, reps: 45, carga_kg: 0, rpe: 6 },
        { serie: 2, reps: 38, carga_kg: 0, rpe: 7 },
        { serie: 3, reps: 30, carga_kg: 0, rpe: 8 },
      ],
    },
  ],
  insights: [
    { tipo: 'success', texto: 'Volumen semanal +8% vs semana anterior — progresión sostenida' },
    { tipo: 'warning', texto: 'RPE medio esta semana: 8.2 — considera reducir intensidad mañana' },
    { tipo: 'info', texto: 'Handstand: sin registro en 9 días — planifica una sesión' },
    { tipo: 'danger', texto: 'Sueño esta semana: 6.1h media — por debajo del óptimo' },
  ],
  sleep_media: 6.1,
}

export const mockBodyWeight: BodyWeightEntry[] = [
  { fecha: 'L', peso: 70.5 },
  { fecha: 'M', peso: 70.2 },
  { fecha: 'X', peso: 69.8 },
  { fecha: 'J', peso: 70.0 },
  { fecha: 'V', peso: 69.5 },
  { fecha: 'S', peso: 68.8 },
  { fecha: 'D', peso: 68.0 },
]

export const mockProgressByExercise: Record<string, { semana: string; valor: number }[]> = {
  'Dominadas / Pull-ups': [
    { semana: 'S1', valor: 7 }, { semana: 'S2', valor: 8 },
    { semana: 'S3', valor: 8 }, { semana: 'S4', valor: 9 },
    { semana: 'S5', valor: 10 }, { semana: 'S6', valor: 10 },
    { semana: 'S7', valor: 11 }, { semana: 'hoy', valor: 12 },
  ],
  'Fondos / Dips': [
    { semana: 'S1', valor: 10 }, { semana: 'S2', valor: 11 },
    { semana: 'S3', valor: 12 }, { semana: 'S4', valor: 12 },
    { semana: 'S5', valor: 13 }, { semana: 'S6', valor: 14 },
    { semana: 'S7', valor: 14 }, { semana: 'hoy', valor: 15 },
  ],
  'Handstand': [
    { semana: 'S1', valor: 20 }, { semana: 'S2', valor: 25 },
    { semana: 'S3', valor: 28 }, { semana: 'S4', valor: 30 },
    { semana: 'S5', valor: 35 }, { semana: 'S6', valor: 38 },
    { semana: 'S7', valor: 40 }, { semana: 'hoy', valor: 45 },
  ],
}
