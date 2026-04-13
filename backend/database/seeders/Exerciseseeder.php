<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // PUSH
            ['name' => 'Push-up',          'muscle_group' => 'chest',     'category' => 'push', 'load_type' => 'bodyweight'],
            ['name' => 'Dip',              'muscle_group' => 'chest',     'category' => 'push', 'load_type' => 'bodyweight'],
            ['name' => 'Pike Push-up',     'muscle_group' => 'shoulders', 'category' => 'push', 'load_type' => 'bodyweight'],
            ['name' => 'Handstand Push-up','muscle_group' => 'shoulders', 'category' => 'push', 'load_type' => 'bodyweight'],
            ['name' => 'Diamond Push-up',  'muscle_group' => 'arms',      'category' => 'push', 'load_type' => 'bodyweight'],

            // PULL
            ['name' => 'Pull-up',          'muscle_group' => 'back',      'category' => 'pull', 'load_type' => 'bodyweight'],
            ['name' => 'Chin-up',          'muscle_group' => 'back',      'category' => 'pull', 'load_type' => 'bodyweight'],
            ['name' => 'Australian Row',   'muscle_group' => 'back',      'category' => 'pull', 'load_type' => 'bodyweight'],
            ['name' => 'Muscle-up',        'muscle_group' => 'back',      'category' => 'pull', 'load_type' => 'bodyweight'],
            ['name' => 'Face Pull',        'muscle_group' => 'shoulders', 'category' => 'pull', 'load_type' => 'bodyweight'],

            // LEGS
            ['name' => 'Squat',            'muscle_group' => 'legs',      'category' => 'legs', 'load_type' => 'bodyweight'],
            ['name' => 'Pistol Squat',     'muscle_group' => 'legs',      'category' => 'legs', 'load_type' => 'bodyweight'],
            ['name' => 'Lunge',            'muscle_group' => 'legs',      'category' => 'legs', 'load_type' => 'bodyweight'],
            ['name' => 'Glute Bridge',     'muscle_group' => 'legs',      'category' => 'legs', 'load_type' => 'bodyweight'],
            ['name' => 'Nordic Curl',      'muscle_group' => 'legs',      'category' => 'legs', 'load_type' => 'bodyweight'],

            // CORE
            ['name' => 'Plank',            'muscle_group' => 'core',      'category' => 'core', 'load_type' => 'bodyweight'],
            ['name' => 'Hollow Body Hold', 'muscle_group' => 'core',      'category' => 'core', 'load_type' => 'bodyweight'],
            ['name' => 'L-sit',            'muscle_group' => 'core',      'category' => 'core', 'load_type' => 'bodyweight'],
            ['name' => 'Dragon Flag',      'muscle_group' => 'core',      'category' => 'core', 'load_type' => 'bodyweight'],
            ['name' => 'Hanging Leg Raise','muscle_group' => 'core',      'category' => 'pull', 'load_type' => 'bodyweight'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create([
                'name'         => $exercise['name'],
                'muscle_group' => $exercise['muscle_group'],
                'category'     => $exercise['category'],
                'is_default'   => true,
                // true = ejercicio del sistema, visible para todos los usuarios
                'user_id'      => null,
                // null = no pertenece a ningún usuario concreto
            ]);
        }
    }
}