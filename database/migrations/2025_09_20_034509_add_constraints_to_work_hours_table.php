<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, atualizar valores negativos ou null para 0
        DB::statement('UPDATE work_hours SET total_weekly_hours = 0 WHERE total_weekly_hours < 0 OR total_weekly_hours IS NULL');

        Schema::table('work_hours', function (Blueprint $table) {
            // Adicionar constraint para garantir que total_weekly_hours seja sempre positivo
            $table->decimal('total_weekly_hours', 5, 2)->unsigned()->change();
        });

        // Executar comando SQL direto para adicionar check constraint (MySQL 8.0+)
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE work_hours ADD CONSTRAINT chk_positive_weekly_hours CHECK (total_weekly_hours >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover check constraint se existir
        if (config('database.default') === 'mysql') {
            try {
                DB::statement('ALTER TABLE work_hours DROP CHECK chk_positive_weekly_hours');
            } catch (\Exception $e) {
                // Ignorar se a constraint não existir
            }
        }

        Schema::table('work_hours', function (Blueprint $table) {
            // Remover a constraint unsigned
            $table->decimal('total_weekly_hours', 5, 2)->change();
        });
    }
};
