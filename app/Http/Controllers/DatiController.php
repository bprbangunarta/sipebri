<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatiController extends Controller
{
    public function kabupaten(Request $request){
        $name = $request->input('name');
        $wil = DB::select('select * from v_wilayah');                 
        $collection = collect($wil);
        $data = $collection->where('nama_dati', $name);        
                
        return response()->json($data);
    }
}
