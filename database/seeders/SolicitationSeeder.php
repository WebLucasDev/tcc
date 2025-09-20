<?php

namespace Database\Seeders;

use App\Models\SolicitationModel;
use App\Models\TimeTrackingModel;
use App\Models\CollaboratorModel;
use App\Enums\SolicitationStatusEnum;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SolicitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Iniciando criação de solicitações...');

        // Limpar dados existentes
        SolicitationModel::truncate();

        // Pegar alguns colaboradores aleatórios (máximo 8)
        $collaborators = CollaboratorModel::inRandomOrder()->limit(8)->get();

        if ($collaborators->isEmpty()) {
            $this->command->error('❌ Nenhum colaborador encontrado');
            return;
        }

        $totalCreated = 0;

        foreach ($collaborators as $collaborator) {
            $this->command->info("Processando colaborador: {$collaborator->name}");

            // Buscar registros de ponto do colaborador (máximo 2 por colaborador)
            $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
                ->where('status', 'completo')
                ->whereNotNull('entry_time_1')
                ->whereNotNull('return_time_1')
                ->inRandomOrder()
                ->limit(2)
                ->get();

            if ($timeTrackings->count() > 0) {
                // Criar 1-2 solicitações por colaborador
                $numSolicitations = min(2, $timeTrackings->count());

                for ($i = 0; $i < $numSolicitations; $i++) {
                    $timeTracking = $timeTrackings[$i];
                    $status = collect([
                        SolicitationStatusEnum::PENDING,
                        SolicitationStatusEnum::PENDING,
                        SolicitationStatusEnum::APPROVED,
                        SolicitationStatusEnum::REJECTED
                    ])->random();

                    // Determinar tipo de solicitação aleatoriamente
                    $solicitationType = collect(['morning', 'afternoon', 'full_shift'])->random();

                    $solicitation = $this->createSolicitation($collaborator, $timeTracking, $status, $solicitationType);

                    if ($solicitation) {
                        $totalCreated++;
                    }
                }
            }
        }

        $this->command->newLine();
        $this->command->info("✅ {$totalCreated} solicitações criadas com sucesso!");

        // Exibir resumo
        $this->showSummary();
    }

    /**
     * Cria uma solicitação baseada no tipo especificado
     */
    private function createSolicitation($collaborator, $timeTracking, $status, $type)
    {
        $reasons = [
            'morning' => [
                'Atraso devido ao trânsito intenso. Solicito compensação no horário.',
                'Consulta médica no período da manhã. Necessário ajuste de horário.',
                'Problema no transporte público. Peço para regularizar o ponto.',
                'Compromisso familiar urgente na manhã do dia.'
            ],
            'afternoon' => [
                'Necessidade de sair mais cedo por compromisso médico.',
                'Reunião na escola do filho. Preciso sair antes do horário.',
                'Consulta médica familiar. Solicito saída antecipada.',
                'Compromisso pessoal inadiável no final da tarde.'
            ],
            'full_shift' => [
                'Trabalho externo durante todo o expediente. Solicito ajuste completo.',
                'Treinamento fora da empresa. Necessário correção dos horários.',
                'Atendimento a cliente externo durante todo o dia.',
                'Home office por motivos pessoais. Peço regularização do ponto.'
            ]
        ];

        $data = [
            'collaborator_id' => $collaborator->id,
            'time_tracking_id' => $timeTracking->id,
            'status' => $status,
            'reason' => collect($reasons[$type])->random(),
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now()->subDays(rand(0, 5)),
        ];

        // Definir horários baseado no tipo
        switch ($type) {
            case 'morning':
                // Apenas período da manhã (entry_time_1 → return_time_1)
                $data['old_time_start'] = $timeTracking->entry_time_1;
                $data['old_time_finish'] = $timeTracking->return_time_1;

                // Garantir que o novo horário de entrada seja antes da saída
                $newEntry = Carbon::parse($timeTracking->entry_time_1)->addMinutes(rand(15, 60));
                $newExit = Carbon::parse($timeTracking->return_time_1)->addMinutes(rand(0, 30));

                // Se a nova entrada ficar depois da saída, ajustar
                if ($newEntry->gte($newExit)) {
                    $newExit = $newEntry->copy()->addMinutes(rand(30, 120));
                }

                $data['new_time_start'] = $newEntry;
                $data['new_time_finish'] = $newExit;
                break;

            case 'afternoon':
                // Apenas período da tarde (entry_time_2 → return_time_2)
                if ($timeTracking->entry_time_2 && $timeTracking->return_time_2) {
                    $data['old_time_start'] = $timeTracking->entry_time_2;
                    $data['old_time_finish'] = $timeTracking->return_time_2;

                    // Para tarde, geralmente é saída antecipada
                    $data['new_time_start'] = $timeTracking->entry_time_2; // Manter entrada
                    $data['new_time_finish'] = Carbon::parse($timeTracking->return_time_2)->subMinutes(rand(30, 120));

                    // Garantir que a saída seja depois da entrada
                    if (Carbon::parse($data['new_time_finish'])->lte(Carbon::parse($data['new_time_start']))) {
                        $data['new_time_finish'] = Carbon::parse($data['new_time_start'])->addMinutes(60);
                    }
                } else {
                    // Se não tem turno da tarde, usar manhã mas com ajuste menor
                    $data['old_time_start'] = $timeTracking->entry_time_1;
                    $data['old_time_finish'] = $timeTracking->return_time_1;
                    $data['new_time_start'] = $timeTracking->entry_time_1;
                    $data['new_time_finish'] = Carbon::parse($timeTracking->return_time_1)->subMinutes(rand(15, 45));

                    // Garantir que a saída seja depois da entrada
                    if (Carbon::parse($data['new_time_finish'])->lte(Carbon::parse($data['new_time_start']))) {
                        $data['new_time_finish'] = Carbon::parse($data['new_time_start'])->addMinutes(30);
                    }
                }
                break;

            case 'full_shift':
                // Turno completo: da entrada da manhã até a saída final
                $data['old_time_start'] = $timeTracking->entry_time_1;
                $data['old_time_finish'] = $timeTracking->return_time_2 ?? $timeTracking->return_time_1;

                // Pequenos ajustes no turno completo
                $newEntry = Carbon::parse($timeTracking->entry_time_1)->addMinutes(rand(-30, 30));
                $newExit = Carbon::parse($data['old_time_finish'])->addMinutes(rand(-30, 60));

                // Garantir que entrada seja antes da saída
                if ($newEntry->gte($newExit)) {
                    $newExit = $newEntry->copy()->addMinutes(rand(240, 480)); // 4-8 horas depois
                }

                $data['new_time_start'] = $newEntry;
                $data['new_time_finish'] = $newExit;
                break;
        }

        // Adicionar comentário do admin se aprovado/rejeitado
        if ($status === SolicitationStatusEnum::APPROVED) {
            $data['admin_comment'] = collect([
                'Solicitação aprovada. Ajuste realizado conforme solicitado.',
                'Justificativa aceita. Ponto regularizado.',
                'Aprovado mediante comprovante apresentado.',
                'Situação regularizada conforme política da empresa.'
            ])->random();
        } elseif ($status === SolicitationStatusEnum::REJECTED) {
            $data['admin_comment'] = collect([
                'Solicitação negada. Não há justificativa suficiente.',
                'Falta documentação comprobatória para aprovação.',
                'Política da empresa não permite este tipo de ajuste.',
                'Prazo para solicitação expirado.'
            ])->random();
        }

        return SolicitationModel::create($data);
    }

    /**
     * Exibe resumo das solicitações criadas
     */
    private function showSummary()
    {
        $solicitations = SolicitationModel::with('collaborator')->get();

        $this->command->info('📊 Resumo das solicitações:');

        $statusCounts = [
            'pending' => $solicitations->where('status', SolicitationStatusEnum::PENDING)->count(),
            'approved' => $solicitations->where('status', SolicitationStatusEnum::APPROVED)->count(),
            'rejected' => $solicitations->where('status', SolicitationStatusEnum::REJECTED)->count(),
        ];

        $this->command->line("   🟡 Pendentes: {$statusCounts['pending']}");
        $this->command->line("   🟢 Aprovadas: {$statusCounts['approved']}");
        $this->command->line("   🔴 Rejeitadas: {$statusCounts['rejected']}");

        $this->command->newLine();
        foreach ($solicitations as $index => $solicitation) {
            $status = match($solicitation->status) {
                SolicitationStatusEnum::PENDING => '🟡 Pendente',
                SolicitationStatusEnum::APPROVED => '🟢 Aprovada',
                SolicitationStatusEnum::REJECTED => '🔴 Rejeitada',
                default => '⚪ Desconhecido'
            };

            $this->command->line("   " . ($index + 1) . ". {$solicitation->collaborator->name} - {$status}");
            $this->command->line("      Motivo: " . substr($solicitation->reason, 0, 50) . "...");
        }
    }
}
