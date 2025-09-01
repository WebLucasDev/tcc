<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SolicitationController extends Controller
{
    public function index()
    {

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Solicitações', 'url' => null]
        ];

        return view('auth.time-management.time-record.index', compact('breadcrumbs'));
    }
}
