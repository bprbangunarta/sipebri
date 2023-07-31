<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('kode_produk', 'like', '%' . $request->name . '%');
        }

        $produk = $query->paginate(10);
        return view('master.produk.index', compact('produk'));
    }

    public function store(Request $request){

        $cek = $request->validate([
            'kode_produk' => 'required|max:3|unique:data_produk,kode_produk',
            'nama_produk' => 'required',
        ]);

        $cek['nama_produk'] = ucfirst($cek['nama_produk']); //kapital hanya depan saja

        $cek ['rate'] = 0;
        $cek ['jumlah_pengajuan'] = 0;

        if ($cek) {
            Produk::create($cek);
            toast('Your Post as been submited!','success');
            return redirect()->back();
        }else{
            return redirect()->back()->withErrors('error', 'Cek kembali data anda');
        }
    }

    public function edit($id){
        $data = Produk::where('kode_produk', $id)->get();
        return response()->json($data);
    }

    public function update(Request $request, $produk){
        
        $cek = $request->validate([
            'kode_produk' => 'required|max:3',
            'nama_produk' => 'required',
        ]);
       
        if(is_null($request->rate)){
            $cek ['rate'] = 0;
        }else{
           $cek ['rate'] = $request->rate;
        }

        if(is_null($request->jumlah_pengajuan)){
            $cek ['jumlah_pengajuan'] = 0;
        }else{
           $cek ['jumlah_pengajuan'] = $request->rate;
        }

        $cek['kode_produk'] = strtoupper($cek['kode_produk']); //Kapital semua
        $cek['nama_produk'] = ucfirst($cek['nama_produk']); //kapital hanya depan saja
        
        if ($cek) {
            $data = Produk::where('kode_produk', $produk)->get();
            Produk::where('id', $data[0]->id)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diubah');
        }else{
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }
    
    public function destroy($produk){
        $data = Produk::where('kode_produk', $produk)->get();
        if ($data) {
            Produk::destroy($data[0]->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
