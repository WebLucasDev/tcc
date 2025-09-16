<?php

namespace App\Http\Controllers;

use App\Models\WorkHoursModel;
use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkHoursModel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'total_weekly_hours', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $workHours = $query->paginate(10)->withQueryString();

        $allWorkHours = WorkHoursModel::all();
        $activeCount = $allWorkHours->where('status->value', 'ativo')->count();
        $inactiveCount = $allWorkHours->where('status->value', 'inativo')->count();

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Jornadas de Trabalho', 'url' => null]
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('auth.registrations.work-hours.partials.table', compact('workHours'))->render(),
                'pagination' => view('auth.registrations.work-hours.partials.pagination', compact('workHours'))->render(),
                'statistics' => [
                    'total' => $workHours->total(),
                    'active' => $activeCount,
                    'inactive' => $inactiveCount
                ]
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
            'sunday' => 'Domingo'
        ];

        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Jornadas de Trabalho', 'url' => route('work-hours.index')],
            ['label' => 'Nova Jornada', 'url' => null],
        ];

        return view('auth.registrations.work-hours.create', compact('breadcrumbs', 'days'));
    }

    public function store()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }

}
