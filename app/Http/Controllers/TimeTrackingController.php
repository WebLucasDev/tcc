<?php

namespace App\Http\Controllers;


use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;
use App\Enums\TimeTrackingActionEnum;
use App\Http\Requests\TimeManagement\TimeTracking\TimeTrackingStoreRequest;
use App\Http\Requests\TimeManagement\TimeTracking\TimeTrackingUpdateRequest;
use Illuminate\Support\Facades\Log;

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

            // Determinar automaticamente o próximo tipo de registro
            $nextTrackingType = $this->getNextTrackingType($existingRecord);

            // Se não há próximo tipo disponível, significa que todos os registros já foram feitos
            if (!$nextTrackingType) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Todos os registros de ponto já foram realizados para este colaborador na data selecionada.');
            }

            // Validar ordem cronológica dos horários
            if ($existingRecord) {
                $newTime = $validated['time']; // Formato H:i (ex: "10:10")

                // Definir a ordem correta dos tipos de registro
                $timeOrder = ['entry_time_1', 'return_time_1', 'entry_time_2', 'return_time_2'];
                $currentIndex = array_search($nextTrackingType, $timeOrder);

                // Verificar horários anteriores (devem ser menores que o atual)
                for ($i = 0; $i < $currentIndex; $i++) {
                    $previousType = $timeOrder[$i];
                    $previousTime = $existingRecord->{$previousType};

                    if ($previousTime) {
                        // Converter ambos para formato de comparação (apenas hora e minuto)
                        $previousTimeFormatted = \Carbon\Carbon::parse($previousTime)->format('H:i');

                        if ($newTime <= $previousTimeFormatted) {
                            $typeNames = [
                                'entry_time_1' => 'Entrada',
                                'return_time_1' => 'Saída',
                                'entry_time_2' => 'Entrada',
                                'return_time_2' => 'Saída'
                            ];

                            return redirect()->back()
                                ->withInput()
                                ->with('error', "O horário de {$typeNames[$nextTrackingType]} ({$newTime}) deve ser posterior ao horário de {$typeNames[$previousType]} ({$previousTimeFormatted}).");
                        }
                    }
                }
            }

            // Definir o campo de observação baseado no tipo de registro
            $observationField = $nextTrackingType . '_observation';

            if ($existingRecord) {
                // Atualizar registro existente
                $updateData = [
                    $nextTrackingType => $validated['time']
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
                    $nextTrackingType => $validated['time']
                ];

                // Adicionar observação específica se fornecida
                if ($validated['time_observation']) {
                    $createData[$observationField] = $validated['time_observation'];
                }

                $timeTracking = TimeTrackingModel::create($createData);

                $message = 'Ponto registrado com sucesso!';
            }

            return redirect()->route('time-tracking.index')
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
                            'entry_time_1' => 'Entrada',
                            'return_time_1' => 'Saída',
                            'entry_time_2' => 'Entrada',
                            'return_time_2' => 'Saída'
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
                            'entry_time_1' => 'Entrada',
                            'return_time_1' => 'Saída',
                            'entry_time_2' => 'Entrada',
                            'return_time_2' => 'Saída'
                        ];

                        return redirect()->back()
                            ->with('error', "O horário de {$typeNames[$timeSlotType]} ({$newTime}) deve ser anterior ao horário de {$typeNames[$nextType]} ({$nextTimeFormatted}).");
                    }
                }
            }

            // Preparar dados para atualização
            $updateData = [
                $timeSlotType => $newTime,
                'action' => TimeTrackingActionEnum::EDITED->value
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
                'entry_time_1' => 'Entrada',
                'return_time_1' => 'Saída',
                'entry_time_2' => 'Entrada',
                'return_time_2' => 'Saída'
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

    /**
     * Cancela um registro de ponto
     */
    public function cancel($id)
    {
        try {
            // Buscar o registro de ponto
            $timeTracking = TimeTrackingModel::with('collaborator')->findOrFail($id);

            Log::info('Cancelando registro:', [
                'id' => $id,
                'current_action' => $timeTracking->action,
                'collaborator' => $timeTracking->collaborator->name
            ]);

            // Verificar se o registro não está já cancelado
            if ($timeTracking->action === TimeTrackingActionEnum::CANCELLED->value) {
                return redirect()->back()
                    ->with('error', 'Este registro já está cancelado.');
            }

            // Atualizar o status para cancelado
            $timeTracking->update([
                'action' => TimeTrackingActionEnum::CANCELLED->value
            ]);

            Log::info('Registro cancelado com sucesso:', [
                'id' => $id,
                'new_action' => $timeTracking->fresh()->action
            ]);

            return redirect()->route('time-tracking.index')
                ->with('success', 'Registro de ponto cancelado com sucesso!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Registro não encontrado para cancelamento:', ['id' => $id]);
            return redirect()->back()
                ->with('error', 'Registro de ponto não encontrado.');
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar registro:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Erro ao cancelar registro: ' . $e->getMessage());
        }
    }

    /**
     * Restaura um registro de ponto cancelado
     */
    public function restore($id)
    {
        try {
            // Buscar o registro de ponto
            $timeTracking = TimeTrackingModel::with('collaborator')->findOrFail($id);

            // Verificar se o registro está cancelado
            if ($timeTracking->action !== TimeTrackingActionEnum::CANCELLED->value) {
                return redirect()->back()
                    ->with('error', 'Apenas registros cancelados podem ser restaurados.');
            }

            // Atualizar o status para restaurado
            $timeTracking->update([
                'action' => TimeTrackingActionEnum::RESTORED->value
            ]);

            return redirect()->route('time-tracking.index')
                ->with('success', 'Registro de ponto restaurado com sucesso!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Registro de ponto não encontrado.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao restaurar registro: ' . $e->getMessage());
        }
    }


    /**
     * Determina automaticamente o próximo tipo de registro baseado nos registros existentes
     */
    private function getNextTrackingType($existingRecord)
    {
        // Ordem dos tipos de registro
        $timeOrder = ['entry_time_1', 'return_time_1', 'entry_time_2', 'return_time_2'];

        // Se não existe registro, o próximo é o primeiro (entrada manhã)
        if (!$existingRecord) {
            return $timeOrder[0];
        }

        // Verificar qual é o próximo registro disponível
        foreach ($timeOrder as $type) {
            if (is_null($existingRecord->{$type})) {
                return $type;
            }
        }

        // Se todos os registros já foram feitos, retorna null
        return null;
    }

    /**
     * Retorna informações sobre o próximo registro para um colaborador em uma data específica
     */
    public function getNextTrackingInfo()
    {
        $collaboratorId = request('collaborator_id');
        $date = request('date', date('Y-m-d'));

        if (!$collaboratorId) {
            return response()->json([
                'next_type' => null,
                'next_type_name' => 'Selecione um colaborador'
            ]);
        }

        // Buscar registro existente
        $existingRecord = TimeTrackingModel::where('collaborator_id', $collaboratorId)
            ->where('date', $date)
            ->first();

        $nextType = $this->getNextTrackingType($existingRecord);

        $typeNames = [
            'entry_time_1' => 'Entrada',
            'return_time_1' => 'Saída',
            'entry_time_2' => 'Entrada',
            'return_time_2' => 'Saída'
        ];

        return response()->json([
            'next_type' => $nextType,
            'next_type_name' => $nextType ? $typeNames[$nextType] : 'Todos os registros completos'
        ]);
    }

}
