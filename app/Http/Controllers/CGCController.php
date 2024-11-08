<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CGCController extends Controller
{
    public function index()
    {
        try {
            $keyword = request()->query('keyword');

            $data = DB::table('data_tabungan')
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('noacc', 'like', '%' . $keyword . '%')
                        ->orWhere('nocif', 'like', '%' . $keyword . '%')
                        ->orWhere('fnama', 'like', '%' . $keyword . '%');
                })
                ->orderBy('fnama', 'ASC')
                ->paginate(10);

            return view('master.cgc.index', compact('data'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dimuat.');
        }
    }

    public function simpan(Request $request)
    {
        try {
            $cek = DB::connection('sqlsrv')->table('m_tabunganc')
                ->where('noacc', $request->noacc)
                ->where('nocif', $request->nocif)
                ->where('fnama', $request->fnama)
                ->first();

            if (is_null($cek)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $data = [
                'noacc' => $request->noacc,
                'nocif' => $request->nocif,
                'fnama' => $request->fnama,
            ];


            $validate = DB::table('data_tabungan')
                ->where('noacc', $request->noacc)
                ->where('nocif', $request->nocif)
                ->where('fnama', $request->fnama)
                ->first();

            if ($validate) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }


            $insert = DB::table('data_tabungan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil di tambahkan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dimuat.');
        }
    }
}
