<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Exibe o Dashboard mostrando o usuário autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        return view('auth.dashboard.index', compact('user'));
    }
}
