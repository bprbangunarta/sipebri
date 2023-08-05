<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgunanController extends Controller
{
    public function index(Request $request)
    {
        return view('agunan.index');
    }

    public function edit(Request $request)
    {
        return view('agunan.edit');
    }
}
