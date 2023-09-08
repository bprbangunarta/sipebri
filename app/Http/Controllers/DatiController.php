<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatiController extends Controller
{
    public function kabupaten(Request $request){
        $kode = $request->input('name');
        $wil = DB::table('v_dati')
                ->select('kecamatan')
                ->distinct()
                ->where('kode_dati', $kode)
                ->get();       
        return response()->json($wil);
    }

    public function kecamatan(Request $request){
        $kode = $request->input('name');
        $wil = DB::table('v_dati')
                ->select('kelurahan')
                ->distinct()
                ->where('kecamatan', $kode)
                ->get();
        return response()->json($wil);
    }
}
