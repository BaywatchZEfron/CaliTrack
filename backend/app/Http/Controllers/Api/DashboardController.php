<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $now  = Carbon::now();

        // ── 1. WORKOUTS ESTE MES ─────────────────────────────────────────────
        $workoutsThisMonth = $user->workouts()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->count();

        // ── 2. SEMANAS ACTIVAS (últimas 12 semanas) ──────────────────────────
        $activeWeeks = $user->workouts()
            ->where('date', '>=', $now->copy()->subWeeks(12))
            ->select(DB::raw('YEARWEEK(date, 1) as week'))
            // YEARWEEK(date, 1) → devuelve un número único por semana
            // Ejemplo: semana del 7 de abril = 202615
            ->distinct()
            ->count();

        // ── 3. EJERCICIO MÁS FRECUENTE (últimos 30 días) ────────────────────
        $activeExercise = DB::table('workout_exercises')
            ->join('workouts', 'workouts.id', '=', 'workout_exercises.workout_id')
            ->join('exercises', 'exercises.id', '=', 'workout_exercises.exercise_id')
            ->where('workouts.user_id', $user->id)
            ->where('workouts.date', '>=', $now->copy()->subDays(30))
            ->select('exercises.name', DB::raw('COUNT(*) as total'))
            ->groupBy('exercises.id', 'exercises.name')
            ->orderByDesc('total')
            ->first();
        // first() → devuelve solo el ejercicio más frecuente

        // ── 4. PROGRESIÓN (máximo reps por semana del ejercicio activo) ──────
        $progression = [];
        if ($activeExercise) {
            $progression = DB::table('sets')
                ->join('workout_exercises', 'workout_exercises.id', '=', 'sets.workout_exercise_id')
                ->join('workouts', 'workouts.id', '=', 'workout_exercises.workout_id')
                ->join('exercises', 'exercises.id', '=', 'workout_exercises.exercise_id')
                ->where('workouts.user_id', $user->id)
                ->where('exercises.name', $activeExercise->name)
                ->where('workouts.date', '>=', $now->copy()->subWeeks(8))
                ->select(
                    DB::raw('YEARWEEK(workouts.date, 1) as week_key'),
                    DB::raw('MIN(workouts.date) as week_start'),
                    DB::raw('MAX(sets.reps) as max_reps')
                )
                ->groupBy('week_key')
                ->orderBy('week_key')
                ->get()
                ->map(function ($row, $index) use ($now) {
                    $weekStart = Carbon::parse($row->week_start);
                    $isCurrentWeek = $weekStart->isSameWeek($now);
                    return [
                        'week'  => $isCurrentWeek ? 'now' : 'W' . ($index + 1),
                        'value' => (int) $row->max_reps,
                    ];
                });
        }

        // ── 5. SCORE SEMANAL ─────────────────────────────────────────────────
        $workoutsThisWeek = $user->workouts()
            ->whereBetween('date', [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ])
            ->count();
        $weeklyScore = min(100, (int) ($workoutsThisWeek / 4 * 100));
        // Objetivo: 4 entrenamientos por semana = 100 puntos
        // min(100) → el score no puede superar 100

        // ── 6. PESO CORPORAL ACTUAL ──────────────────────────────────────────
        $currentWeight = $user->bodyWeightLogs()
            ->orderByDesc('date')
            ->value('weight_kg');
        // value() → devuelve solo ese campo, sin cargar el modelo entero

        // ── 7. WORKOUTS RECIENTES ────────────────────────────────────────────
        $recentWorkouts = $user->workouts()
            ->with(['workoutExercises.exercise', 'workoutExercises.sets'])
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // ── 8. SUEÑO MEDIA 7 DÍAS ────────────────────────────────────────────
        $sleepAvg = $user->sleepLogs()
            ->where('date', '>=', $now->copy()->subDays(7))
            ->avg('hours');
        // avg() → media directa en SQL, null si no hay registros

        // ── 9. INSIGHTS ──────────────────────────────────────────────────────
        $insights = $this->generateInsights(
            $workoutsThisWeek,
            $sleepAvg,
            $activeExercise?->name
        );

        return response()->json([
            'workouts_this_month' => $workoutsThisMonth,
            'active_weeks'        => $activeWeeks,
            'weekly_score'        => $weeklyScore,
            'current_weight'      => $currentWeight,
            'active_exercise'     => $activeExercise?->name,
            'progression'         => $progression,
            'recent_workouts'     => $recentWorkouts,
            'insights'            => $insights,
            'sleep_avg'           => $sleepAvg ? round($sleepAvg, 1) : null,
        ]);
    }

    private function generateInsights(
        int $workoutsThisWeek,
        ?float $sleepAvg,
        ?string $activeExercise
    ): array {
        $insights = [];

        if ($workoutsThisWeek >= 3) {
            $insights[] = [
                'type' => 'success',
                'text' => "{$workoutsThisWeek} entrenamientos esta semana — buena consistencia",
            ];
        } elseif ($workoutsThisWeek === 0) {
            $insights[] = [
                'type' => 'warning',
                'text' => 'Sin entrenamientos esta semana — intenta retomar la rutina',
            ];
        }

        if ($sleepAvg !== null && $sleepAvg < 7) {
            $insights[] = [
                'type' => 'danger',
                'text' => "Sueño esta semana: {$sleepAvg}h media — por debajo del óptimo",
            ];
        }

        if ($activeExercise) {
            $insights[] = [
                'type' => 'info',
                'text' => "Ejercicio más frecuente: {$activeExercise}",
            ];
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'info',
                'text' => 'Registra tus primeros entrenamientos para ver insights personalizados',
            ];
        }

        return $insights;
    }
}