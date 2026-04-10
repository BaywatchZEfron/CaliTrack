<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'notes',
        'duration_minutes',
    ];

    protected $casts = [
        'date' => 'date',
        // MySQL guarda la fecha como string "2025-01-15"
        // Con este cast Laravel la convierte a un objeto Carbon
        // Carbon te permite hacer cosas como $workout->date->format('d/m/Y')
        // o $workout->date->diffInDays(now())
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // Un workout pertenece a un usuario
    }

    public function workoutExercises(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class);
        // Un workout tiene muchos ejercicios (a través de la tabla intermedia)
    }
}