<?php

namespace App\Http\Controllers;

use App\Models\CollaboratorModel;

class TimeTrackingController extends Controller
{
    public function index()
    {
        $collaborators = CollaboratorModel::with('position.department')->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Registro de Ponto', 'url' => null]
        ];

        return view('auth.time-management.time-tracking.index', compact('collaborators', 'breadcrumbs'));
    }

}
