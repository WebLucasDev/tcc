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
        Schema::table('solicitations', function (Blueprint $table) {
            // Desabilitar verificação de foreign keys temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Remover constraint existente
            $table->dropForeign(['colaborator_id']);

            // Renomear coluna
            $table->renameColumn('colaborator_id', 'collaborator_id');

            // Recriar constraint com nome correto
            $table->foreign('collaborator_id')->references('id')->on('collaborators')->onDelete('cascade');

            // Reabilitar verificação de foreign keys
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            // Desabilitar verificação de foreign keys temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Remover constraint
            $table->dropForeign(['collaborator_id']);

            // Renomear coluna de volta
            $table->renameColumn('collaborator_id', 'colaborator_id');

            // Recriar constraint com nome antigo
            $table->foreign('colaborator_id')->references('id')->on('collaborators')->onDelete('cascade');

            // Reabilitar verificação de foreign keys
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
};
