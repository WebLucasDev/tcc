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
        Schema::table('collaborators', function (Blueprint $table) {
            // Remove a foreign key constraint primeiro (se existir)
            $table->dropForeign(['department_id']);
            // Remove a coluna department_id
            $table->dropColumn('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            // Re-adiciona a coluna department_id
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
        });
    }
};
