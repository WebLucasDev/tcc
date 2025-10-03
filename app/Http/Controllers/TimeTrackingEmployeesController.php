<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeTrackingEmployeesController extends Controller
{
    public function index()
    {
        return view('auth.system-for-employees.time-tracking-employees.index');
    }
}
