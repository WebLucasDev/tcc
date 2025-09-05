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
            if (!Schema::hasColumn('solicitations', 'status')) {
                $table->string('status')->default('pending')->after('id');
            }
            if (!Schema::hasColumn('solicitations', 'old_time_start')) {
                $table->timestamp('old_time_start')->nullable()->after('status');
            }
            if (!Schema::hasColumn('solicitations', 'old_time_finish')) {
                $table->timestamp('old_time_finish')->nullable()->after('old_time_start');
            }
            if (!Schema::hasColumn('solicitations', 'new_time_start')) {
                $table->timestamp('new_time_start')->nullable()->after('old_time_finish');
            }
            if (!Schema::hasColumn('solicitations', 'new_time_finish')) {
                $table->timestamp('new_time_finish')->nullable()->after('new_time_start');
            }
            if (!Schema::hasColumn('solicitations', 'reason')) {
                $table->text('reason')->nullable()->after('new_time_finish');
            }
            if (!Schema::hasColumn('solicitations', 'admin_comment')) {
                $table->text('admin_comment')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('solicitations', 'time_tracking_id')) {
                $table->unsignedBigInteger('time_tracking_id')->after('admin_comment');
                $table->foreign('time_tracking_id')->references('id')->on('time_tracking')->onDelete('cascade');
            }
            if (!Schema::hasColumn('solicitations', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('time_tracking_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            $table->dropForeign(['time_tracking_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'status',
                'old_time_start',
                'old_time_finish',
                'new_time_start',
                'new_time_finish',
                'reason',
                'admin_comment',
                'time_tracking_id',
                'user_id'
            ]);
        });
    }
};
