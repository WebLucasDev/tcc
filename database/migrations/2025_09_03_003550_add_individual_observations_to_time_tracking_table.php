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
        Schema::table('time_tracking', function (Blueprint $table) {
            // Adicionar observações individuais para cada horário (máximo 30 caracteres)
            $table->string('entry_time_1_observation', 30)->nullable()->after('entry_time_1');
            $table->string('return_time_1_observation', 30)->nullable()->after('return_time_1');
            $table->string('entry_time_2_observation', 30)->nullable()->after('entry_time_2');
            $table->string('return_time_2_observation', 30)->nullable()->after('return_time_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'entry_time_1_observation',
                'return_time_1_observation',
                'entry_time_2_observation',
                'return_time_2_observation'
            ]);
        });
    }
};
