<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pekerjaan::query();
        $query->select('*');

        if (!empty($request->name)) {
            // $query->where('nama_pekerjaan', 'like', '%' . $request->name . '%');
             $query->where('nama_pekerjaan', 'like', '%' . $request->name . '%')
                ->orderBy('kode_pekerjaan', 'asc');
        }

        //Ascending berdasarkan Kode Pekerjaan
        $query->where('nama_pekerjaan', 'like', '%' . $request->name . '%')
                ->orderBy('kode_pekerjaan', 'asc');
                
        $pekerjaan = $query->paginate(10);
        return view('master.pekerjaan.index', compact('pekerjaan'));
    }

    public function store(Request $request){
         $cek = $request->validate([
            'kode_pekerjaan' => 'required|unique:data_pekerjaan,kode_pekerjaan',
            'nama_pekerjaan' => 'required'
        ]);

        $cek['nama_pekerjaan'] = ucfirst($cek['nama_pekerjaan']); //kapital hanya depan saja

        if ($cek) {
            Pekerjaan::create($cek);
            toast('Your Post as been submited!','success');
            return redirect()->back();
        }else{
            return redirect()->back()->withErrors('error', 'Cek kembali data anda');
        }
    }

    public function edit(Request $request, $id){
        $data = Pekerjaan::where('kode_pekerjaan', $id)->get();
        return response()->json($data);
    }

    public function update(Request $request){
    
        $cek = $request->validate([
            'kode_pekerjaan' => 'required',
            'nama_pekerjaan' => 'required'
        ]);

        $cek['nama_pekerjaan'] = ucfirst($cek['nama_pekerjaan']); //kapital hanya depan saja

        if ($cek) {
            $data = Pekerjaan::where('kode_pekerjaan', $request->kode_pekerjaan)->get();
            Pekerjaan::where('id', $data[0]->id)
                    ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diubah');
        }else{
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
        
    }

    public function destroy(Request $request, $job){
        $data = Pekerjaan::where('kode_pekerjaan', $job)->get();
        if ($data) {
            Pekerjaan::destroy($data[0]->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
