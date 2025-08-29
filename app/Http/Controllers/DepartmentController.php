<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\departments\DepartmentStoreRequest;
use App\Http\Requests\web\registrations\departments\DepartmentUpdateRequest;
use App\Models\DepartmentModel;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DepartmentModel::with(['positions', 'collaborators']);

        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Adicionar contagem de relacionamentos
        $query->withCount(['positions', 'collaborators']);

        $departments = $query->paginate(10)->withQueryString();

        // Calcular estatísticas corretas do banco total
        $allDepartments = DepartmentModel::all();
        $withPositions = $allDepartments->filter(function($dept) {
            return $dept->positions()->count() > 0;
        })->count();
        $withCollaborators = $allDepartments->filter(function($dept) {
            return $dept->collaborators()->count() > 0;
        })->count();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Departamentos', 'url' => null]
        ];

        // Se for uma requisição AJAX, retornar apenas os dados necessários
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('auth.registrations.departments.partials.table', compact('departments'))->render(),
                'pagination' => view('auth.registrations.departments.partials.pagination', compact('departments'))->render(),
                'statistics' => [
                    'total' => $departments->total(),
                    'with_positions' => $withPositions,
                    'with_collaborators' => $withCollaborators
                ]
            ]);
        }

        return view('auth.registrations.departments.index', compact('departments', 'breadcrumbs', 'withPositions', 'withCollaborators'));
    }

    public function create()
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Departamentos', 'url' => null],
            ['label' => 'Novo Departamento', 'url' => null],
        ];

        return view('auth.registrations.departments.create', compact('breadcrumbs'));
    }

    public function store(DepartmentStoreRequest $request)
    {
        try {
            DepartmentModel::create([
                'name' => $request->name,
            ]);

            return redirect()->route('department.index')
                ->with('success', 'Departamento criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar o departamento. Tente novamente.']);
        }
    }

    public function edit($id)
    {
        $department = DepartmentModel::findOrFail($id);

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Departamentos', 'url' => route('department.index')],
            ['label' => 'Editar Departamento', 'url' => null],
        ];

        return view('auth.registrations.departments.create', compact('department', 'breadcrumbs'));
    }

    public function update(DepartmentUpdateRequest $request, $id)
    {
        try {
            $department = DepartmentModel::findOrFail($id);

            $department->update([
                'name' => $request->name,
            ]);

            return redirect()->route('department.index')
                ->with('success', 'Departamento atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar o departamento. Tente novamente.']);
        }
    }

    public function destroy($id)
    {
        try {
            $department = DepartmentModel::findOrFail($id);

            // Verificar se o departamento possui colaboradores vinculados
            if ($department->collaborators()->count() > 0) {
                return redirect()->back()
                    ->withErrors(['error' => 'Não é possível excluir este departamento pois existem colaboradores vinculados a ele.']);
            }

            $department->delete();

            return redirect()->route('department.index')
                ->with('success', 'Departamento excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao excluir o departamento. Tente novamente.']);
        }
    }
}
