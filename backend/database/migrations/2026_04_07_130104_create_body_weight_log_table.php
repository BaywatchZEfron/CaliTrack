<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('body_weight_log', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();
        
        $table->date('date');
        
        $table->smallInteger('weight_kg')->unsigned();
        // Consistente con tu decisión en users — sin decimales
        
        $table->text('notes')->nullable();
        
        $table->timestamps();
        
        $table->unique(['user_id', 'date']);
        // Clave única compuesta: un usuario solo puede tener
        // un registro de peso por día. MySQL lo refuerza a nivel de BD.
    });
}

public function down(): void
{
    Schema::dropIfExists('body_weight_log');
}
};
