<?php

namespace Database\Seeders;

use App\Enums\SolicitationStatusEnum;
use App\Models\SolicitationModel;
use App\Models\TimeTrackingModel;
use App\Models\CollaboratorModel;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SolicitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Iniciando criação de solicitações de ajuste de ponto...');

        // Limpar dados existentes se necessário
        SolicitationModel::truncate();

        // Buscar registros de time tracking e colaboradores existentes
        $timeTrackings = TimeTrackingModel::with('collaborator')->get();
        $colaborators = CollaboratorModel::all();

        if ($timeTrackings->isEmpty()) {
            $this->command->error('❌ Nenhum registro de time tracking encontrado. Execute primeiro o TimeTrackingSeeder.');
            return;
        }

        if ($colaborators->isEmpty()) {
            $this->command->error('❌ Nenhum colaborador encontrado. Execute primeiro o CollaboratorSeeder.');
            return;
        }

        $this->command->info('📊 Encontrados ' . $timeTrackings->count() . ' registros de ponto e ' . $colaborators->count() . ' colaboradores.');

        // Motivos comuns para solicitações
        $reasons = [
            'Esqueci de registrar o ponto na entrada',
            'Tive que sair mais cedo para consulta médica',
            'Cheguei atrasado devido ao trânsito intenso',
            'Houve um problema com o sistema de ponto',
            'Precisei me ausentar para resolver questões bancárias',
            'Tive reunião externa que se estendeu além do horário',
            'Esqueci de registrar o retorno do almoço',
            'Saí mais cedo por autorização verbal do supervisor',
            'Tive que levar meu filho ao médico',
            'Houve queda de energia e não consegui registrar',
            'Estive em treinamento externo',
            'Tive problema de saúde e precisei me ausentar',
            'Esqueci de registrar a saída para o almoço',
            'Cheguei mais cedo para compensar horas',
            'Tive que acompanhar familiar em consulta',
        ];

        // Comentários de administrador (para aprovados/rejeitados)
        $adminComments = [
            'Aprovado conforme justificativa apresentada.',
            'Solicitação válida, ajuste realizado no sistema.',
            'Justificativa aceita, ponto corrigido.',
            'Aprovado mediante comprovação médica.',
            'Situação excepcional aprovada.',
            'Rejeitado - falta de justificativa adequada.',
            'Rejeitado - não há comprovação da situação alegada.',
            'Rejeitado - solicitação recorrente sem justificativa.',
            'Rejeitado - horário não confere com o alegado.',
            'Rejeitado - necessário apresentar documento comprobatório.',
        ];

        $createdCount = 0;
        $statusDistribution = [
            SolicitationStatusEnum::PENDING->value => 12,    // 40%
            SolicitationStatusEnum::APPROVED->value => 10,   // 33%
            SolicitationStatusEnum::REJECTED->value => 5,    // 17%
            SolicitationStatusEnum::CANCELLED->value => 3,   // 10%
        ];

        $this->command->info('📝 Criando solicitações com diferentes status...');

        foreach ($statusDistribution as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Selecionar um time tracking aleatório
                $timeTracking = $timeTrackings->random();

                // Verificar se o colaborador existe
                $colaborator = $colaborators->find($timeTracking->collaborator_id);
                if (!$colaborator) {
                    // Se não encontrar colaborador por ID, usar um aleatório
                    $colaborator = $colaborators->random();
                }

                // Gerar horários antigos (baseados no registro original)
                $oldTimeStart = $timeTracking->entry_time_1 ?
                    Carbon::parse($timeTracking->entry_time_1) :
                    Carbon::createFromTime(8, 0);

                $oldTimeFinish = $timeTracking->return_time_2 ?
                    Carbon::parse($timeTracking->return_time_2) :
                    Carbon::createFromTime(17, 0);

                // Gerar novos horários (com variações realistas)
                $newTimeStart = (clone $oldTimeStart)->addMinutes(rand(-30, 30));
                $newTimeFinish = (clone $oldTimeFinish)->addMinutes(rand(-60, 60));

                $solicitation = SolicitationModel::create([
                    'colaborator_id' => $colaborator->id,
                    'time_tracking_id' => $timeTracking->id,
                    'status' => $status,
                    'old_time_start' => $oldTimeStart,
                    'old_time_finish' => $oldTimeFinish,
                    'new_time_start' => $newTimeStart,
                    'new_time_finish' => $newTimeFinish,
                    'reason' => fake()->randomElement($reasons),
                    'admin_comment' => in_array($status, [
                        SolicitationStatusEnum::APPROVED->value,
                        SolicitationStatusEnum::REJECTED->value
                    ]) ? fake()->randomElement($adminComments) : null,
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                    'updated_at' => now(),
                ]);

                $createdCount++;

                if ($createdCount % 5 == 0) {
                    $this->command->line("   ✓ Criadas {$createdCount} solicitações...");
                }
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Seeder concluída com sucesso!");
        $this->command->newLine();

        // Exibir estatísticas
        $this->command->line("📈 <fg=yellow>ESTATÍSTICAS DAS SOLICITAÇÕES:</fg=yellow>");
        $this->command->line("   🔄 Pendentes: " . SolicitationModel::where('status', 'pending')->count());
        $this->command->line("   ✅ Aprovadas: " . SolicitationModel::where('status', 'approved')->count());
        $this->command->line("   ❌ Rejeitadas: " . SolicitationModel::where('status', 'rejected')->count());
        $this->command->line("   🚫 Canceladas: " . SolicitationModel::where('status', 'cancelled')->count());
        $this->command->line("   📊 Total: " . SolicitationModel::count());

        $this->command->newLine();
        $this->command->line("🎯 <fg=green>Solicitações criadas com dados realistas para testes!</fg=green>");
    }
}
