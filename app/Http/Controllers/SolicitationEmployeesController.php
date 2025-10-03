<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SolicitationEmployeesController extends Controller
{
    public function index()
    {
        return view('auth.system-for-employees.solicitation-employees.index');
    }
}
