<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\positions\PositionStoreRequest;
use App\Http\Requests\web\registrations\positions\PositionUpdateRequest;
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

        // Calcular estatísticas corretas do banco total
        $allPositions = PositionModel::all();
        $withDepartment = $allPositions->where('department_id', '!=', null)->count();
        $withoutDepartment = $allPositions->where('department_id', null)->count();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Cargos', 'url' => null]
        ];

        // Se for uma requisição AJAX, retornar apenas os dados necessários
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('auth.registrations.positions.partials.table', compact('positions'))->render(),
                'pagination' => view('auth.registrations.positions.partials.pagination', compact('positions'))->render(),
                'statistics' => [
                    'total' => $positions->total(),
                    'with_department' => $withDepartment,
                    'without_department' => $withoutDepartment
                ]
            ]);
        }

        return view('auth.registrations.positions.index', compact('positions', 'departments', 'breadcrumbs', 'withDepartment', 'withoutDepartment'));
    }

    public function create()
    {
        // Departamentos para o select
        $departments = DepartmentModel::orderBy('name')->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Cargos', 'url' => null],
            ['label' => 'Novo Cargo', 'url' => null],
        ];

        return view('auth.registrations.positions.create', compact('departments', 'breadcrumbs'));
    }

    public function store(PositionStoreRequest $request)
    {
        try {
            PositionModel::create([
                'name' => $request->name,
                'department_id' => $request->department_id ?: null,
            ]);

            return redirect()->route('position.index')
                ->with('success', 'Cargo criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar o cargo. Tente novamente.']);
        }
    }

    public function edit($id)
    {
        $position = PositionModel::findOrFail($id);

        // Departamentos para o select
        $departments = DepartmentModel::orderBy('name')->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Cargos', 'url' => route('position.index')],
            ['label' => 'Editar Cargo', 'url' => null],
        ];

        return view('auth.registrations.positions.create', compact('position', 'departments', 'breadcrumbs'));
    }

    public function update(PositionUpdateRequest $request, $id)
    {
        try {
            $position = PositionModel::findOrFail($id);

            $position->update([
                'name' => $request->name,
                'department_id' => $request->department_id ?: null,
            ]);

            return redirect()->route('position.index')
                ->with('success', 'Cargo atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar o cargo. Tente novamente.']);
        }
    }

    public function destroy($id)
    {
        try {
            $position = PositionModel::findOrFail($id);

            // Verificar se o cargo possui colaboradores vinculados
            if ($position->collaborators()->count() > 0) {
                return redirect()->back()
                    ->withErrors(['error' => 'Não é possível excluir este cargo pois existem colaboradores vinculados a ele.']);
            }

            $position->delete();

            return redirect()->route('position.index')
                ->with('success', 'Cargo excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao excluir o cargo. Tente novamente.']);
        }
    }
}
