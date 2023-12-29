<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantor;

use function Pest\Laravel\json;

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
            'kode_kantor' => 'required|unique:data_kantor,kode_kantor',
            'nama_kantor' => 'required'
        ]);

        $cek['kode_kantor'] = strtoupper($cek['kode_kantor']); //Kapital semua
        $cek['nama_kantor'] = ucfirst($cek['nama_kantor']); //kapital hanya depan saja

        if ($cek) {
            Kantor::create($cek);
            toast('Your Post as been submited!','success');
            return redirect()->back();
        }else{
            return redirect()->back()->withErrors('error', 'Cek kembali data anda');
        }
    }

    public function edit(Request $request, $id){
        $data = Kantor::where('kode_kantor', $id)->get();
        return response()->json($data);
    }

    public function update(Request $request){

        $cek = $request->validate([
            'kode_kantor' => 'required',
            'nama_kantor' => 'required',
        ]);

        $cek['kode_kantor'] = strtoupper($cek['kode_kantor']); //Kapital semua
        $cek['nama_kantor'] = ucfirst($cek['nama_kantor']); //kapital hanya depan saja

        if ($cek) {
            $data = Kantor::where('kode_kantor',$request->kode_kantor)->get();
            Kantor::where('id', $data[0]->id)
                    ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        }else{
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }

    public function destroy(Request $request, $kantor){
        $data = Kantor::where('kode_kantor', $kantor)->get();
        if ($data) {
            Kantor::destroy($data[0]->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
