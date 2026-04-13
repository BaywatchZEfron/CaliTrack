<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $workouts = Workout::where('user_id', $request->user()->id)
            ->with(['workoutExercises.exercise', 'workoutExercises.sets'])
            ->orderBy('date', 'desc')
            ->get();
        // with() → carga las relaciones en la misma query (eager loading)
        // Sin esto Laravel haría una query por cada workout (N+1 problem)
        // workoutExercises.exercise → carga el ejercicio de cada workout_exercise
        // workoutExercises.sets     → carga las series de cada workout_exercise

        return response()->json($workouts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'                              => 'required|date',
            'notes'                             => 'nullable|string',
            'duration_minutes'                  => 'nullable|integer|min:1',
            'exercises'                         => 'required|array|min:1',
            'exercises.*.exercise_id'           => 'required|integer|exists:exercises,id',
            // exists:exercises,id → verifica que el exercise_id existe en la BD
            'exercises.*.order_index'           => 'required|integer|min:1',
            'exercises.*.load_type'             => 'required|in:bodyweight,weighted,assisted',
            'exercises.*.rest_time'             => 'nullable|integer|min:0',
            'exercises.*.notes'                 => 'nullable|string',
            'exercises.*.sets'                  => 'required|array|min:1',
            'exercises.*.sets.*.set_number'     => 'required|integer|min:1',
            'exercises.*.sets.*.reps'           => 'required|integer|min:1',
            'exercises.*.sets.*.weight_kg'      => 'nullable|numeric|min:0',
            'exercises.*.sets.*.rpe'            => 'nullable|integer|min:1|max:10',
        ]);

        // DB::transaction() → si algo falla a mitad, deshace TODO
        // Sin esto podrías crear el workout pero que fallen las series
        // y quedarte con datos a medias en la BD
        $workout = DB::transaction(function () use ($validated, $request) {

            $workout = Workout::create([
                'user_id'          => $request->user()->id,
                'date'             => $validated['date'],
                'notes'            => $validated['notes'] ?? null,
                'duration_minutes' => $validated['duration_minutes'] ?? null,
            ]);

            foreach ($validated['exercises'] as $exerciseData) {

                $workoutExercise = $workout->workoutExercises()->create([
                    'exercise_id'      => $exerciseData['exercise_id'],
                    'order_index'      => $exerciseData['order_index'],
                    'load_type'        => $exerciseData['load_type'],
                    'rest_time'        => $exerciseData['rest_time'] ?? null,
                    'notes'            => $exerciseData['notes'] ?? null,
                    'intensity_target' => null,
                ]);
                // $workout->workoutExercises()->create() → Eloquent asigna
                // workout_id automáticamente, no tienes que pasarlo tú

                foreach ($exerciseData['sets'] as $setData) {
                    $workoutExercise->sets()->create([
                        'set_number'   => $setData['set_number'],
                        'reps'         => $setData['reps'],
                        'weight_kg'    => $setData['weight_kg'] ?? 0,
                        'is_assistance'=> false,
                        'rpe'          => $setData['rpe'] ?? null,
                    ]);
                }
            }

            return $workout->load(['workoutExercises.exercise', 'workoutExercises.sets']);
            // load() → carga las relaciones en el objeto ya creado
            // Para devolver el workout completo en la respuesta
        });

        return response()->json($workout, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $workout = Workout::where('user_id', $request->user()->id)
            ->with(['workoutExercises.exercise', 'workoutExercises.sets'])
            ->findOrFail($id);
        // findOrFail() → si no existe o no es de este usuario, devuelve 404
        // where('user_id') → un usuario nunca puede ver workouts de otro

        return response()->json($workout);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $workout = Workout::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $workout->delete();
        // El cascade de la BD borra automáticamente workout_exercises y sets

        return response()->json(['message' => 'Entrenamiento eliminado correctamente.']);
    }
}  