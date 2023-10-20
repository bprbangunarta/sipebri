<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAnalisController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
}
