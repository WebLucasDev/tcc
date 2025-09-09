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
        $this->command->info('🔄 Iniciando criação de solicitações para o colaborador ID 1...');

        // Limpar dados existentes
        SolicitationModel::truncate();

        // Verificar se o colaborador existe
        $collaborator = CollaboratorModel::find(1);
        if (!$collaborator) {
            $this->command->error('❌ Colaborador ID 1 não encontrado');
            return;
        }

        // Buscar alguns registros de time tracking do colaborador 1 para referenciar
        $timeTrackings = TimeTrackingModel::where('collaborator_id', 1)
            ->whereNotNull('entry_time_1')
            ->limit(3)
            ->get();

        $this->command->info("📊 Encontrados {$timeTrackings->count()} registros de time tracking para o colaborador");

        if ($timeTrackings->count() >= 2) {
            // Primeira solicitação - Correção do período da MANHÃ (entry_time_1 e return_time_1)
            SolicitationModel::create([
                'collaborator_id' => 1,
                'time_tracking_id' => $timeTrackings[0]->id,
                'status' => SolicitationStatusEnum::PENDING,
                'old_time_start' => $timeTrackings[0]->entry_time_1, // Entrada da manhã
                'old_time_finish' => $timeTrackings[0]->return_time_1, // Saída da manhã (almoço)
                'new_time_start' => Carbon::parse($timeTrackings[0]->entry_time_1)->addMinutes(30), // 30 min depois
                'new_time_finish' => Carbon::parse($timeTrackings[0]->return_time_1)->addMinutes(30), // Compensar no almoço
                'reason' => 'Atraso no período da manhã devido a trânsito intenso na data ' . $timeTrackings[0]->date->format('d/m/Y') . '. Solicito ajuste no horário de entrada da manhã com compensação no horário de saída para o almoço.',
                'admin_comment' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]);

            // Segunda solicitação - Correção do período da TARDE (entry_time_2 e return_time_2)
            SolicitationModel::create([
                'collaborator_id' => 1,
                'time_tracking_id' => $timeTrackings[1]->id,
                'status' => SolicitationStatusEnum::PENDING,
                'old_time_start' => $timeTrackings[1]->entry_time_2, // Retorno do almoço
                'old_time_finish' => $timeTrackings[1]->return_time_2, // Saída final
                'new_time_start' => $timeTrackings[1]->entry_time_2, // Manter retorno do almoço
                'new_time_finish' => Carbon::parse($timeTrackings[1]->return_time_2)->subMinutes(60), // Sair 1h antes
                'reason' => 'Necessidade de sair 1 hora mais cedo no período da tarde do dia ' . $timeTrackings[1]->date->format('d/m/Y') . ' por compromisso médico. Mantenho o horário de retorno do almoço normal.',
                'admin_comment' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]);

            $this->command->info('✅ Criadas 2 solicitações pendentes para o colaborador ID 1 com time tracking real');
        } else {
            $this->command->warn('⚠️  Poucos registros de time tracking encontrados. Criando solicitações genéricas...');
        }

        // Adicionar uma terceira solicitação para período da manhã de outro dia
        if ($timeTrackings->count() >= 3) {
            SolicitationModel::create([
                'collaborator_id' => 1,
                'time_tracking_id' => $timeTrackings[2]->id,
                'status' => SolicitationStatusEnum::PENDING,
                'old_time_start' => $timeTrackings[2]->entry_time_1, // Entrada da manhã
                'old_time_finish' => $timeTrackings[2]->return_time_1, // Saída para almoço
                'new_time_start' => Carbon::parse($timeTrackings[2]->entry_time_1)->subMinutes(15), // Chegar 15 min antes
                'new_time_finish' => Carbon::parse($timeTrackings[2]->return_time_1)->subMinutes(15), // Sair 15 min antes para almoço
                'reason' => 'Solicito ajuste no período da manhã do dia ' . $timeTrackings[2]->date->format('d/m/Y') . ' para entrar 15 minutos mais cedo e sair para o almoço também 15 minutos mais cedo por motivos pessoais.',
                'admin_comment' => null,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ]);
        } else {
            // Criar solicitação genérica para período da tarde
            SolicitationModel::create([
                'collaborator_id' => 1,
                'time_tracking_id' => null,
                'status' => SolicitationStatusEnum::PENDING,
                'old_time_start' => Carbon::createFromTime(13, 0), // Retorno do almoço
                'old_time_finish' => Carbon::createFromTime(17, 0), // Saída final
                'new_time_start' => Carbon::createFromTime(13, 0), // Manter retorno
                'new_time_finish' => Carbon::createFromTime(16, 30), // Sair 30 min antes
                'reason' => 'Solicitação genérica de ajuste no período da tarde por motivos pessoais.',
                'admin_comment' => null,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ]);
        }

        $totalSolicitations = SolicitationModel::count();

        $this->command->newLine();
        $this->command->info('🎯 Resumo das solicitações criadas:');
        $this->command->line('   - Colaborador: ' . $collaborator->name . ' (ID: 1)');
        $this->command->line('   - Status: Pendente (para testes)');
        $this->command->line('   - Quantidade: ' . $totalSolicitations . ' solicitações');
        $this->command->line('   - Enum utilizado: SolicitationStatusEnum::PENDING');
        $this->command->line('   - Regra: Apenas períodos consecutivos (manhã OU tarde)');

        // Exibir detalhes das solicitações
        $solicitations = SolicitationModel::with('collaborator')->get();
        foreach ($solicitations as $index => $solicitation) {
            $period = $this->determinePeriod($solicitation);
            $this->command->line('   ' . ($index + 1) . '. Time Tracking: ' . ($solicitation->time_tracking_id ?? 'Genérica') . ' - Período: ' . $period);
            $this->command->line('      Motivo: ' . substr($solicitation->reason, 0, 60) . '...');
        }
    }

    /**
     * Determina se a solicitação é para período da manhã ou tarde
     */
    private function determinePeriod($solicitation): string
    {
        if ($solicitation->old_time_start && $solicitation->old_time_finish) {
            $startHour = $solicitation->old_time_start->hour;
            $finishHour = $solicitation->old_time_finish->hour;

            // Se ambos os horários são antes das 13h, é período da manhã
            if ($startHour < 13 && $finishHour <= 13) {
                return 'Manhã (entry_time_1 → return_time_1)';
            }
            // Se ambos são após as 12h, é período da tarde
            else if ($startHour >= 12 && $finishHour > 13) {
                return 'Tarde (entry_time_2 → return_time_2)';
            }
        }

        return 'Indefinido';
    }
}
