<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('auth.registrations.departments.index');
    }

    public function create()
    {
        return view('auth.registrations.departments.create');
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
