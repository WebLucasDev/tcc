<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function auth(LoginRequest $request)
    {
        try {
            $authenticated = Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]);

            if(!$authenticated){
                return back()->withInput()->with('error', 'Credenciais inválidas.');
            }

            return redirect()->route('dashboard.index');
        }
        catch (Exception $exception) {
            return back()->withInput()->with('error', 'Credenciais inválidas.'. $exception);
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login.index')->with('success', 'Logout realizado com sucesso!');
    }
}
