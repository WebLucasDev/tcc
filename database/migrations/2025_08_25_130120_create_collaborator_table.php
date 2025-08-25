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
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cpf', 14)->unique();
            $table->date('admission_date');
            $table->string('phone', 15)->nullable();
            $table->string('zip_code', 9)->nullable();
            $table->string('street')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('number')->nullable();
            $table->time('entry_time_1');
            $table->time('entry_time_2')->nullable();
            $table->time('return_time_1')->nullable();
            $table->time('return_time_2')->nullable();
            $table->foreignId('department_id')->nullable()->references('id')->on('departments')->onDelete('set null');
            $table->foreignId('position_id')->nullable()->references('id')->on('positions')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
