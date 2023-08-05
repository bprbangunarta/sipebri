<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendampingController extends Controller
{
    public function edit(Request $request)
    {
        return view('pendamping.edit');
    }
}
