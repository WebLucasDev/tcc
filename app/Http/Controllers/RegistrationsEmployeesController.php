<?php

namespace App\Http\Controllers;

use App\Http\Requests\forEmployees\registrations\RegistrationsEmployeesUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationsEmployeesController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user();
        $collaborator->load(['position', 'workHours']);

        return view('auth.system-for-employees.registrations-employees.index', compact('collaborator'));
    }

    public function update(RegistrationsEmployeesUpdateRequest $request)
    {
        try {
            $collaborator = Auth::guard('collaborator')->user();

            // Atualiza a senha (o mutator do model já faz o Hash::make automaticamente)
            $collaborator->password = $request['new_password'];
            $collaborator->save();

            return redirect()->route('system-for-employees.registrations.index')
                ->with('success', 'Senha alterada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao alterar senha. Tente novamente.');
        }
    }
}
