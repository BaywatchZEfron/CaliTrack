<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->enum('muscle_group', [
                'chest', 'back', 'shoulders',
                'arms', 'legs', 'core', 'full_body'
            ]);
            // Valores cerrados → no hay inconsistencias de texto

            $table->enum('category', ['push', 'pull', 'legs', 'core', 'full_body']);
            // Patrón de movimiento, distinto de muscle_group

            // load_type eliminado — pertenece a workout_exercises, no aquí

            $table->boolean('is_default')->default(true);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unique(['name', 'user_id']);
            // Evita duplicados. El edge case de null+null lo controlamos en el seeder.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
