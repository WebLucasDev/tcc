<?php

namespace App\Http\Controllers;

use App\Http\Requests\login\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /**
     * Exibe a tela de login
     */
    public function index()
    {
        return view('public.login.index');
    }

    public function auth(LoginRequest $request)
    {
        try {
            // Determina qual guard usar baseado no tipo de usuário
            if ($request->isCollaborator()) {
                // Tenta autenticar como colaborador
                $authenticated = Auth::guard('collaborator')->attempt([
                    'email' => $request['email'],
                    'password' => $request['password']
                ]);

                if (!$authenticated) {
                    return back()->withInput()->with('error', 'Credenciais inválidas.');
                }

                // Redireciona para o sistema de colaboradores
                return redirect()->route('system-for-employees.dashboard.index');

            } elseif ($request->isUser()) {
                // Tenta autenticar como usuário/gestor
                $authenticated = Auth::guard('web')->attempt([
                    'email' => $request['email'],
                    'password' => $request['password']
                ]);

                if (!$authenticated) {
                    return back()->withInput()->with('error', 'Credenciais inválidas.');
                }

                // Redireciona para o dashboard administrativo
                return redirect()->route('dashboard.index');

            } else {
                return back()->withInput()->with('error', 'Credenciais inválidas.');
            }
        }
        catch (Exception $exception) {
            return back()->withInput()->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    public function logout()
    {
        // Faz logout de ambos os guards para garantir que o usuário seja deslogado independente do tipo
        Auth::guard('web')->logout();
        Auth::guard('collaborator')->logout();

        return redirect()->route('login.index')->with('success', 'Logout realizado com sucesso!');
    }
}
