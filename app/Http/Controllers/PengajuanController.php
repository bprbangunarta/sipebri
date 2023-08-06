<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;

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

    public function agunan(Request $request)
    {
        return view('pengajuan.agunan');
    }
}
