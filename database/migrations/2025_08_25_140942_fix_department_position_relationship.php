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
        // Remover position_id da tabela departments
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });

        // Adicionar department_id na tabela positions
        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter: remover department_id da tabela positions
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        // Reverter: adicionar position_id na tabela departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
        });
    }
};
