<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardEmployessController extends Controller
{
    public function index()
    {
        return view('auth.system-for-employees.dashboard-employess.index');
    }
}
