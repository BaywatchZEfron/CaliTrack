<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\Set;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Borra el usuario demo si ya existe
        User::where('email', 'demo@calitrack.com')->delete();

        $user = User::create([
            'name'       => 'Alex Demo',
            'email'      => 'demo@calitrack.com',
            'password'   => Hash::make('demo1234'),
            'age'        => 24,
            'height_cm'  => 178,
            'weight_kg'  => 74,
            'level'      => 'intermediate',
            'goal'       => 'strength',
            'plan'       => 'premium',
        ]);

        // Ejercicios que vamos a usar
        $pullup  = Exercise::where('name', 'Pull-up')->first();
        $dip     = Exercise::where('name', 'Dip')->first();
        $pushup  = Exercise::where('name', 'Push-up')->first();
        $muscleup = Exercise::where('name', 'Muscle-up')->first();

        // Datos de progresión: semana → [reps por serie]
        // Pull-up: empieza en 6 reps, sube hasta 14 en 12 semanas
        $pullupProgression = [
            1  => [6, 5, 5],
            2  => [7, 6, 5],
            3  => [7, 6, 6],
            4  => [8, 7, 6],
            5  => [8, 8, 7],
            6  => [9, 8, 7],
            7  => [10, 9, 8],
            8  => [10, 10, 8],
            9  => [11, 10, 9],
            10 => [12, 11, 10],
            11 => [13, 12, 10],
            12 => [14, 13, 11],
        ];

        // Dip: empieza en 8, sube hasta 18
        $dipProgression = [
            1  => [8, 7, 6],
            2  => [9, 8, 7],
            3  => [10, 9, 8],
            4  => [11, 10, 8],
            5  => [12, 11, 9],
            6  => [12, 12, 10],
            7  => [13, 12, 11],
            8  => [14, 13, 11],
            9  => [15, 14, 12],
            10 => [16, 14, 13],
            11 => [17, 15, 13],
            12 => [18, 16, 14],
        ];

        // Push-up: aparece solo las últimas 6 semanas
        $pushupProgression = [
            7  => [20, 18, 15],
            8  => [22, 20, 16],
            9  => [24, 20, 18],
            10 => [25, 22, 20],
            11 => [26, 24, 20],
            12 => [28, 25, 22],
        ];

        // Muscle-up: aparece las últimas 4 semanas
        $muscleupProgression = [
            9  => [2, 1],
            10 => [3, 2],
            11 => [3, 3],
            12 => [4, 3],
        ];

        $now = Carbon::now();

        // Genera workouts para las últimas 12 semanas
        // 2-3 entrenamientos por semana
        for ($week = 1; $week <= 12; $week++) {
            // Fecha base de la semana (lunes de esa semana)
            $weekStart = $now->copy()->subWeeks(12 - $week);

            // Días de entrenamiento: lunes, miércoles, viernes (o similar)
            $trainingDays = [0, 2, 4]; // offsets dentro de la semana

            foreach ($trainingDays as $dayOffset) {
                // Semanas tempranas tienen menos días
                if ($week <= 4 && $dayOffset === 4) continue;

                $date = $weekStart->copy()->addDays($dayOffset);

                // Decide qué ejercicios van en este entrenamiento
                // Día 0 (lunes): pull-up + muscle-up
                // Día 2 (miércoles): dip + push-up
                // Día 4 (viernes): pull-up + dip
                $exercises = [];

                if ($dayOffset === 0) {
                    if ($pullup && isset($pullupProgression[$week])) {
                        $exercises[] = ['exercise' => $pullup, 'reps' => $pullupProgression[$week]];
                    }
                    if ($muscleup && isset($muscleupProgression[$week])) {
                        $exercises[] = ['exercise' => $muscleup, 'reps' => $muscleupProgression[$week]];
                    }
                } elseif ($dayOffset === 2) {
                    if ($dip && isset($dipProgression[$week])) {
                        $exercises[] = ['exercise' => $dip, 'reps' => $dipProgression[$week]];
                    }
                    if ($pushup && isset($pushupProgression[$week])) {
                        $exercises[] = ['exercise' => $pushup, 'reps' => $pushupProgression[$week]];
                    }
                } else {
                    if ($pullup && isset($pullupProgression[$week])) {
                        $exercises[] = ['exercise' => $pullup, 'reps' => $pullupProgression[$week]];
                    }
                    if ($dip && isset($dipProgression[$week])) {
                        $exercises[] = ['exercise' => $dip, 'reps' => $dipProgression[$week]];
                    }
                }

                if (empty($exercises)) continue;

                // Crea el workout
                $workout = Workout::create([
                    'user_id'          => $user->id,
                    'date'             => $date->toDateString(),
                    'notes'            => null,
                    'duration_minutes' => rand(30, 60),
                ]);

                foreach ($exercises as $order => $exData) {
                    $we = WorkoutExercise::create([
                        'workout_id'  => $workout->id,
                        'exercise_id' => $exData['exercise']->id,
                        'order_index' => $order + 1,
                        'load_type'   => 'bodyweight',
                        'rest_time'   => 90,
                        'notes'       => null,
                    ]);

                    foreach ($exData['reps'] as $i => $reps) {
                        Set::create([
                            'workout_exercise_id' => $we->id,
                            'set_number'          => $i + 1,
                            'reps'                => $reps,
                            'weight_kg'           => 0,
                            'is_assistance'       => false,
                            'rpe'                 => rand(7, 9),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Usuario demo creado: demo@calitrack.com / demo1234');
        $this->command->info('Workouts generados: 12 semanas de progresión');
    }
}