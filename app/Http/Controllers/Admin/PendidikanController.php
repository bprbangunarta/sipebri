<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendidikan::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('nama_pendidikan', 'like', '%' . $request->name . '%')
                ->orderBy('kode_pendidikan', 'asc');
        }

        $query->where('nama_pendidikan', 'like', '%' . $request->name . '%')
            ->orderBy('kode_pendidikan', 'asc');

        $pendidikan = $query->paginate(10);
        return view('master.pendidikan.index', compact('pendidikan'));
    }

    public function store(Request $request)
    {
        $cek = $request->validate([
            'kode_pendidikan' => 'required|unique:data_pendidikan,kode_pendidikan',
            'nama_pendidikan' => 'required',
        ]);

        $cek['nama_pendidikan'] = strtoupper($cek['nama_pendidikan']); //Kapital semua
        if ($cek) {
            Pendidikan::create($cek);
            toast('Data berhasil ditambahkan', 'success');
            return redirect()->back();
        } else {
            return redirect()->back()->withErrors('error', 'Cek kembali data anda');
        }
    }

    public function edit($id)
    {
        $data = Pendidikan::where('kode_pendidikan', $id)->get();
        return response()->json($data);
    }

    public function update(Request $request)
    {

        $cek = $request->validate([
            'kode_pendidikan' => 'required',
            'nama_pendidikan' => 'required',
        ]);

        $cek['nama_pendidikan'] = ucfirst($cek['nama_pendidikan']); //kapital hanya depan saja

        if ($cek) {
            $data = Pendidikan::where('kode_pendidikan', $request->kode_pendidikan)->get();
            Pendidikan::where('id', $data[0]->id)
                ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }

    public function destroy(Request $request, $pendidikan)
    {
        $data = Pendidikan::where('kode_pendidikan', $pendidikan)->get();

        if ($data) {
            Pendidikan::destroy($data[0]->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
