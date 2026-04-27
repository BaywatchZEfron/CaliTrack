<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $now  = Carbon::now();

        // Ejercicios que el usuario ha usado alguna vez
        $exerciseIds = DB::table('workout_exercises')
            ->join('workouts', 'workouts.id', '=', 'workout_exercises.workout_id')
            ->where('workouts.user_id', $user->id)
            ->distinct()
            ->pluck('workout_exercises.exercise_id');
        // pluck() → devuelve solo esa columna como array [1, 5, 6...]
        // distinct() → sin duplicados si el usuario ha hecho el mismo ejercicio varias veces

        $result = [];

        foreach ($exerciseIds as $exerciseId) {
            $exercise = Exercise::find($exerciseId);
            if (!$exercise) continue;

            // Progresión semana a semana — máximo de reps por semana
            $points = DB::table('sets')
                ->join('workout_exercises', 'workout_exercises.id', '=', 'sets.workout_exercise_id')
                ->join('workouts', 'workouts.id', '=', 'workout_exercises.workout_id')
                ->where('workouts.user_id', $user->id)
                ->where('workout_exercises.exercise_id', $exerciseId)
                ->where('workouts.date', '>=', $now->copy()->subWeeks(12))
                ->select(
                    DB::raw('YEARWEEK(workouts.date, 1) as week_key'),
                    DB::raw('MAX(sets.reps) as max_reps')
                )
                ->groupBy('week_key')
                ->orderBy('week_key')
                ->get()
                ->map(function ($row, $index) use ($now) {
                    $weekKey = $row->week_key;
                    $currentWeekKey = (int) $now->format('oW');
                    // oW → año ISO + número de semana → ej: 202617
                    return [
                        'week'  => $weekKey == $currentWeekKey ? 'now' : 'W' . ($index + 1),
                        'value' => (int) $row->max_reps,
                    ];
                });

            // Máximo histórico de reps
            $currentMax = DB::table('sets')
                ->join('workout_exercises', 'workout_exercises.id', '=', 'sets.workout_exercise_id')
                ->join('workouts', 'workouts.id', '=', 'workout_exercises.workout_id')
                ->where('workouts.user_id', $user->id)
                ->where('workout_exercises.exercise_id', $exerciseId)
                ->max('sets.reps') ?? 0;

            $result[] = [
                'exercise'    => $exercise,
                'points'      => $points,
                'current_max' => (int) $currentMax,
                'goal'        => (int) ceil($currentMax * 1.25),
                // Objetivo: 25% más que el máximo actual
                // ceil() → redondea hacia arriba: 12.5 → 13
            ];
        }

        return response()->json($result);
    }
}