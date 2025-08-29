<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\collaborators\CollaboratorStoreRequest;
use App\Http\Requests\web\registrations\collaborators\CollaboratorUpdateRequest;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function index()
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.index', compact('breadcrumbs'));
    }

    public function create()
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Cadastros', 'url' => null],
            ['label' => 'Colaborador', 'url' => null],
            ['label' => 'Novo Colaborador', 'url' => null],
        ];

        return view('auth.registrations.collaborators.create', compact('breadcrumbs'));
    }

    public function store(CollaboratorStoreRequest $request)
    {

    }

    public function update(CollaboratorUpdateRequest $request)
    {

    }

    public function destroy()
    {

    }
}
