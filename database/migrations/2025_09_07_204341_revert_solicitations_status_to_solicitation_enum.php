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
        // Converter os valores de status de volta para SolicitationStatusEnum
        DB::table('solicitations')->update(['status' => 'pending']);
        
        // Atualizar a coluna para usar os valores corretos do SolicitationStatusEnum
        Schema::table('solicitations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Voltar para o enum anterior
        Schema::table('solicitations', function (Blueprint $table) {
            $table->enum('status', ['completo', 'incompleto', 'ausente', 'pendente'])
                  ->default('pendente')
                  ->change();
        });
    }
};
