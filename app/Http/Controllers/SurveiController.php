<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SurveiController extends Controller
{
    public function edit(Request $request)
    {
        return view('survei.edit');
    }
}
