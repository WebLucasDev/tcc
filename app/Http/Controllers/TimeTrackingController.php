<?php

namespace App\Http\Controllers;

use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;

class TimeTrackingController extends Controller
{
    public function index()
    {
        $collaborators = CollaboratorModel::with('position.department')->orderBy('name')->get();
        
        // Filtros de busca
        $search = request('search');
        $collaboratorFilter = request('collaborator_id');
        $sortBy = request('sort_by', 'date');
        $sortDirection = request('sort_direction', 'desc');
        
        // Query base
        $query = TimeTrackingModel::with('collaborator.position')
            ->where('date', '>=', now()->subDays(30));
        
        // Aplicar filtros
        if ($search) {
            $query->whereHas('collaborator', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        if ($collaboratorFilter) {
            $query->where('collaborator_id', $collaboratorFilter);
        }
        
        // Aplicar ordenação
        if ($sortBy === 'collaborator') {
            $query->join('collaborators', 'time_tracking.collaborator_id', '=', 'collaborators.id')
                  ->orderBy('collaborators.name', $sortDirection)
                  ->select('time_tracking.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        // Paginação
        $timeTrackings = $query->paginate(15)->withQueryString();

        // Calcular informações da paginação para a view
        $paginationInfo = [
            'start' => max(1, $timeTrackings->currentPage() - 5),
            'end' => min($timeTrackings->lastPage(), max(1, $timeTrackings->currentPage() - 5) + 9),
        ];
        $paginationInfo['start'] = max(1, $paginationInfo['end'] - 9);

        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Registro de Ponto', 'url' => null]
        ];

        // Se for uma requisição AJAX, retornar JSON
        if (request()->ajax()) {
            try {
                // Renderizar as partials
                $tableHtml = view('auth.time-management.time-tracking.partials.table', compact('timeTrackings'))->render();
                $paginationHtml = $timeTrackings->hasPages() 
                    ? view('auth.time-management.time-tracking.partials.pagination', [
                        'paginator' => $timeTrackings,
                        'paginationInfo' => $paginationInfo
                    ])->render() 
                    : '';

                return response()->json([
                    'success' => true,
                    'html' => $tableHtml,
                    'pagination' => $paginationHtml,
                    'message' => 'Dados carregados com sucesso'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar os dados: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('auth.time-management.time-tracking.index', compact('collaborators', 'timeTrackings', 'breadcrumbs', 'paginationInfo'));
    }

    public function store()
    {
        
    }
    
    public function update()
    {
        
    }

    public function destroy()
    {
        
    }

}
