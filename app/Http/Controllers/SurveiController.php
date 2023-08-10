<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\Nasabah;
use Illuminate\Http\Request;

class SurveiController extends Controller
{
    public function edit(Request $request)
    {
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();

        $kantor = Kantor::all();
        return view('survei.edit', [
            'data' => $cek,
            'kantor' => $kantor,
        ]);
    }
}
