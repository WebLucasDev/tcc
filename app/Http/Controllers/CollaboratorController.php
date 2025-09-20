<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\collaborators\CollaboratorStoreRequest;
use App\Http\Requests\web\registrations\collaborators\CollaboratorUpdateRequest;
use App\Models\CollaboratorModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\WorkHoursModel;
use App\Models\TimeTrackingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CollaboratorController extends Controller
{
    public function index(Request $request)
    {
        $query = CollaboratorModel::with(['position.department']);

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

        if ($request->filled('department_id')) {
            $query->whereHas('position', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'email', 'admission_date'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $collaborators = $query->paginate(10)->withQueryString();

        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::orderBy('name')->get();

        $allCollaborators = CollaboratorModel::with('position')->get();
        $withDepartment = $allCollaborators->filter(function ($collaborator) {
            return $collaborator->position && $collaborator->position->department_id;
        })->count();
        $withPosition = $allCollaborators->where('position_id', '!=', null)->count();

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaboradores', 'url' => null]
        ];

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
        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::with('department')->orderBy('name')->get();
        $workHours = WorkHoursModel::where('status', 'ativo')->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
            ['label' => 'Novo Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.create', compact('breadcrumbs', 'departments', 'positions', 'workHours'));
    }

    public function store(CollaboratorStoreRequest $request)
    {
        try {
            Log::info('Dados recebidos para criar colaborador:', $request->validated());

            $collaborator = CollaboratorModel::create([
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
                'cpf' => $request->validated()['cpf'],
                'admission_date' => $request->validated()['admission_date'],
                'phone' => $request->validated()['phone'],
                'zip_code' => $request->validated()['zip_code'],
                'street' => $request->validated()['street'],
                'number' => $request->validated()['number'],
                'neighborhood' => $request->validated()['neighborhood'],
                'position_id' => $request->validated()['position_id'],
                'work_hours_id' => $request->validated()['work_hours_id'],
                'password' => 'senha123',
                'status' => $request->validated()['status'],
            ]);

            Log::info('Colaborador criado com sucesso:', ['id' => $collaborator->id]);

            return redirect()->route('collaborator.index')
                ->with('success', 'Colaborador criado com sucesso!');
        } catch (\Exception $e) {
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

        $departments = DepartmentModel::orderBy('name')->get();
        $positions = PositionModel::with('department')->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
            ['label' => 'Editar Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.create', compact('collaborator', 'breadcrumbs', 'departments', 'positions', 'workHours'));
    }

    public function update(CollaboratorUpdateRequest $request, $id)
    {
        try {
            $collaborator = CollaboratorModel::findOrFail($id);

            $collaborator->update([
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
                'cpf' => $request->validated()['cpf'],
                'admission_date' => $request->validated()['admission_date'],
                'phone' => $request->validated()['phone'],
                'zip_code' => $request->validated()['zip_code'],
                'street' => $request->validated()['street'],
                'number' => $request->validated()['number'],
                'neighborhood' => $request->validated()['neighborhood'],
                'position_id' => $request->validated()['position_id'],
                'work_hours_id' => $request->validated()['work_hours_id'],
                'status' => $request->validated()['status'],
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

            // Verificar se o colaborador possui registros de ponto
            $hasTimeTracking = TimeTrackingModel::where('collaborator_id', $collaborator->id)->exists();

            if ($hasTimeTracking) {
                return redirect()->back()
                    ->with('error', 'Este colaborador não pode ser excluído pois possui registros de ponto no sistema. Para impedir o acesso, altere o status para "Inativo".');
            }

            $collaborator->delete();

            return redirect()->route('collaborator.index')
                ->with('success', 'Colaborador excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao excluir o colaborador. Tente novamente.']);
        }
    }
}
