<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CollaboratorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('collaborator')->check()) {
            return redirect()->route('login.index')->with('error', 'Acesso negado. Faça login como colaborador.');
        }

        return $next($request);
    }
}
