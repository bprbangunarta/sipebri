<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantor;

class KantorController extends Controller
{
    public function index(Request $request)
    {
        $query = Kantor::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('nama_kantor', 'like', '%' . $request->name . '%');
        }

        $kantor = $query->paginate(10);
        return view('master.kantor.index', compact('kantor'));
    }

    public function store(Request $request){
        $cek = $request->validate([
            'kode_kantor' => 'required',
            'nama_kantor' => 'required'
        ]);

        if ($cek) {
            Kantor::create($cek);
            toast('Your Post as been submited!','success');
            return redirect()->back();
        }else{
            return redirect()->back()->withErrors('error', 'Cek kembali data anda');
        }
    }
}
