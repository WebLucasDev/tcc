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
        Schema::table('solicitations', function (Blueprint $table) {
            // Renomear coluna user_id para colaborator_id
            $table->renameColumn('user_id', 'colaborator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            // Reverter renomeação da coluna
            $table->renameColumn('colaborator_id', 'user_id');
        });
    }
};
