<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompTimeEmployessController extends Controller
{
    public function index()
    {
        return view('auth.system-for-employees.comp-time-employees.index');
    }
}
