<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        return view('pengajuan.index');
    }

    public function edit(Request $request)
    {
        return view('pengajuan.edit');
    }
}
