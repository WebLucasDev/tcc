<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TimeTrackingActionEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_tracking', function (Blueprint $table) {
            // Coluna para armazenar a ação realizada no registro
            $table->enum('action', TimeTrackingActionEnum::getAll())
                  ->nullable()
                  ->after('return_time_2_observation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_tracking', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
};
