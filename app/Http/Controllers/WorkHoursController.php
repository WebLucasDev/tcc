<?php

namespace App\Http\Controllers;

use App\Http\Requests\registrations\workHours\WorkHoursStoreRequest;
use App\Http\Requests\registrations\workHours\WorkHoursUpdateRequest;
use App\Models\WorkHoursModel;
use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkHoursModel::withCount('collaborators');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereRaw('status = ?', [$request->status]);
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'total_weekly_hours', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $workHours = $query->paginate(10)->withQueryString();

        $filteredQuery = WorkHoursModel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $filteredQuery->whereRaw('status = ?', [$request->status]);
        }

        $filteredWorkHours = $filteredQuery->get();
        $activeCount = $filteredWorkHours->filter(function ($workHour) {
            return $workHour->status->value === 'ativo';
        })->count();
        $inactiveCount = $filteredWorkHours->filter(function ($workHour) {
            return $workHour->status->value === 'inativo';
        })->count();

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Jornadas de Trabalho', 'url' => null],
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('auth.registrations.work-hours.partials.table', compact('workHours'))->render(),
                'pagination' => view('auth.registrations.work-hours.partials.pagination', compact('workHours'))->render(),
                'statistics' => [
                    'total' => $workHours->total(),
                    'active' => $activeCount,
                    'inactive' => $inactiveCount,
                ],
            ]);
        }

        return view('auth.registrations.work-hours.index', compact('workHours', 'breadcrumbs', 'activeCount', 'inactiveCount'));
    }

    public function create()
    {
        $days = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Jornadas de Trabalho', 'url' => route('work-hours.index')],
            ['label' => 'Nova Jornada', 'url' => null],
        ];

        return view('auth.registrations.work-hours.create', compact('breadcrumbs', 'days'));
    }

    public function store(WorkHoursStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {

                if (! isset($data[$day.'_active']) || ! $data[$day.'_active']) {
                    $data[$day.'_active'] = false;
                    $data[$day.'_entry_1'] = null;
                    $data[$day.'_exit_1'] = null;
                    $data[$day.'_entry_2'] = null;
                    $data[$day.'_exit_2'] = null;
                } else {
                    $data[$day.'_active'] = true;

                    if (empty($data[$day.'_entry_1']) || empty($data[$day.'_exit_1'])) {
                        $data[$day.'_entry_1'] = null;
                        $data[$day.'_exit_1'] = null;
                    }
                    if (empty($data[$day.'_entry_2']) || empty($data[$day.'_exit_2'])) {
                        $data[$day.'_entry_2'] = null;
                        $data[$day.'_exit_2'] = null;
                    }
                }
            }

            $workHour = WorkHoursModel::create($data);

            return redirect()
                ->route('work-hours.index')
                ->with('success', 'Jornada de trabalho criada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar jornada de trabalho. Tente novamente.');
        }
    }

    public function edit($id)
    {
        $workHour = WorkHoursModel::findOrFail($id);

        $days = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Jornadas de Trabalho', 'url' => route('work-hours.index')],
            ['label' => 'Editar Jornada', 'url' => null],
        ];

        return view('auth.registrations.work-hours.create', compact('breadcrumbs', 'days', 'workHour'));
    }

    public function update(WorkHoursUpdateRequest $request, $id)
    {
        try {
            $workHour = WorkHoursModel::findOrFail($id);
            $data = $request->validated();

            $existingWorkHour = WorkHoursModel::where('name', $data['name'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingWorkHour) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Já existe uma jornada com este nome.']);
            }

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {

                if (! isset($data[$day.'_active']) || ! $data[$day.'_active']) {
                    $data[$day.'_active'] = false;
                    $data[$day.'_entry_1'] = null;
                    $data[$day.'_exit_1'] = null;
                    $data[$day.'_entry_2'] = null;
                    $data[$day.'_exit_2'] = null;
                } else {
                    $data[$day.'_active'] = true;

                    if (empty($data[$day.'_entry_1']) || empty($data[$day.'_exit_1'])) {
                        $data[$day.'_entry_1'] = null;
                        $data[$day.'_exit_1'] = null;
                    }
                    if (empty($data[$day.'_entry_2']) || empty($data[$day.'_exit_2'])) {
                        $data[$day.'_entry_2'] = null;
                        $data[$day.'_exit_2'] = null;
                    }
                }
            }

            $workHour->update($data);

            return redirect()
                ->route('work-hours.index')
                ->with('success', 'Jornada de trabalho atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar jornada de trabalho. Tente novamente.');
        }
    }

    public function destroy($id)
    {
        try {
            $workHour = WorkHoursModel::findOrFail($id);

            if ($workHour->collaborators()->count() > 0) {
                return redirect()
                    ->route('work-hours.index')
                    ->with('error', 'Não é possível excluir esta jornada de trabalho pois ela está sendo utilizada por colaboradores.');
            }

            $workHour->delete();

            return redirect()
                ->route('work-hours.index')
                ->with('success', 'Jornada de trabalho excluída com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->route('work-hours.index')
                ->with('error', 'Erro ao excluir jornada de trabalho. Tente novamente.');
        }
    }
}
