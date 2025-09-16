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
        Schema::create('work_hours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome da jornada de trabalho');
            $table->decimal('total_weekly_hours', 5, 2)->default(0)->comment('Total de horas semanais calculado automaticamente');

            // Segunda-feira
            $table->boolean('monday_active')->default(false)->comment('Se trabalha na segunda-feira');
            $table->time('monday_entry_1')->nullable()->comment('Segunda - Entrada 1º turno');
            $table->time('monday_exit_1')->nullable()->comment('Segunda - Saída 1º turno');
            $table->time('monday_entry_2')->nullable()->comment('Segunda - Entrada 2º turno');
            $table->time('monday_exit_2')->nullable()->comment('Segunda - Saída 2º turno');

            // Terça-feira
            $table->boolean('tuesday_active')->default(false)->comment('Se trabalha na terça-feira');
            $table->time('tuesday_entry_1')->nullable()->comment('Terça - Entrada 1º turno');
            $table->time('tuesday_exit_1')->nullable()->comment('Terça - Saída 1º turno');
            $table->time('tuesday_entry_2')->nullable()->comment('Terça - Entrada 2º turno');
            $table->time('tuesday_exit_2')->nullable()->comment('Terça - Saída 2º turno');

            // Quarta-feira
            $table->boolean('wednesday_active')->default(false)->comment('Se trabalha na quarta-feira');
            $table->time('wednesday_entry_1')->nullable()->comment('Quarta - Entrada 1º turno');
            $table->time('wednesday_exit_1')->nullable()->comment('Quarta - Saída 1º turno');
            $table->time('wednesday_entry_2')->nullable()->comment('Quarta - Entrada 2º turno');
            $table->time('wednesday_exit_2')->nullable()->comment('Quarta - Saída 2º turno');

            // Quinta-feira
            $table->boolean('thursday_active')->default(false)->comment('Se trabalha na quinta-feira');
            $table->time('thursday_entry_1')->nullable()->comment('Quinta - Entrada 1º turno');
            $table->time('thursday_exit_1')->nullable()->comment('Quinta - Saída 1º turno');
            $table->time('thursday_entry_2')->nullable()->comment('Quinta - Entrada 2º turno');
            $table->time('thursday_exit_2')->nullable()->comment('Quinta - Saída 2º turno');

            // Sexta-feira
            $table->boolean('friday_active')->default(false)->comment('Se trabalha na sexta-feira');
            $table->time('friday_entry_1')->nullable()->comment('Sexta - Entrada 1º turno');
            $table->time('friday_exit_1')->nullable()->comment('Sexta - Saída 1º turno');
            $table->time('friday_entry_2')->nullable()->comment('Sexta - Entrada 2º turno');
            $table->time('friday_exit_2')->nullable()->comment('Sexta - Saída 2º turno');

            // Sábado
            $table->boolean('saturday_active')->default(false)->comment('Se trabalha no sábado');
            $table->time('saturday_entry_1')->nullable()->comment('Sábado - Entrada 1º turno');
            $table->time('saturday_exit_1')->nullable()->comment('Sábado - Saída 1º turno');
            $table->time('saturday_entry_2')->nullable()->comment('Sábado - Entrada 2º turno');
            $table->time('saturday_exit_2')->nullable()->comment('Sábado - Saída 2º turno');

            // Domingo
            $table->boolean('sunday_active')->default(false)->comment('Se trabalha no domingo');
            $table->time('sunday_entry_1')->nullable()->comment('Domingo - Entrada 1º turno');
            $table->time('sunday_exit_1')->nullable()->comment('Domingo - Saída 1º turno');
            $table->time('sunday_entry_2')->nullable()->comment('Domingo - Entrada 2º turno');
            $table->time('sunday_exit_2')->nullable()->comment('Domingo - Saída 2º turno');

            $table->text('description')->nullable()->comment('Descrição adicional da jornada');
            $table->boolean('is_active')->default(true)->comment('Se a jornada está ativa');

            $table->timestamps();

            // Índices para performance
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_hours');
    }
};
