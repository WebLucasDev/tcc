<?php

namespace App\Http\Controllers;

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

    public function store()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
