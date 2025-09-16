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
            // Remover colunas de horários que não são mais necessárias
            $table->dropColumn([
                'entry_time_1',
                'entry_time_2',
                'return_time_1',
                'return_time_2'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            // Readicionar as colunas de horários caso seja necessário reverter
            $table->time('entry_time_1')->nullable()->after('work_hours_id');
            $table->time('entry_time_2')->nullable()->after('entry_time_1');
            $table->time('return_time_1')->nullable()->after('entry_time_2');
            $table->time('return_time_2')->nullable()->after('return_time_1');
        });
    }
};
