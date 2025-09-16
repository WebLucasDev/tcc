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
        Schema::table('work_hours', function (Blueprint $table) {
            // Remover a coluna is_active
            $table->dropColumn('is_active');

            // Adicionar a coluna status como enum
            $table->enum('status', ['ativo', 'inativo'])->default('ativo')->after('description');

            // Remover o índice antigo e adicionar o novo
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_hours', function (Blueprint $table) {
            // Remover a coluna status e o índice
            $table->dropIndex(['status']);
            $table->dropColumn('status');

            // Readicionar a coluna is_active
            $table->boolean('is_active')->default(true)->after('description');
            $table->index('is_active');
        });
    }
};
