<?php

namespace App\Http\Controllers;

use App\Models\DepartmentModel;
use App\Models\PositionModel;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = PositionModel::with(['department', 'collaborators']);

        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dept) use ($search) {
                      $dept->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por departamento
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Adicionar contagem de colaboradores
        $query->withCount('collaborators');

        $positions = $query->paginate(10)->withQueryString();

        // Departamentos para o filtro
        $departments = DepartmentModel::orderBy('name')->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Cargos', 'url' => null]
        ];

        return view('auth.registrations.positions.index', compact('positions', 'departments', 'breadcrumbs'));
    }
}
