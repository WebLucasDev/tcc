<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\registrations\departments\DepartmentStoreRequest;
use App\Http\Requests\web\registrations\departments\DepartmentUpdateRequest;
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

    public function store(DepartmentStoreRequest $request)
    {

    }

    public function update(DepartmentUpdateRequest $request)
    {

    }

    public function destroy()
    {

    }
}
