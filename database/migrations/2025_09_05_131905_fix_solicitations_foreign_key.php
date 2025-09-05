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
            // Como a constraint pode ter nomes diferentes, vamos usar DB raw
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        });

        // Primeiro tentar remover constraint antiga
        try {
            DB::statement('ALTER TABLE solicitations DROP FOREIGN KEY solicitations_user_id_foreign');
        } catch (\Exception $e) {
            // Se não existir, ignorar
        }

        Schema::table('solicitations', function (Blueprint $table) {
            // Adicionar nova constraint apontando para collaborators
            $table->foreign('colaborator_id')->references('id')->on('collaborators')->onDelete('cascade');

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            // Remover constraint para collaborators
            $table->dropForeign(['colaborator_id']);

            // Restaurar constraint original apontando para users
            $table->foreign('colaborator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
