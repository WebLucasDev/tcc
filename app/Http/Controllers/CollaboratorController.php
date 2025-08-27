<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\collaborators\CollaboratorStoreRequest;
use App\Http\Requests\web\registrations\collaborators\CollaboratorUpdateRequest;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function index()
    {
        return view('auth.registrations.collaborators.index');
    }

    public function create()
    {
        return view('auth.registrations.collaborators.create');
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
