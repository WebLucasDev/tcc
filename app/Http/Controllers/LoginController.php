<?php

namespace App\Http\Controllers;

use App\Http\Requests\login\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('public.login.index');
    }

    public function auth(LoginRequest $request)
    {
        try {
            if ($request->isCollaborator()) {
                $authenticated = Auth::guard('collaborator')->attempt([
                    'email' => $request['email'],
                    'password' => $request['password'],
                ]);

                if (! $authenticated) {
                    return back()->withInput()->with('error', 'Credenciais inválidas.');
                }

                return redirect()->route('system-for-employees.dashboard.index');

            } elseif ($request->isUser()) {
                $authenticated = Auth::guard('user')->attempt([
                    'email' => $request['email'],
                    'password' => $request['password'],
                ]);

                if (! $authenticated) {
                    return back()->withInput()->with('error', 'Credenciais inválidas.');
                }

                return redirect()->route('dashboard.index');

            } else {
                return back()->withInput()->with('error', 'Credenciais inválidas.');
            }
        } catch (Exception $exception) {
            return back()->withInput()->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    public function logout()
    {
        Auth::guard('user')->logout();
        Auth::guard('collaborator')->logout();

        return redirect()->route('login.index')->with('success', 'Logout realizado com sucesso!');
    }
}
