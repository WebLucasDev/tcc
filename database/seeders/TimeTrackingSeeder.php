<?php

namespace Database\Seeders;

use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TimeTrackingSeeder extends Seeder
{
    /**
     * Popula a tabela 'time_tracking' no banco de dados, com os dados aqui dispostos.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $collaborators = CollaboratorModel::all();

        if ($collaborators->isEmpty()) {
            $this->command->error('Nenhum colaborador encontrado. Execute o CollaboratorSeeder primeiro.');
            return;
        }

        // Gerar registros dos últimos 30 dias
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now()->subDay(); // Até ontem

        foreach ($collaborators as $collaborator) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                // Pular fins de semana (sábado = 6, domingo = 0)
                if ($currentDate->dayOfWeek == 0 || $currentDate->dayOfWeek == 6) {
                    $currentDate->addDay();
                    continue;
                }

                // 85% de chance de ter registros completos
                $hasRecord = $faker->boolean(85);

                if ($hasRecord) {
                    // Variações nos horários baseados no horário padrão do colaborador
                    $baseEntry1 = Carbon::parse($collaborator->entry_time_1 ?: '08:00:00');
                    $baseReturn1 = Carbon::parse($collaborator->return_time_1 ?: '12:00:00');
                    $baseEntry2 = Carbon::parse($collaborator->entry_time_2 ?: '13:00:00');
                    $baseReturn2 = Carbon::parse($collaborator->return_time_2 ?: '17:00:00');

                    // Adicionar variações de +/- 15 minutos
                    $entryTime1 = $baseEntry1->copy()->addMinutes($faker->numberBetween(-15, 15))->format('H:i:s');
                    $returnTime1 = $baseReturn1->copy()->addMinutes($faker->numberBetween(-10, 10))->format('H:i:s');
                    $entryTime2 = $baseEntry2->copy()->addMinutes($faker->numberBetween(-10, 30))->format('H:i:s');
                    $returnTime2 = $baseReturn2->copy()->addMinutes($faker->numberBetween(-15, 30))->format('H:i:s');

                    // 5% de chance de ter registros incompletos
                    $isIncomplete = $faker->boolean(5);

                    if ($isIncomplete) {
                        // Registros incompletos - faltar algum horário
                        $missingField = $faker->randomElement(['return_time_1', 'entry_time_2', 'return_time_2']);
                        if ($missingField === 'return_time_1') $returnTime1 = null;
                        if ($missingField === 'entry_time_2') $entryTime2 = null;
                        if ($missingField === 'return_time_2') $returnTime2 = null;
                    }

                    $observations = null;
                    if ($faker->boolean(10)) { // 10% de chance de ter observações
                        $observations = $faker->randomElement([
                            'Reunião externa',
                            'Atendimento ao cliente',
                            'Treinamento',
                            'Saída antecipada autorizada',
                            'Entrada tardia justificada',
                            'Horas extras'
                        ]);
                    }

                    $timeTracking = TimeTrackingModel::create([
                        'collaborator_id' => $collaborator->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'entry_time_1' => $entryTime1,
                        'return_time_1' => $returnTime1,
                        'entry_time_2' => $entryTime2,
                        'return_time_2' => $returnTime2,
                        'observations' => $observations,
                    ]);

                    // Calcular e salvar o total de horas trabalhadas
                    $timeTracking->total_hours_worked = $timeTracking->calculateWorkedHours();
                    $timeTracking->save();
                } else {
                    // 15% de chance de não ter registro (falta)
                    $timeTracking = TimeTrackingModel::create([
                        'collaborator_id' => $collaborator->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'observations' => $faker->randomElement([
                            'Falta não justificada',
                            'Atestado médico',
                            'Férias',
                            'Folga compensatória'
                        ]),
                    ]);

                    // Para ausências, definir total como 0
                    $timeTracking->total_hours_worked = 0;
                    $timeTracking->save();
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('Registros de ponto dos últimos 30 dias criados com sucesso!');
    }
}
