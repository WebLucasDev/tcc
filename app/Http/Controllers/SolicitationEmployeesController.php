<?php

namespace App\Http\Controllers;

use App\Enums\SolicitationStatusEnum;
use App\Models\SolicitationModel;
use App\Models\TimeTrackingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitationEmployeesController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user();

        $solicitations = SolicitationModel::where('collaborator_id', $collaborator->id)
            ->with('timeTracking')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('auth.system-for-employees.solicitation-employees.index', compact('solicitations'));
    }

    public function create()
    {
        $collaborator = Auth::guard('collaborator')->user();

        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();

        return view('auth.system-for-employees.solicitation-employees.create', compact('timeTrackings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'time_tracking_id' => 'required|exists:time_tracking,id',
            'old_time_start' => 'nullable|date_format:H:i',
            'old_time_finish' => 'nullable|date_format:H:i',
            'new_time_start' => 'required|date_format:H:i',
            'new_time_finish' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
        ], [
            'time_tracking_id.required' => 'Selecione um registro de ponto',
            'time_tracking_id.exists' => 'Registro de ponto inválido',
            'new_time_start.required' => 'Informe o novo horário de entrada',
            'new_time_start.date_format' => 'Formato de horário inválido',
            'new_time_finish.required' => 'Informe o novo horário de saída',
            'new_time_finish.date_format' => 'Formato de horário inválido',
            'reason.required' => 'Informe o motivo da solicitação',
            'reason.max' => 'O motivo deve ter no máximo 500 caracteres',
        ]);

        $collaborator = Auth::guard('collaborator')->user();
        $timeTracking = TimeTrackingModel::findOrFail($request->time_tracking_id);

        if ($timeTracking->collaborator_id !== $collaborator->id) {
            return back()->withErrors(['error' => 'Você não tem permissão para criar solicitação para este registro']);
        }

        $date = \Carbon\Carbon::parse($timeTracking->date)->format('Y-m-d');

        SolicitationModel::create([
            'status' => SolicitationStatusEnum::PENDING,
            'old_time_start' => $request->old_time_start ? $date.' '.$request->old_time_start.':00' : null,
            'old_time_finish' => $request->old_time_finish ? $date.' '.$request->old_time_finish.':00' : null,
            'new_time_start' => $date.' '.$request->new_time_start.':00',
            'new_time_finish' => $date.' '.$request->new_time_finish.':00',
            'reason' => $request->reason,
            'time_tracking_id' => $request->time_tracking_id,
            'collaborator_id' => $collaborator->id,
        ]);

        return redirect()->route('system-for-employees.solicitation.index')
            ->with('success', 'Solicitação criada com sucesso! Aguarde a análise do administrador.');
    }

    public function show($id)
    {
        $collaborator = Auth::guard('collaborator')->user();

        $solicitation = SolicitationModel::where('id', $id)
            ->where('collaborator_id', $collaborator->id)
            ->with('timeTracking')
            ->firstOrFail();

        return response()->json($solicitation);
    }

    public function cancel($id)
    {
        $collaborator = Auth::guard('collaborator')->user();

        $solicitation = SolicitationModel::where('id', $id)
            ->where('collaborator_id', $collaborator->id)
            ->firstOrFail();

        if ($solicitation->status !== SolicitationStatusEnum::PENDING) {
            return back()->withErrors(['error' => 'Apenas solicitações pendentes podem ser canceladas']);
        }

        $solicitation->update(['status' => SolicitationStatusEnum::CANCELLED]);

        return back()->with('success', 'Solicitação cancelada com sucesso');
    }
}
