<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('workout_exercises', function (Blueprint $table) {
        $table->id();

        $table->foreignId('workout_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('exercise_id')
              ->constrained()
              ->restrictOnDelete();
        // restrictOnDelete() → si intentas borrar un ejercicio que tiene
        // historial en workouts, MySQL lanza un error y lo impide.
        // Así nunca se pierde historial por accidente.

        $table->tinyInteger('order_index')->unsigned();

        $table->enum('load_type', ['bodyweight', 'weighted', 'assisted'])
              ->default('bodyweight');

        $table->smallInteger('rest_time')->unsigned()->nullable();
        $table->tinyInteger('intensity_target')->unsigned()->nullable();
        $table->text('notes')->nullable();

        $table->timestamps();

        $table->unique(['workout_id', 'order_index']);
        // Unique compuesto: dentro de un mismo workout,
        // no pueden existir dos ejercicios en la misma posición.
        // workout_id=1, order_index=1 → OK
        // workout_id=1, order_index=1 → ❌ MySQL rechaza esto
        // workout_id=2, order_index=1 → OK (distinto workout)
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
