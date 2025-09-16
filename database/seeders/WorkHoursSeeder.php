<?php

namespace Database\Seeders;

use App\Enums\WorkHoursStatusEnum;
use App\Models\WorkHoursModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jornada CLT Padrão 44h (Segunda a Sexta)
        WorkHoursModel::create([
            'name' => 'CLT Padrão 44h',
            'description' => 'Jornada padrão de 44 horas semanais, segunda a sexta-feira com intervalo para almoço',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Segunda-feira
            'monday_active' => true,
            'monday_entry_1' => '08:00',
            'monday_exit_1' => '12:00',
            'monday_entry_2' => '13:00',
            'monday_exit_2' => '17:48',

            // Terça-feira
            'tuesday_active' => true,
            'tuesday_entry_1' => '08:00',
            'tuesday_exit_1' => '12:00',
            'tuesday_entry_2' => '13:00',
            'tuesday_exit_2' => '17:48',

            // Quarta-feira
            'wednesday_active' => true,
            'wednesday_entry_1' => '08:00',
            'wednesday_exit_1' => '12:00',
            'wednesday_entry_2' => '13:00',
            'wednesday_exit_2' => '17:48',

            // Quinta-feira
            'thursday_active' => true,
            'thursday_entry_1' => '08:00',
            'thursday_exit_1' => '12:00',
            'thursday_entry_2' => '13:00',
            'thursday_exit_2' => '17:48',

            // Sexta-feira
            'friday_active' => true,
            'friday_entry_1' => '08:00',
            'friday_exit_1' => '12:00',
            'friday_entry_2' => '13:00',
            'friday_exit_2' => '17:48',
        ]);

        // Jornada 30h (Meio Período)
        WorkHoursModel::create([
            'name' => 'Meio Período 30h',
            'description' => 'Jornada reduzida de 30 horas semanais, apenas período matutino',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Segunda-feira
            'monday_active' => true,
            'monday_entry_1' => '08:00',
            'monday_exit_1' => '14:00',

            // Terça-feira
            'tuesday_active' => true,
            'tuesday_entry_1' => '08:00',
            'tuesday_exit_1' => '14:00',

            // Quarta-feira
            'wednesday_active' => true,
            'wednesday_entry_1' => '08:00',
            'wednesday_exit_1' => '14:00',

            // Quinta-feira
            'thursday_active' => true,
            'thursday_entry_1' => '08:00',
            'thursday_exit_1' => '14:00',

            // Sexta-feira
            'friday_active' => true,
            'friday_entry_1' => '08:00',
            'friday_exit_1' => '14:00',
        ]);

        // Jornada 12x36 (Saúde)
        WorkHoursModel::create([
            'name' => 'Escala 12x36 - Plantão',
            'description' => 'Jornada 12x36 para profissionais da saúde e segurança',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Segunda, Quarta e Sexta (exemplo)
            'monday_active' => true,
            'monday_entry_1' => '07:00',
            'monday_exit_1' => '19:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '07:00',
            'wednesday_exit_1' => '19:00',

            'friday_active' => true,
            'friday_entry_1' => '07:00',
            'friday_exit_1' => '19:00',
        ]);

        // Jornada com Final de Semana
        WorkHoursModel::create([
            'name' => 'Comercial com Sábados',
            'description' => 'Horário comercial incluindo sábados pela manhã',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Segunda a Sexta
            'monday_active' => true,
            'monday_entry_1' => '09:00',
            'monday_exit_1' => '18:00',

            'tuesday_active' => true,
            'tuesday_entry_1' => '09:00',
            'tuesday_exit_1' => '18:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '09:00',
            'wednesday_exit_1' => '18:00',

            'thursday_active' => true,
            'thursday_entry_1' => '09:00',
            'thursday_exit_1' => '18:00',

            'friday_active' => true,
            'friday_entry_1' => '09:00',
            'friday_exit_1' => '18:00',

            // Sábado meio período
            'saturday_active' => true,
            'saturday_entry_1' => '08:00',
            'saturday_exit_1' => '12:00',
        ]);

        // Jornada Noturna
        WorkHoursModel::create([
            'name' => 'Turno Noturno',
            'description' => 'Jornada noturna para operações 24h',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Domingo a Quinta (cobrindo a noite)
            'sunday_active' => true,
            'sunday_entry_1' => '22:00',
            'sunday_exit_1' => '06:00',

            'monday_active' => true,
            'monday_entry_1' => '22:00',
            'monday_exit_1' => '06:00',

            'tuesday_active' => true,
            'tuesday_entry_1' => '22:00',
            'tuesday_exit_1' => '06:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '22:00',
            'wednesday_exit_1' => '06:00',

            'thursday_active' => true,
            'thursday_entry_1' => '22:00',
            'thursday_exit_1' => '06:00',
        ]);

        // Jornada Flexível
        WorkHoursModel::create([
            'name' => 'Horário Flexível',
            'description' => 'Jornada flexível com horários diferenciados por dia',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Horários variados
            'monday_active' => true,
            'monday_entry_1' => '08:00',
            'monday_exit_1' => '17:00',

            'tuesday_active' => true,
            'tuesday_entry_1' => '09:00',
            'tuesday_exit_1' => '18:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '08:30',
            'wednesday_exit_1' => '17:30',

            'thursday_active' => true,
            'thursday_entry_1' => '07:30',
            'thursday_exit_1' => '16:30',

            'friday_active' => true,
            'friday_entry_1' => '08:00',
            'friday_exit_1' => '16:00',
        ]);

        // Jornada de Estagiário
        WorkHoursModel::create([
            'name' => 'Estágio 20h',
            'description' => 'Jornada para estagiários de 20 horas semanais',
            'status' => WorkHoursStatusEnum::ACTIVE,

            // Segunda, Quarta e Sexta
            'monday_active' => true,
            'monday_entry_1' => '14:00',
            'monday_exit_1' => '18:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '14:00',
            'wednesday_exit_1' => '18:00',

            'friday_active' => true,
            'friday_entry_1' => '14:00',
            'friday_exit_1' => '18:00',

            // Terça e Quinta (meio período)
            'tuesday_active' => true,
            'tuesday_entry_1' => '14:00',
            'tuesday_exit_1' => '18:00',

            'thursday_active' => true,
            'thursday_entry_1' => '14:00',
            'thursday_exit_1' => '18:00',
        ]);

        // Jornada Inativa (para demonstrar status)
        WorkHoursModel::create([
            'name' => 'Horário Antigo - Descontinuado',
            'description' => 'Jornada que não é mais utilizada pela empresa',
            'status' => WorkHoursStatusEnum::INACTIVE,

            'monday_active' => true,
            'monday_entry_1' => '07:00',
            'monday_exit_1' => '16:00',

            'tuesday_active' => true,
            'tuesday_entry_1' => '07:00',
            'tuesday_exit_1' => '16:00',

            'wednesday_active' => true,
            'wednesday_entry_1' => '07:00',
            'wednesday_exit_1' => '16:00',

            'thursday_active' => true,
            'thursday_entry_1' => '07:00',
            'thursday_exit_1' => '16:00',

            'friday_active' => true,
            'friday_entry_1' => '07:00',
            'friday_exit_1' => '16:00',
        ]);

        // Jornada de Final de Semana
        WorkHoursModel::create([
            'name' => 'Fim de Semana',
            'description' => 'Jornada específica para operações de fim de semana',
            'status' => WorkHoursStatusEnum::ACTIVE,

            'saturday_active' => true,
            'saturday_entry_1' => '08:00',
            'saturday_exit_1' => '12:00',
            'saturday_entry_2' => '13:00',
            'saturday_exit_2' => '17:00',

            'sunday_active' => true,
            'sunday_entry_1' => '08:00',
            'sunday_exit_1' => '12:00',
            'sunday_entry_2' => '13:00',
            'sunday_exit_2' => '17:00',
        ]);
    }
}
