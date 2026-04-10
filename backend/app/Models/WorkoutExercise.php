<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutExercise extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'order_index',
        'load_type',
        'rest_time',
        'intensity_target',
        'notes',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
        // Este registro pertenece a un workout concreto
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
        // Y a un ejercicio concreto del catálogo
    }

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
        // Tiene muchas series ejecutadas
    }
}