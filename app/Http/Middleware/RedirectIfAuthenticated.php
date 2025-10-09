<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Se não foram especificados guards, usa os dois principais
        $guards = empty($guards) ? ['user', 'collaborator'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Se for um gestor autenticado (guard 'user')
                if ($guard === 'user') {
                    return redirect()->route('dashboard.index');
                }

                // Se for um colaborador autenticado (guard 'collaborator')
                if ($guard === 'collaborator') {
                    return redirect()->route('system-for-employees.dashboard.index');
                }
            }
        }

        return $next($request);
    }
}
