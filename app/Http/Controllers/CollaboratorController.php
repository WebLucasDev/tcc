<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\collaborators\CollaboratorStoreRequest;
use App\Http\Requests\web\registrations\collaborators\CollaboratorUpdateRequest;
use App\Models\CollaboratorModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function index(Request $request)
    {
        $query = CollaboratorModel::with(['position.department']);

        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('position.department', function ($dept) use ($search) {
                      $dept->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('position', function ($pos) use ($search) {
                      $pos->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por departamento (via position)
        if ($request->filled('department_id')) {
            $query->whereHas('position', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filtro por cargo
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'email', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $collaborators = $query->paginate(10)->withQueryString();

        // Departamentos e cargos para os filtros
        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::orderBy('name')->get();

        // Calcular estatísticas corretas do banco total (via position)
        $allCollaborators = CollaboratorModel::with('position')->get();
        $withDepartment = $allCollaborators->filter(function ($collaborator) {
            return $collaborator->position && $collaborator->position->department_id;
        })->count();
        $withPosition = $allCollaborators->where('position_id', '!=', null)->count();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaboradores', 'url' => null]
        ];

        // Se for uma requisição AJAX, retornar apenas os dados necessários
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('auth.registrations.collaborators.partials.table', compact('collaborators'))->render(),
                'pagination' => view('auth.registrations.collaborators.partials.pagination', compact('collaborators'))->render(),
                'statistics' => [
                    'total' => $collaborators->total(),
                    'with_department' => $withDepartment,
                    'with_position' => $withPosition
                ]
            ]);
        }

        return view('auth.registrations.collaborators.index', compact('collaborators', 'departments', 'positions', 'breadcrumbs', 'withDepartment', 'withPosition'));
    }

    public function create()
    {
        // Departamentos e cargos para os selects
        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::with('department')->orderBy('name')->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
            ['label' => 'Novo Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.create', compact('breadcrumbs', 'departments', 'positions'));
    }

    public function store(CollaboratorStoreRequest $request)
    {

    }

    public function edit()
    {

    }

    public function update(CollaboratorUpdateRequest $request)
    {

    }

    public function destroy()
    {

    }
}
