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
            $query->join('collaborators', 'time_trackings.collaborator_id', '=', 'collaborators.id')
                  ->orderBy('collaborators.name', $sortDirection)
                  ->select('time_trackings.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        // Paginação
        $timeTrackings = $query->paginate(15)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Registro de Ponto', 'url' => null]
        ];

        return view('auth.time-management.time-tracking.index', compact('collaborators', 'timeTrackings', 'breadcrumbs'));
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
