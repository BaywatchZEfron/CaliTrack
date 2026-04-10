<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
    Schema::create('sets', function (Blueprint $table) {
        $table->id();

        $table->foreignId('workout_exercise_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->tinyInteger('set_number')->unsigned();

        $table->tinyInteger('reps')->unsigned();

        $table->decimal('weight_kg', 5, 2)->default(0);
        // decimal(5,2) → permite 1.25, 2.50, 10.75
        // 0 = bodyweight puro

        $table->boolean('is_assistance')->default(false);
        // false → weight_kg es lastre añadido (weighted)
        // true  → weight_kg es asistencia de banda (se resta al peso corporal)
        // Ejemplo: is_assistance=true, weight_kg=10 → "banda de 10kg de ayuda"
        // Así no hay ambigüedad en los cálculos de analytics

        $table->tinyInteger('rpe')->unsigned()->nullable();

        $table->timestamps();

        $table->unique(['workout_exercise_id', 'set_number']);
        // Dentro del mismo ejercicio en ese workout,
        // no pueden existir dos series con el mismo número.
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
