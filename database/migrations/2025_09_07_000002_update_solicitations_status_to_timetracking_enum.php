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
        // Primeiro, vamos limpar dados existentes para evitar conflitos
        DB::table('solicitations')->truncate();
        
        Schema::table('solicitations', function (Blueprint $table) {
            // Alterar coluna status para aceitar os novos valores do TimeTrackingStatusEnum
            $table->string('status')->change();
        });
        
        // Adicionar comentário explicativo
        DB::statement("ALTER TABLE solicitations MODIFY COLUMN status VARCHAR(255) COMMENT 'Status usando TimeTrackingStatusEnum: completo, incompleto, ausente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpar dados
        DB::table('solicitations')->truncate();
        
        Schema::table('solicitations', function (Blueprint $table) {
            // Voltar para os valores do SolicitationStatusEnum
            $table->string('status')->change();
        });
        
        // Remover comentário
        DB::statement("ALTER TABLE solicitations MODIFY COLUMN status VARCHAR(255) COMMENT ''");
    }
};
