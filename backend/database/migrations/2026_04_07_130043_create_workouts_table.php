<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('workouts', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();
        // cascadeOnDelete() → si el usuario se borra, sus workouts también
        // Tiene sentido: los entrenamientos son datos personales del usuario
        
        $table->date('date');
        // Solo fecha, sin hora — un workout pertenece a un día concreto
        
        $table->text('notes')->nullable();
        // Notas opcionales sobre el entrenamiento completo
        
        $table->smallInteger('duration_minutes')->unsigned()->nullable();
        // smallInteger aguanta hasta 65535 minutos, más que suficiente
        // nullable porque no siempre se registra la duración
        
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
