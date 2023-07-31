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
            'kode_produk' => 'required',
            'nama_produk' => 'required',
        ]);

        $cek['kode_produk'] = strtoupper($cek['kode_produk']); //Kapital semua
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
            'kode_produk' => 'required',
            'nama_produk' => 'required',
            'rate' => 'required',
            'jumlah_pengajuan' => 'required',
        ]);
       
        $cek['kode_produk'] = strtoupper($cek['kode_produk']); //Kapital semua
        $cek['nama_produk'] = ucfirst($cek['nama_produk']); //kapital hanya depan saja
        $cek ['rate'] = 0;
        $cek ['jumlah_pengajuan'] = 0;
        
        if ($cek) {
            Produk::where('kode_produk', $produk)
                    ->update($cek);
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diubah']);
        }else{
            return response()->json(['status' => 'error', 'message' => 'Data gagal diubah']);
        }
    }
}
