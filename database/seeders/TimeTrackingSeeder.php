<?php

namespace Database\Seeders;

use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;
use App\Models\WorkHoursModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TimeTrackingSeeder extends Seeder
{
    /**
     * Popula a tabela 'time_tracking' com registros para todo o ano de 2025,
     * respeitando as jornadas de trabalho de cada colaborador.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $collaborators = CollaboratorModel::with('workHours')->get();

        if ($collaborators->isEmpty()) {
            $this->command->error('Nenhum colaborador encontrado. Execute o CollaboratorSeeder primeiro.');
            return;
        }

        // Gerar registros para todo o ano de 2025
        $startDate = Carbon::createFromDate(2025, 1, 1);
        $endDate = Carbon::createFromDate(2025, 12, 31);

        $this->command->info('Gerando registros de ponto para ' . $collaborators->count() . ' colaboradores...');
        $this->command->info('Período: ' . $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y'));

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalRecords = 0;

        foreach ($collaborators as $index => $collaborator) {
            $this->command->info('Processando ' . ($index + 1) . '/' . $collaborators->count() . ': ' . $collaborator->name);
            
            $currentDate = $startDate->copy();

            for ($currentDate = $startDate->copy(); $currentDate->lte($endDate); $currentDate->addDay()) {
                // Verificar se já existe registro para esta data e colaborador
                $existingRecord = TimeTrackingModel::where('collaborator_id', $collaborator->id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->first();

                if ($existingRecord) {
                    continue; // Pula se já existe
                }

                // Verificar se deve trabalhar no dia baseado na jornada
                $dayName = strtolower($currentDate->locale('en')->dayName);
                $shouldWork = $this->shouldWorkOnDay($collaborator, $dayName);

                if ($shouldWork) {
                    // Registro normal de trabalho
                    $recordCreated = $this->createTimeRecord($collaborator, $currentDate->copy(), $faker);
                    if ($recordCreated) {
                        $totalRecords++;
                    }
                } else {
                    // Pequena chance de trabalhar em dias não programados (horas extras, plantões)
                    if ($faker->boolean(2)) { // 2% de chance
                        $recordCreated = $this->createExtraTimeRecord($collaborator, $currentDate->copy(), $faker);
                        if ($recordCreated) {
                            $totalRecords++;
                        }
                    }
                }
            }
        }

        $this->command->info("✅ Registros de ponto criados com sucesso!");
        $this->command->info("📊 Total de registros gerados: {$totalRecords}");
        $this->command->info("📅 Período completo de 2025 processado!");
    }

    /**
     * Verifica se o colaborador deve trabalhar no dia especificado baseado na jornada
     */
    private function shouldWorkOnDay($collaborator, $dayName)
    {
        if (!$collaborator->workHours) {
            return false; // Se não tem jornada definida, não trabalha
        }

        $workHours = $collaborator->workHours;
        $dayActiveField = $dayName . '_active';

        return $workHours->{$dayActiveField} ?? false;
    }

    /**
     * Cria um registro de ponto normal baseado na jornada do colaborador
     */
    private function createTimeRecord($collaborator, $date, $faker)
    {
        $workHours = $collaborator->workHours;
        if (!$workHours) {
            return false;
        }

        // Feriados brasileiros de 2025 (simplificado)
        $holidays = $this->getBrazilianHolidays2025();
        if (in_array($date->format('Y-m-d'), $holidays)) {
            // 10% de chance de trabalhar em feriados
            if (!$faker->boolean(10)) {
                return false;
            }
        }

        $dayName = strtolower($date->locale('en')->dayName);

        // Obter horários da jornada para o dia
        $entry1Field = $dayName . '_entry_1';
        $exit1Field = $dayName . '_exit_1';
        $entry2Field = $dayName . '_entry_2';
        $exit2Field = $dayName . '_exit_2';

        $baseEntry1 = $workHours->{$entry1Field};
        $baseExit1 = $workHours->{$exit1Field};
        $baseEntry2 = $workHours->{$entry2Field};
        $baseExit2 = $workHours->{$exit2Field};

        // 90% de chance de comparecer ao trabalho
        if (!$faker->boolean(90)) {
            return $this->createAbsenceRecord($collaborator, $date, $faker);
        }

        // Calcular horários com variações realistas
        $entryTime1 = null;
        $exitTime1 = null;
        $entryTime2 = null;
        $exitTime2 = null;

        if ($baseEntry1) {
            $baseTime = Carbon::parse($baseEntry1);
            $variation = $this->getTimeVariation($faker);
            $entryTime1 = $baseTime->addMinutes($variation)->format('H:i:s');
        }

        if ($baseExit1) {
            $baseTime = Carbon::parse($baseExit1);
            $variation = $this->getTimeVariation($faker, 'exit');
            $exitTime1 = $baseTime->addMinutes($variation)->format('H:i:s');
        }

        if ($baseEntry2) {
            $baseTime = Carbon::parse($baseEntry2);
            $variation = $this->getTimeVariation($faker);
            $entryTime2 = $baseTime->addMinutes($variation)->format('H:i:s');
        }

        if ($baseExit2) {
            $baseTime = Carbon::parse($baseExit2);
            $variation = $this->getTimeVariation($faker, 'exit');
            $exitTime2 = $baseTime->addMinutes($variation)->format('H:i:s');
        }

        // 3% de chance de registro incompleto (esqueceu de bater ponto)
        $makeIncomplete = $faker->boolean(3);
        
        if ($makeIncomplete) {
            // Para jornadas de 1 turno, só pode esquecer saída
            if (!$baseEntry2 && !$baseExit2) {
                $exitTime1 = null; // Esqueceu de bater saída
            } else {
                // Para jornadas de 2 turnos, pode esquecer vários pontos
                $missingFields = ['exit_1', 'entry_2', 'exit_2'];
                $missingField = $faker->randomElement($missingFields);
                
                switch ($missingField) {
                    case 'exit_1':
                        $exitTime1 = null;
                        break;
                    case 'entry_2':
                        $entryTime2 = null;
                        break;
                    case 'exit_2':
                        $exitTime2 = null;
                        break;
                }
            }
        }

        $observations = null;
        if ($faker->boolean(12)) { // 12% de chance de ter observações
            $observations = $faker->randomElement([
                'Reunião externa com cliente',
                'Treinamento técnico',
                'Atendimento presencial ao cliente',
                'Reunião de equipe',
                'Visita técnica',
                'Apresentação de projeto',
                'Suporte técnico urgente',
                'Implantação de sistema',
                'Manutenção de servidor',
                'Backup de dados',
                'Atualização de sistema',
                'Desenvolvimento de nova funcionalidade'
            ]);
        }

        // Determinar status baseado nos horários preenchidos e na jornada
        $status = 'completo';
        
        // Verificar se todos os horários obrigatórios estão preenchidos
        if (!$entryTime1 || !$exitTime1) {
            $status = 'incompleto';
        }
        
        // Se tem segundo turno definido na jornada, verificar se está completo
        if ($baseEntry2 && $baseExit2) {
            if (!$entryTime2 || !$exitTime2) {
                $status = 'incompleto';
            }
        }
        // Se não tem segundo turno na jornada, mas foi preenchido no registro
        elseif ($entryTime2 || $exitTime2) {
            // Se preencheu apenas um dos dois, fica incompleto
            if (!$entryTime2 || !$exitTime2) {
                $status = 'incompleto';
            }
        }

        $timeTracking = TimeTrackingModel::create([
            'collaborator_id' => $collaborator->id,
            'date' => $date->format('Y-m-d'),
            'entry_time_1' => $entryTime1,
            'return_time_1' => $exitTime1,
            'entry_time_2' => $entryTime2,
            'return_time_2' => $exitTime2,
            'observations' => $observations,
            'status' => $status,
        ]);

        // Calcular e salvar o total de horas trabalhadas
        $timeTracking->total_hours_worked = $timeTracking->calculateWorkedHours();
        $timeTracking->save();

        return true;
    }

    /**
     * Cria um registro de ausência/falta
     */
    private function createAbsenceRecord($collaborator, $date, $faker)
    {
        $absenceReasons = [
            'Atestado médico',
            'Consulta médica',
            'Falta justificada',
            'Licença médica',
            'Férias programadas',
            'Folga compensatória',
            'Licença para tratamento de saúde',
            'Acompanhamento médico familiar',
            'Questões pessoais autorizadas'
        ];

        // 15% das faltas são não justificadas
        if ($faker->boolean(15)) {
            $absenceReasons = ['Falta não justificada', 'Ausência não comunicada'];
        }

        TimeTrackingModel::create([
            'collaborator_id' => $collaborator->id,
            'date' => $date->format('Y-m-d'),
            'observations' => $faker->randomElement($absenceReasons),
            'status' => 'ausente',
            'total_hours_worked' => 0,
        ]);

        return true;
    }

    /**
     * Cria registro de trabalho em dia não programado (horas extras, plantão)
     */
    private function createExtraTimeRecord($collaborator, $date, $faker)
    {
        // Horários típicos para trabalho extra
        $extraSchedules = [
            ['entry' => '08:00', 'exit' => '12:00'], // Meio período manhã
            ['entry' => '14:00', 'exit' => '18:00'], // Meio período tarde
            ['entry' => '19:00', 'exit' => '23:00'], // Período noturno
            ['entry' => '08:00', 'exit' => '17:00'], // Dia completo
        ];

        $schedule = $faker->randomElement($extraSchedules);

        $observations = $faker->randomElement([
            'Plantão de fim de semana',
            'Manutenção programada',
            'Suporte técnico urgente',
            'Backup de sistemas',
            'Implantação fora do horário comercial',
            'Atendimento de emergência',
            'Projeto com prazo crítico',
            'Migração de dados',
            'Atualização de sistema',
            'Monitoramento especial'
        ]);

        $timeTracking = TimeTrackingModel::create([
            'collaborator_id' => $collaborator->id,
            'date' => $date->format('Y-m-d'),
            'entry_time_1' => $schedule['entry'],
            'return_time_1' => $schedule['exit'],
            'observations' => $observations,
            'status' => 'completo', // Trabalho extra sempre completo
        ]);

        // Calcular e salvar o total de horas trabalhadas
        $timeTracking->total_hours_worked = $timeTracking->calculateWorkedHours();
        $timeTracking->save();

        return true;
    }

    /**
     * Gera variações realistas nos horários
     */
    private function getTimeVariation($faker, $type = 'entry')
    {
        if ($type === 'exit') {
            // Saídas: mais chance de sair tarde (horas extras)
            return $faker->randomElement([
                -10, -5, 0, 5, 10, 15, 20, 30, 45, 60, // 60 = 1h extra
                -15, -10, -5, 0, 5, 10, 15, 30, // variações normais
            ]);
        } else {
            // Entradas: mais chance de atraso
            return $faker->randomElement([
                -10, -5, 0, 5, 10, 15, 20, 30, // atrasos são mais comuns
                -5, 0, 5, 10, 15, // variações normais
                -15, -10, -5, 0, 5, // antecipações menores
            ]);
        }
    }

    /**
     * Lista de feriados brasileiros para 2025 (principais)
     */
    private function getBrazilianHolidays2025()
    {
        return [
            '2025-01-01', // Confraternização Universal
            '2025-02-17', // Carnaval (segunda-feira)
            '2025-02-18', // Carnaval (terça-feira)
            '2025-04-18', // Sexta-feira Santa
            '2025-04-21', // Tiradentes
            '2025-05-01', // Dia do Trabalhador
            '2025-09-07', // Independência do Brasil
            '2025-10-12', // Nossa Senhora Aparecida
            '2025-11-02', // Finados
            '2025-11-15', // Proclamação da República
            '2025-12-25', // Natal
        ];
    }
}
