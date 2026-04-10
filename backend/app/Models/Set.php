<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Set extends Model
{
    protected $fillable = [
        'workout_exercise_id',
        'set_number',
        'reps',
        'weight_kg',
        'is_assistance',
        'rpe',
    ];

    protected $casts = [
        'is_assistance' => 'boolean',
        // Igual que is_default en Exercise
        // MySQL guarda 0/1, PHP necesita true/false
        'weight_kg' => 'decimal:2',
        // Mantiene los 2 decimales al serializar a JSON
        // Sin esto PHP puede devolver "10" en lugar de "10.00"
    ];

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
        // Una serie pertenece a un ejercicio dentro de un workout
    }
}