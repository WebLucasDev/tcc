<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\timeManagement\timeTracking\TimeTrackingStoreRequest;
use App\Http\Requests\web\timeManagement\timeTracking\TimeTrackingUpdateRequest;
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

    public function store(TimeTrackingStoreRequest $request)
    {
        // Obter dados validados do FormRequest
        $validated = $request->validated();

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
                $newTime = $validated['time']; // Formato H:i (ex: "10:10")
                $trackingType = $validated['tracking_type'];

                // Definir a ordem correta dos tipos de registro
                $timeOrder = ['entry_time_1', 'return_time_1', 'entry_time_2', 'return_time_2'];
                $currentIndex = array_search($trackingType, $timeOrder);

                // Verificar horários anteriores (devem ser menores que o atual)
                for ($i = 0; $i < $currentIndex; $i++) {
                    $previousType = $timeOrder[$i];
                    $previousTime = $existingRecord->{$previousType};

                    if ($previousTime) {
                        // Converter ambos para formato de comparação (apenas hora e minuto)
                        $previousTimeFormatted = \Carbon\Carbon::parse($previousTime)->format('H:i');

                        if ($newTime <= $previousTimeFormatted) {
                            $typeNames = [
                                'entry_time_1' => 'Entrada (Manhã)',
                                'return_time_1' => 'Saída para Almoço',
                                'entry_time_2' => 'Volta do Almoço',
                                'return_time_2' => 'Saída (Final do Dia)'
                            ];

                            return redirect()->back()
                                ->withInput()
                                ->with('error', "O horário de {$typeNames[$trackingType]} ({$newTime}) deve ser posterior ao horário de {$typeNames[$previousType]} ({$previousTimeFormatted}).");
                        }
                    }
                }

                // Verificar horários posteriores (devem ser maiores que o atual)
                for ($i = $currentIndex + 1; $i < count($timeOrder); $i++) {
                    $nextType = $timeOrder[$i];
                    $nextTime = $existingRecord->{$nextType};

                    if ($nextTime) {
                        // Converter ambos para formato de comparação (apenas hora e minuto)
                        $nextTimeFormatted = \Carbon\Carbon::parse($nextTime)->format('H:i');

                        if ($newTime >= $nextTimeFormatted) {
                            $typeNames = [
                                'entry_time_1' => 'Entrada (Manhã)',
                                'return_time_1' => 'Saída para Almoço',
                                'entry_time_2' => 'Volta do Almoço',
                                'return_time_2' => 'Saída (Final do Dia)'
                            ];

                            return redirect()->back()
                                ->withInput()
                                ->with('error', "O horário de {$typeNames[$trackingType]} ({$newTime}) deve ser anterior ao horário de {$typeNames[$nextType]} ({$nextTimeFormatted}).");
                        }
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

    public function show($id)
    {
        try {
            // Buscar o registro com o colaborador
            $timeTracking = TimeTrackingModel::with('collaborator.position')
                ->findOrFail($id);

            // Preparar dados para retorno
            $data = [
                'id' => $timeTracking->id,
                'collaborator_id' => $timeTracking->collaborator_id,
                'collaborator_name' => $timeTracking->collaborator->name,
                'date' => $timeTracking->date,
                'entry_time_1' => $timeTracking->entry_time_1 ? \Carbon\Carbon::parse($timeTracking->entry_time_1)->format('H:i') : null,
                'return_time_1' => $timeTracking->return_time_1 ? \Carbon\Carbon::parse($timeTracking->return_time_1)->format('H:i') : null,
                'entry_time_2' => $timeTracking->entry_time_2 ? \Carbon\Carbon::parse($timeTracking->entry_time_2)->format('H:i') : null,
                'return_time_2' => $timeTracking->return_time_2 ? \Carbon\Carbon::parse($timeTracking->return_time_2)->format('H:i') : null,
                'entry_time_1_observation' => $timeTracking->entry_time_1_observation,
                'return_time_1_observation' => $timeTracking->return_time_1_observation,
                'entry_time_2_observation' => $timeTracking->entry_time_2_observation,
                'return_time_2_observation' => $timeTracking->return_time_2_observation,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de ponto não encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(TimeTrackingUpdateRequest $request)
    {
        // Obter dados validados do FormRequest
        $validated = $request->validated();

        try {
            // Buscar o registro de ponto
            $timeTracking = TimeTrackingModel::with('collaborator')->findOrFail($validated['tracking_id']);

            // Armazenar dados para validação cronológica
            $newTime = $validated['time'];
            $timeSlotType = $validated['time_slot_type'];
            $observationField = $timeSlotType . '_observation';

            // Validar ordem cronológica dos horários (similar ao store, mas excluindo o campo atual)
            $timeOrder = ['entry_time_1', 'return_time_1', 'entry_time_2', 'return_time_2'];
            $currentIndex = array_search($timeSlotType, $timeOrder);

            // Verificar horários anteriores (devem ser menores que o atual)
            for ($i = 0; $i < $currentIndex; $i++) {
                $previousType = $timeOrder[$i];
                $previousTime = $timeTracking->{$previousType};

                if ($previousTime) {
                    // Converter para formato de comparação (apenas hora e minuto)
                    $previousTimeFormatted = \Carbon\Carbon::parse($previousTime)->format('H:i');

                    if ($newTime <= $previousTimeFormatted) {
                        $typeNames = [
                            'entry_time_1' => 'Entrada (Manhã)',
                            'return_time_1' => 'Saída para Almoço',
                            'entry_time_2' => 'Volta do Almoço',
                            'return_time_2' => 'Saída (Final do Dia)'
                        ];

                        return redirect()->back()
                            ->with('error', "O horário de {$typeNames[$timeSlotType]} ({$newTime}) deve ser posterior ao horário de {$typeNames[$previousType]} ({$previousTimeFormatted}).");
                    }
                }
            }

            // Verificar horários posteriores (devem ser maiores que o atual)
            for ($i = $currentIndex + 1; $i < count($timeOrder); $i++) {
                $nextType = $timeOrder[$i];
                $nextTime = $timeTracking->{$nextType};

                if ($nextTime) {
                    // Converter para formato de comparação (apenas hora e minuto)
                    $nextTimeFormatted = \Carbon\Carbon::parse($nextTime)->format('H:i');

                    if ($newTime >= $nextTimeFormatted) {
                        $typeNames = [
                            'entry_time_1' => 'Entrada (Manhã)',
                            'return_time_1' => 'Saída para Almoço',
                            'entry_time_2' => 'Volta do Almoço',
                            'return_time_2' => 'Saída (Final do Dia)'
                        ];

                        return redirect()->back()
                            ->with('error', "O horário de {$typeNames[$timeSlotType]} ({$newTime}) deve ser anterior ao horário de {$typeNames[$nextType]} ({$nextTimeFormatted}).");
                    }
                }
            }

            // Preparar dados para atualização
            $updateData = [
                $timeSlotType => $newTime
            ];

            // Adicionar ou remover observação
            if (!empty($validated['observation'])) {
                $updateData[$observationField] = $validated['observation'];
            } else {
                // Se observação está vazia, definir como null para limpar
                $updateData[$observationField] = null;
            }

            // Atualizar registro
            $timeTracking->update($updateData);

            // Definir nomes amigáveis para resposta
            $typeNames = [
                'entry_time_1' => 'Entrada (Manhã)',
                'return_time_1' => 'Saída para Almoço',
                'entry_time_2' => 'Volta do Almoço',
                'return_time_2' => 'Saída (Final do Dia)'
            ];

            return redirect()->route('time-tracking.index')
                ->with('success', "Horário de {$typeNames[$timeSlotType]} atualizado com sucesso para {$newTime}!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Registro de ponto não encontrado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro interno do servidor: ' . $e->getMessage());
        }
    }

    public function destroy()
    {

    }

}
