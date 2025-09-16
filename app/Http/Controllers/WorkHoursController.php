<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    public function index()
    {
        return view('auth.registrations.work-hours.index');
    }
}
