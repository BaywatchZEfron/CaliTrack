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
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedTinyInteger('age')->nullable()->after('remember_token');
        $table->unsignedSmallInteger('height_cm')->nullable()->after('age');
        $table->unsignedSmallInteger('weight_kg')->nullable()->after('height_cm');
        $table->enum('level', ['beginner', 'intermediate', 'advanced'])
              ->nullable()
              ->after('weight_kg');
        $table->enum('goal', ['strength', 'endurance', 'weight_loss', 'skill'])
              ->nullable()
              ->after('level');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['age', 'height_cm', 'weight_kg', 'level', 'goal']);
    });
}
};
