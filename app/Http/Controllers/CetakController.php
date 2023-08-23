<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function pengajuan(Request $request){
        $kode = $request->query('pengajuan');
       
        return view('cetak.pengajuan',[
            'data' => $kode
        ]);
    }
}
