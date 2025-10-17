<?php

namespace App\Http\Controllers;

use App\Enums\SolicitationStatusEnum;
use App\Models\SolicitationModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SolicitationController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Solicitações', 'url' => null],
        ];

        $solicitations = SolicitationModel::with(['collaborator', 'timeTracking']);

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

        if ($solicitations->currentPage() > $solicitations->lastPage() && $solicitations->lastPage() > 0) {
            if (! request()->ajax()) {
                return redirect()->route('solicitation.index', array_merge(request()->query(), ['page' => $solicitations->lastPage()]));
            }

            $queryParams = request()->query();
            $queryParams['page'] = $solicitations->lastPage();

            $solicitations = SolicitationModel::with(['collaborator', 'timeTracking']);

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

        $stats = [
            'pending' => SolicitationModel::where('status', 'pending')->count(),
            'approved' => SolicitationModel::where('status', 'approved')->count(),
            'rejected' => SolicitationModel::where('status', 'rejected')->count(),
            'cancelled' => SolicitationModel::where('status', 'cancelled')->count(),
        ];

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
                'resetPage' => request('page') && $solicitations->currentPage() != request('page'),
            ]);
        }

        return view('auth.time-management.solicitations.index', compact('breadcrumbs', 'solicitations', 'stats'));
    }

    public function approve(Request $request, $id): RedirectResponse
    {
        $solicitation = SolicitationModel::with('timeTracking')->findOrFail($id);

        try {
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Esta solicitação já foi processada.');
            }

            $solicitation->update([
                'status' => SolicitationStatusEnum::APPROVED,
                'admin_comment' => $request->input('admin_comment'),
            ]);

            $timeTracking = $solicitation->timeTracking;
            if ($timeTracking) {
                $updates = [];

                if ($solicitation->new_time_start && $solicitation->new_time_finish) {
                    $isMorningPeriod = $this->isMorningPeriodUpdate($solicitation, $timeTracking);

                    if ($isMorningPeriod) {
                        $updates['entry_time_1'] = $solicitation->new_time_start->format('H:i');
                        $updates['return_time_1'] = $solicitation->new_time_finish->format('H:i');
                    } else {
                        $updates['entry_time_2'] = $solicitation->new_time_start->format('H:i');
                        $updates['return_time_2'] = $solicitation->new_time_finish->format('H:i');
                    }
                } else {
                    if ($solicitation->new_time_start) {
                        $updates['entry_time_1'] = $solicitation->new_time_start->format('H:i');
                    }
                    if ($solicitation->new_time_finish) {
                        $updates['return_time_2'] = $solicitation->new_time_finish->format('H:i');
                    }
                }

                if (! empty($updates)) {
                    $timeTracking->update($updates);

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
                        'solicitation_id' => $solicitation->id,
                    ]);
                }
            } else {
                Log::warning('Solicitação aprovada mas time tracking não encontrado', [
                    'solicitation_id' => $solicitation->id,
                    'time_tracking_id' => $solicitation->time_tracking_id,
                ]);
            }

            Log::info('Solicitação aprovada', [
                'solicitation_id' => $solicitation->id,
                'approved_by' => Auth::id(),
                'collaborator_id' => $solicitation->collaborator_id,
                'time_tracking_updated' => $timeTracking ? 'yes' : 'no',
            ]);

            return redirect()->back()->with('success', 'Solicitação aprovada com sucesso! Os horários foram atualizados no registro de ponto.');

        } catch (\Exception $e) {
            Log::error('Erro ao aprovar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Erro ao aprovar solicitação: '.$e->getMessage());
        }
    }

    public function reject(Request $request, $id): RedirectResponse
    {
        $solicitation = SolicitationModel::findOrFail($id);

        try {
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Esta solicitação já foi processada.');
            }

            $request->validate([
                'admin_comment' => 'required|string|min:10',
            ], [
                'admin_comment.required' => 'O motivo da rejeição é obrigatório.',
                'admin_comment.min' => 'O motivo deve ter pelo menos 10 caracteres.',
            ]);

            $solicitation->update([
                'status' => SolicitationStatusEnum::REJECTED,
                'admin_comment' => $request->input('admin_comment'),
            ]);

            Log::info('Solicitação rejeitada', [
                'solicitation_id' => $solicitation->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->input('admin_comment'),
            ]);

            return redirect()->back()->with('success', 'Solicitação rejeitada.');

        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Erro ao rejeitar solicitação.');
        }
    }

    public function cancel($id): RedirectResponse
    {
        $solicitation = SolicitationModel::findOrFail($id);

        try {
            if ($solicitation->status->value !== 'pending') {
                return redirect()->back()->with('error', 'Apenas solicitações pendentes podem ser canceladas.');
            }

            $solicitation->update([
                'status' => SolicitationStatusEnum::CANCELLED,
            ]);

            Log::info('Solicitação cancelada', [
                'solicitation_id' => $solicitation->id,
                'cancelled_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Solicitação cancelada.');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar solicitação', [
                'solicitation_id' => $solicitation->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Erro ao cancelar solicitação.');
        }
    }

    private function isMorningPeriodUpdate($solicitation, $timeTracking): bool
    {
        if ($solicitation->old_time_start && $solicitation->old_time_finish) {
            $oldStart = $solicitation->old_time_start->format('H:i');
            $oldFinish = $solicitation->old_time_finish->format('H:i');

            $currentEntryTime1 = $timeTracking->entry_time_1 ? $timeTracking->entry_time_1->format('H:i') : null;
            $currentReturnTime1 = $timeTracking->return_time_1 ? $timeTracking->return_time_1->format('H:i') : null;

            if ($currentEntryTime1 === $oldStart && $currentReturnTime1 === $oldFinish) {
                return true;
            }
        }

        if ($solicitation->new_time_start) {
            return $solicitation->new_time_start->hour < 14;
        }

        return $solicitation->old_time_start ? $solicitation->old_time_start->hour < 12 : true;
    }
}
