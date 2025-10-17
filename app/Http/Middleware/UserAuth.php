<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    public function handle(Request $request, Closure $next): Response
    {

        if (! Auth::guard('user')->check()) {
            return redirect()->route('login.index')->with('error', 'Acesso negado. Faça login como gestor.');
        }

        return $next($request);
    }
}
