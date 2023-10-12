<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;

class KomiteController extends Controller
{
    public function index()
    {
        $data = Pengajuan::where('tracking', 'Pengajuan')->paginate(5);
        // dd($data);
        return view('komite.index', [
            'data' => $data,
        ]);
    }
}
