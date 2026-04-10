<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('sleep_log', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->date('date');

        $table->decimal('hours', 4, 1);
        // decimal(4,1) → hasta 999.9 horas, permite 7.5h, 8.0h, 6.5h
        // Aquí sí tiene sentido el decimal — medio hora importa para el sueño

        $table->tinyInteger('quality')->unsigned()->nullable();
        // Calidad subjetiva 1-5

        $table->timestamps();

        $table->unique(['user_id', 'date']);
        // Un registro de sueño por día por usuario
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('sleep_log');
    }
};
