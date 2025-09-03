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
        // Validação dos dados
        $validated = request()->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'tracking_type' => 'required|in:entry_time_1,return_time_1,entry_time_2,return_time_2',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'time_observation' => 'nullable|string|max:30'
        ]);

        try {
            // Verificar se já existe um registro para este colaborador na data
            $existingRecord = TimeTrackingModel::where('collaborator_id', $validated['collaborator_id'])
                ->where('date', $validated['date'])
                ->first();

            // Validar se o tipo de registro já foi realizado
            if ($existingRecord && !is_null($existingRecord->{$validated['tracking_type']})) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Este tipo de registro já foi realizado para este colaborador na data selecionada. Use a função de editar para alterar.');
            }

            // Validar ordem cronológica dos horários
            if ($existingRecord) {
                $newTime = $validated['time'];
                $trackingType = $validated['tracking_type'];

                // Definir a ordem correta dos tipos de registro
                $timeOrder = ['entry_time_1', 'return_time_1', 'entry_time_2', 'return_time_2'];
                $currentIndex = array_search($trackingType, $timeOrder);

                // Verificar horários anteriores (devem ser menores que o atual)
                for ($i = 0; $i < $currentIndex; $i++) {
                    $previousType = $timeOrder[$i];
                    $previousTime = $existingRecord->{$previousType};

                    if ($previousTime && $newTime <= $previousTime) {
                        $typeNames = [
                            'entry_time_1' => 'Entrada (Manhã)',
                            'return_time_1' => 'Saída para Almoço',
                            'entry_time_2' => 'Volta do Almoço',
                            'return_time_2' => 'Saída (Final do Dia)'
                        ];

                        return redirect()->back()
                            ->withInput()
                            ->with('error', "O horário de {$typeNames[$trackingType]} ({$newTime}) deve ser posterior ao horário de {$typeNames[$previousType]} ({$previousTime}).");
                    }
                }

                // Verificar horários posteriores (devem ser maiores que o atual)
                for ($i = $currentIndex + 1; $i < count($timeOrder); $i++) {
                    $nextType = $timeOrder[$i];
                    $nextTime = $existingRecord->{$nextType};

                    if ($nextTime && $newTime >= $nextTime) {
                        $typeNames = [
                            'entry_time_1' => 'Entrada (Manhã)',
                            'return_time_1' => 'Saída para Almoço',
                            'entry_time_2' => 'Volta do Almoço',
                            'return_time_2' => 'Saída (Final do Dia)'
                        ];

                        return redirect()->back()
                            ->withInput()
                            ->with('error', "O horário de {$typeNames[$trackingType]} ({$newTime}) deve ser anterior ao horário de {$typeNames[$nextType]} ({$nextTime}).");
                    }
                }
            }

            // Definir o campo de observação baseado no tipo de registro
            $observationField = $validated['tracking_type'] . '_observation';

            if ($existingRecord) {
                // Atualizar registro existente
                $updateData = [
                    $validated['tracking_type'] => $validated['time']
                ];

                // Adicionar observação específica se fornecida
                if ($validated['time_observation']) {
                    $updateData[$observationField] = $validated['time_observation'];
                }

                $existingRecord->update($updateData);

                $message = 'Registro de ponto atualizado com sucesso!';
                $timeTracking = $existingRecord;
            } else {
                // Criar novo registro
                $createData = [
                    'collaborator_id' => $validated['collaborator_id'],
                    'date' => $validated['date'],
                    $validated['tracking_type'] => $validated['time']
                ];

                // Adicionar observação específica se fornecida
                if ($validated['time_observation']) {
                    $createData[$observationField] = $validated['time_observation'];
                }

                $timeTracking = TimeTrackingModel::create($createData);

                $message = 'Ponto registrado com sucesso!';
            }            return redirect()->route('time-tracking.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao registrar ponto: ' . $e->getMessage());
        }
    }

    public function update()
    {
        
    }

    public function destroy()
    {

    }

}
