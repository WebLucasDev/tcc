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
        Schema::create('time_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborator_id')->constrained('collaborators')->onDelete('cascade');
            $table->date('date');
            $table->time('entry_time_1')->nullable(); // Entrada manhã
            $table->time('return_time_1')->nullable(); // Saída almoço
            $table->time('entry_time_2')->nullable(); // Volta almoço
            $table->time('return_time_2')->nullable(); // Saída tarde
            $table->text('observations')->nullable();
            $table->enum('status', ['completo', 'incompleto', 'ausente'])->default('incompleto');
            $table->integer('total_hours_worked')->nullable(); // Em minutos
            $table->timestamps();

            // Índices para otimização
            $table->index(['collaborator_id', 'date']);
            $table->unique(['collaborator_id', 'date']); // Um registro por colaborador por dia
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tracking');
    }
};
