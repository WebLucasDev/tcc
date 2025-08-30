<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\collaborators\CollaboratorStoreRequest;
use App\Http\Requests\web\registrations\collaborators\CollaboratorUpdateRequest;
use App\Models\CollaboratorModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            // Log dos dados recebidos para debug
            Log::info('Dados recebidos para criar colaborador:', $request->all());

            $collaborator = CollaboratorModel::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'admission_date' => $request->admission_date,
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'street' => $request->street,
                'number' => $request->number,
                'neighborhood' => $request->neighborhood,
                'position_id' => $request->position_id,
                'entry_time_1' => $request->entry_time_1,
                'return_time_1' => $request->return_time_1,
                'entry_time_2' => $request->entry_time_2,
                'return_time_2' => $request->return_time_2,
                'password' => 'senha123', // Senha padrão temporária
            ]);

            Log::info('Colaborador criado com sucesso:', ['id' => $collaborator->id]);

            return redirect()->route('collaborator.index')
                ->with('success', 'Colaborador criado com sucesso!');
        } catch (\Exception $e) {
            // Log do erro específico
            Log::error('Erro ao criar colaborador:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar o colaborador: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $collaborator = CollaboratorModel::findOrFail($id);

        // Departamentos e cargos para os selects
        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::with('department')->orderBy('name')->get();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
            ['label' => 'Editar Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.create', compact('collaborator', 'breadcrumbs', 'departments', 'positions'));
    }

    public function update(CollaboratorUpdateRequest $request, $id)
    {
        try {
            $collaborator = CollaboratorModel::findOrFail($id);

            $collaborator->update([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'admission_date' => $request->admission_date,
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'street' => $request->street,
                'number' => $request->number,
                'neighborhood' => $request->neighborhood,
                'position_id' => $request->position_id,
                'entry_time_1' => $request->entry_time_1,
                'return_time_1' => $request->return_time_1,
                'entry_time_2' => $request->entry_time_2,
                'return_time_2' => $request->return_time_2,
            ]);

            return redirect()->route('collaborator.index')
                ->with('success', 'Colaborador atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar o colaborador. Tente novamente.']);
        }
    }

    public function destroy($id)
    {
        try {
            $collaborator = CollaboratorModel::findOrFail($id);

            $collaborator->delete();

            return redirect()->route('collaborator.index')
                ->with('success', 'Colaborador excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao excluir o colaborador. Tente novamente.']);
        }
    }
}
