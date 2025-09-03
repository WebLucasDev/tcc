<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SolicitationController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Solicitações', 'url' => null]
        ];

        return view('auth.time-management.solicitations.index', compact('breadcrumbs'));
    }
}
