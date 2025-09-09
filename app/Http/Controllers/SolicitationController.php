<?php

namespace App\Http\Controllers;

use App\Enums\SolicitationStatusEnum;
use App\Models\SolicitationModel;
use App\Models\TimeTrackingModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SolicitationController extends Controller
{
    /**
     * Exibir listagem de solicitações
     */
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Solicitações', 'url' => null]
        ];

        $solicitations = SolicitationModel::with(['collaborator', 'timeTracking']);

        // Aplicar filtros de busca
        if (request('search')) {
            $search = request('search');
            $solicitations = $solicitations->where(function ($query) use ($search) {
                $query->whereHas('collaborator', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Aplicar filtro de status
        if (request('status')) {
            $solicitations = $solicitations->where('status', request('status'));
        }

        // Aplicar ordenação
        $sortBy = request('sort_by', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        switch ($sortBy) {
            case 'collaborator_name':
                $solicitations = $solicitations->join('collaborators', 'solicitations.collaborator_id', '=', 'collaborators.id')
                    ->orderBy('collaborators.name', $sortDirection)
                    ->select('solicitations.*');
                break;
            case 'status':
                $solicitations = $solicitations->orderBy('status', $sortDirection);
                break;
            default:
                $solicitations = $solicitations->orderBy('created_at', $sortDirection);
                break;
        }

        $solicitations = $solicitations->paginate(15);

        // Verificar se a página atual é válida
        if ($solicitations->currentPage() > $solicitations->lastPage() && $solicitations->lastPage() > 0) {
            // Redirecionar para a última página válida em requisições não-AJAX
            if (!request()->ajax()) {
                return redirect()->route('solicitation.index', array_merge(request()->query(), ['page' => $solicitations->lastPage()]));
            }

            // Para requisições AJAX, buscar novamente com a página correta
            $queryParams = request()->query();
            $queryParams['page'] = $solicitations->lastPage();

            // Refazer a consulta com a página correta
            $solicitations = SolicitationModel::with(['collaborator', 'timeTracking']);

            // Reaplicar filtros
            if (request('search')) {
                $search = request('search');
                $solicitations = $solicitations->where(function ($query) use ($search) {
                    $query->whereHas('collaborator', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    })->orWhere('reason', 'like', "%{$search}%");
                });
            }

            if (request('status')) {
                $solicitations = $solicitations->where('status', request('status'));
            }

            // Reaplicar ordenação
            switch ($sortBy) {
                case 'collaborator_name':
                    $solicitations = $solicitations->join('collaborators', 'solicitations.collaborator_id', '=', 'collaborators.id')
                        ->orderBy('collaborators.name', $sortDirection)
                        ->select('solicitations.*');
                    break;
                case 'status':
                    $solicitations = $solicitations->orderBy('status', $sortDirection);
                    break;
                default:
                    $solicitations = $solicitations->orderBy('created_at', $sortDirection);
                    break;
            }

            $solicitations = $solicitations->paginate(15, ['*'], 'page', $solicitations->lastPage());
        }

        // Estatísticas para o dashboard
        $stats = [
            'pending' => SolicitationModel::where('status', 'pending')->count(),
            'approved' => SolicitationModel::where('status', 'approved')->count(),
            'rejected' => SolicitationModel::where('status', 'rejected')->count(),
            'cancelled' => SolicitationModel::where('status', 'cancelled')->count(),
        ];

        // Verificar se é uma requisição AJAX
        if (request()->ajax()) {
            $paginationHtml = '';
            $showPagination = $solicitations->hasPages() && $solicitations->lastPage() > 1;

            if ($showPagination || $solicitations->count() > 0) {
                $paginationHtml = view('auth.time-management.solicitations.partials.pagination', compact('solicitations'))->render();
            }

            return response()->json([
                'table' => view('auth.time-management.solicitations.partials.table', compact('solicitations'))->render(),
                'pagination' => $paginationHtml,
                'statistics' => $stats,
                'currentPage' => $solicitations->currentPage(),
                'lastPage' => $solicitations->lastPage(),
                'hasPages' => $solicitations->hasPages(),
                'resetPage' => request('page') && $solicitations->currentPage() != request('page')
            ]);
        }

        return view('auth.time-management.solicitations.index', compact('breadcrumbs', 'solicitations', 'stats'));
    }

    /**
     * Aprovar uma solicitação
     */
    public function approve(Request $request, $id): RedirectResponse
    {
        $solicitation = SolicitationModel::with('timeTracking')->findOrFail($id);

        try {
            // Verificar se a solicitação ainda está pendente
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Esta solicitação já foi processada.');
            }

            // Atualizar status para aprovado
            $solicitation->update([
                'status' => SolicitationStatusEnum::APPROVED,
                'admin_comment' => $request->input('admin_comment')
            ]);

            // Aplicar as alterações no registro de time tracking
            $timeTracking = $solicitation->timeTracking;
            if ($timeTracking) {
                // Atualizar os horários conforme a solicitação
                $updates = [];

                // Verificar se é uma alteração do período da manhã (entry_time_1 e return_time_1)
                if ($solicitation->new_time_start && $solicitation->new_time_finish) {
                    // Determinar se é período da manhã ou tarde baseado nos horários atuais
                    $isMorningPeriod = $this->isMorningPeriodUpdate($solicitation, $timeTracking);
                    
                    if ($isMorningPeriod) {
                        $updates['entry_time_1'] = $solicitation->new_time_start->format('H:i');
                        $updates['return_time_1'] = $solicitation->new_time_finish->format('H:i');
                    } else {
                        $updates['entry_time_2'] = $solicitation->new_time_start->format('H:i');
                        $updates['return_time_2'] = $solicitation->new_time_finish->format('H:i');
                    }
                } else {
                    // Alteração de apenas um horário (não deveria acontecer com a nova regra, mas mantido para compatibilidade)
                    if ($solicitation->new_time_start) {
                        $updates['entry_time_1'] = $solicitation->new_time_start->format('H:i');
                    }
                    if ($solicitation->new_time_finish) {
                        $updates['return_time_2'] = $solicitation->new_time_finish->format('H:i');
                    }
                }

                // Aplicar as atualizações se houver
                if (!empty($updates)) {
                    $timeTracking->update($updates);

                    // Log das alterações aplicadas
                    Log::info('Horários atualizados no time tracking', [
                        'time_tracking_id' => $timeTracking->id,
                        'solicitation_id' => $solicitation->id,
                        'updates' => $updates,
                        'period_type' => isset($updates['entry_time_1']) ? 'morning' : 'afternoon',
                        'old_entry_time_1' => $solicitation->old_time_start?->format('H:i'),
                        'old_return_time_1' => $solicitation->old_time_finish?->format('H:i'),
                    ]);
                } else {
                    Log::info('Solicitação aprovada mas nenhum horário para atualizar', [
                        'solicitation_id' => $solicitation->id
                    ]);
                }
            } else {
                Log::warning('Solicitação aprovada mas time tracking não encontrado', [
                    'solicitation_id' => $solicitation->id,
                    'time_tracking_id' => $solicitation->time_tracking_id
                ]);
            }

            Log::info('Solicitação aprovada', [
                'solicitation_id' => $solicitation->id,
                'approved_by' => Auth::id(),
                'collaborator_id' => $solicitation->collaborator_id,
                'time_tracking_updated' => $timeTracking ? 'yes' : 'no'
            ]);

            return redirect()->back()->with('success', 'Solicitação aprovada com sucesso! Os horários foram atualizados no registro de ponto.');

        } catch (\Exception $e) {
            Log::error('Erro ao aprovar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Erro ao aprovar solicitação: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar uma solicitação
     */
    public function reject(Request $request, $id): RedirectResponse
    {
        $solicitation = SolicitationModel::findOrFail($id);

        try {
            // Verificar se a solicitação ainda está pendente
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Esta solicitação já foi processada.');
            }

            // Validar motivo da rejeição
            $request->validate([
                'admin_comment' => 'required|string|min:10'
            ], [
                'admin_comment.required' => 'O motivo da rejeição é obrigatório.',
                'admin_comment.min' => 'O motivo deve ter pelo menos 10 caracteres.'
            ]);

            // Atualizar status para rejeitado
            $solicitation->update([
                'status' => SolicitationStatusEnum::REJECTED,
                'admin_comment' => $request->input('admin_comment')
            ]);

            Log::info('Solicitação rejeitada', [
                'solicitation_id' => $solicitation->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->input('admin_comment')
            ]);

            return redirect()->back()->with('success', 'Solicitação rejeitada.');

        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao rejeitar solicitação.');
        }
    }

    /**
     * Cancelar uma solicitação (apenas pelo próprio colaborador)
     */
    public function cancel($id): RedirectResponse
    {
        $solicitation = SolicitationModel::findOrFail($id);

        try {
            // Verificar se a solicitação ainda está pendente
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Apenas solicitações pendentes podem ser canceladas.');
            }

            // Atualizar status para cancelado
            $solicitation->update([
                'status' => SolicitationStatusEnum::CANCELLED
            ]);

            Log::info('Solicitação cancelada', [
                'solicitation_id' => $solicitation->id,
                'cancelled_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Solicitação cancelada.');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao cancelar solicitação.');
        }
    }

    /**
     * Determina se a solicitação é para o período da manhã ou tarde
     * 
     * @param SolicitationModel $solicitation
     * @param TimeTrackingModel $timeTracking
     * @return bool True se for período da manhã, False se for período da tarde
     */
    private function isMorningPeriodUpdate($solicitation, $timeTracking): bool
    {
        // Se os horários antigos correspondem ao período da manhã
        if ($solicitation->old_time_start && $solicitation->old_time_finish) {
            $oldStart = $solicitation->old_time_start->format('H:i');
            $oldFinish = $solicitation->old_time_finish->format('H:i');
            
            $currentEntryTime1 = $timeTracking->entry_time_1 ? $timeTracking->entry_time_1->format('H:i') : null;
            $currentReturnTime1 = $timeTracking->return_time_1 ? $timeTracking->return_time_1->format('H:i') : null;
            
            // Se os horários antigos correspondem aos horários atuais do período da manhã
            if ($currentEntryTime1 === $oldStart && $currentReturnTime1 === $oldFinish) {
                return true; // Período da manhã
            }
        }
        
        // Se não corresponde ao período da manhã, assume que é período da tarde
        // ou usa heurística baseada no horário (antes das 14h = manhã)
        if ($solicitation->new_time_start) {
            return $solicitation->new_time_start->hour < 14;
        }
        
        // Fallback: se não conseguir determinar, assume período da manhã para horários antes das 12h
        return $solicitation->old_time_start ? $solicitation->old_time_start->hour < 12 : true;
    }
}
