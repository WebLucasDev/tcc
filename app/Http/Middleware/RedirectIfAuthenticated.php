<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {

        $guards = empty($guards) ? ['user', 'collaborator'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                if ($guard === 'user') {
                    return redirect()->route('dashboard.index');
                }

                if ($guard === 'collaborator') {
                    return redirect()->route('system-for-employees.dashboard.index');
                }
            }
        }

        return $next($request);
    }
}
