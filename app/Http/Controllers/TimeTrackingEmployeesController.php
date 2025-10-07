<?php

namespace App\Http\Controllers;

use App\Models\TimeTrackingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeTrackingEmployeesController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user();

        // Busca os registros de ponto dos últimos 30 dias
        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->paginate(15);

        $paginationInfo = [
            'from' => $timeTrackings->firstItem(),
            'to' => $timeTrackings->lastItem(),
            'total' => $timeTrackings->total()
        ];

        return view('auth.system-for-employees.time-tracking-employees.index', compact('timeTrackings', 'paginationInfo', 'collaborator'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'time_observation' => 'nullable|string|max:30'
        ]);

        $collaborator = Auth::guard('collaborator')->user();

        // Usa a data e hora atual do servidor
        $currentDate = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        // Busca ou cria o registro do dia
        $tracking = TimeTrackingModel::firstOrCreate(
            [
                'collaborator_id' => $collaborator->id,
                'date' => $currentDate,
            ],
            [
                'status' => 'incompleto'
            ]
        );

        // Determina qual horário preencher
        if (empty($tracking->entry_time_1)) {
            $tracking->entry_time_1 = $currentTime;
            $tracking->entry_time_1_observation = $request->time_observation;
            $message = 'Entrada registrada com sucesso!';
        } elseif (empty($tracking->return_time_1)) {
            $tracking->return_time_1 = $currentTime;
            $tracking->return_time_1_observation = $request->time_observation;
            $message = 'Saída para almoço registrada com sucesso!';
        } elseif (empty($tracking->entry_time_2)) {
            $tracking->entry_time_2 = $currentTime;
            $tracking->entry_time_2_observation = $request->time_observation;
            $message = 'Retorno do almoço registrado com sucesso!';
        } elseif (empty($tracking->return_time_2)) {
            $tracking->return_time_2 = $currentTime;
            $tracking->return_time_2_observation = $request->time_observation;
            $tracking->status = 'completo';
            $message = 'Saída registrada com sucesso! Ponto completo.';
        } else {
            return redirect()->route('system-for-employees.time-tracking.index')
                ->with('error', 'Todos os horários já foram registrados para esta data.');
        }

        $tracking->save();

        return redirect()->route('system-for-employees.time-tracking.index')
            ->with('success', $message);
    }

    public function getNextTrackingInfo()
    {
        $collaborator = Auth::guard('collaborator')->user();
        $date = now()->format('Y-m-d'); // Sempre usa a data atual

        $tracking = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->where('date', $date)
            ->first();

        if (!$tracking || empty($tracking->entry_time_1)) {
            return response()->json([
                'next_type' => 'Entrada',
                'message' => 'Próximo registro: ENTRADA (Manhã)'
            ]);
        }

        if (empty($tracking->return_time_1)) {
            return response()->json([
                'next_type' => 'Saída para Almoço',
                'message' => 'Próximo registro: SAÍDA PARA ALMOÇO'
            ]);
        }

        if (empty($tracking->entry_time_2)) {
            return response()->json([
                'next_type' => 'Retorno do Almoço',
                'message' => 'Próximo registro: RETORNO DO ALMOÇO'
            ]);
        }

        if (empty($tracking->return_time_2)) {
            return response()->json([
                'next_type' => 'Saída',
                'message' => 'Próximo registro: SAÍDA (Tarde)'
            ]);
        }

        return response()->json([
            'next_type' => 'Completo',
            'message' => 'Todos os horários já foram registrados'
        ]);
    }

    public function show($id)
    {
        $collaborator = Auth::guard('collaborator')->user();

        $tracking = TimeTrackingModel::where('id', $id)
            ->where('collaborator_id', $collaborator->id)
            ->firstOrFail();

        return response()->json([
            'id' => $tracking->id,
            'date' => $tracking->date->format('Y-m-d'),
            'entry_time_1' => $tracking->entry_time_1,
            'entry_time_1_observation' => $tracking->entry_time_1_observation,
            'return_time_1' => $tracking->return_time_1,
            'return_time_1_observation' => $tracking->return_time_1_observation,
            'entry_time_2' => $tracking->entry_time_2,
            'entry_time_2_observation' => $tracking->entry_time_2_observation,
            'return_time_2' => $tracking->return_time_2,
            'return_time_2_observation' => $tracking->return_time_2_observation,
            'status' => $tracking->status
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:time_tracking,id',
            'entry_time_1' => 'required',
            'return_time_1' => 'nullable',
            'entry_time_2' => 'nullable',
            'return_time_2' => 'nullable',
            'entry_time_1_observation' => 'nullable|string|max:30',
            'return_time_1_observation' => 'nullable|string|max:30',
            'entry_time_2_observation' => 'nullable|string|max:30',
            'return_time_2_observation' => 'nullable|string|max:30'
        ]);

        $collaborator = Auth::guard('collaborator')->user();

        $tracking = TimeTrackingModel::where('id', $request->id)
            ->where('collaborator_id', $collaborator->id)
            ->firstOrFail();

        // Determinar status
        $status = 'incompleto';
        if ($request->entry_time_1 && $request->return_time_1 && $request->entry_time_2 && $request->return_time_2) {
            $status = 'completo';
        }

        $tracking->update([
            'entry_time_1' => $request->entry_time_1,
            'entry_time_1_observation' => $request->entry_time_1_observation,
            'return_time_1' => $request->return_time_1,
            'return_time_1_observation' => $request->return_time_1_observation,
            'entry_time_2' => $request->entry_time_2,
            'entry_time_2_observation' => $request->entry_time_2_observation,
            'return_time_2' => $request->return_time_2,
            'return_time_2_observation' => $request->return_time_2_observation,
            'status' => $status
        ]);

        return redirect()->route('system-for-employees.time-tracking.index')
            ->with('success', 'Registro atualizado com sucesso!');
    }

    public function cancel($id)
    {
        $collaborator = Auth::guard('collaborator')->user();

        $tracking = TimeTrackingModel::where('id', $id)
            ->where('collaborator_id', $collaborator->id)
            ->firstOrFail();

        if ($tracking->status === 'ausente') {
            return redirect()->route('system-for-employees.time-tracking.index')
                ->with('error', 'Este registro já está marcado como ausente.');
        }

        // Determinar qual foi o último horário registrado e cancelá-lo
        if (!empty($tracking->return_time_2)) {
            // Cancelar saída (tarde)
            $tracking->return_time_2 = null;
            $tracking->return_time_2_observation = null;
            $tracking->status = 'incompleto';
            $message = 'Saída (Tarde) cancelada com sucesso!';
        } elseif (!empty($tracking->entry_time_2)) {
            // Cancelar retorno do almoço
            $tracking->entry_time_2 = null;
            $tracking->entry_time_2_observation = null;
            $message = 'Retorno do Almoço cancelado com sucesso!';
        } elseif (!empty($tracking->return_time_1)) {
            // Cancelar saída para almoço
            $tracking->return_time_1 = null;
            $tracking->return_time_1_observation = null;
            $message = 'Saída para Almoço cancelada com sucesso!';
        } elseif (!empty($tracking->entry_time_1)) {
            // Cancelar entrada (manhã) - remove completamente o horário
            $tracking->entry_time_1 = null;
            $tracking->entry_time_1_observation = null;
            $tracking->status = 'ausente';
            $message = 'Entrada cancelada e registro marcado como ausente!';
        } else {
            return redirect()->route('system-for-employees.time-tracking.index')
                ->with('error', 'Não há registros para cancelar.');
        }

        $tracking->save();

        return redirect()->route('system-for-employees.time-tracking.index')
            ->with('success', $message);
    }

    public function restore($id)
    {
        $collaborator = Auth::guard('collaborator')->user();

        $tracking = TimeTrackingModel::where('id', $id)
            ->where('collaborator_id', $collaborator->id)
            ->firstOrFail();

        if ($tracking->status !== 'ausente') {
            return redirect()->route('system-for-employees.time-tracking.index')
                ->with('error', 'Este registro não está marcado como ausente.');
        }

        // Determinar o status correto ao restaurar
        $status = 'incompleto';
        if ($tracking->entry_time_1 && $tracking->return_time_1 && $tracking->entry_time_2 && $tracking->return_time_2) {
            $status = 'completo';
        }

        $tracking->update(['status' => $status]);

        return redirect()->route('system-for-employees.time-tracking.index')
            ->with('success', 'Registro restaurado com sucesso!');
    }
}

