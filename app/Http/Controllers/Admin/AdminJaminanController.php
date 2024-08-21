<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminJaminanController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $jaminan = DB::table('data_jaminan')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_jaminan.*',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_identitas',
            )
            ->where(function ($query) use ($keyword) {
                $query->where('data_jaminan.atas_nama', 'like', '%' . $keyword . '%')
                    ->orWhere('data_jaminan.pengajuan_kode', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_jaminan.atas_nama', 'ASC')
            ->paginate(10);
        //

        return view('master.jaminan.index', ['data' => $jaminan]);
    }

    public function edit($id)
    {
        $jaminan = DB::table('data_jaminan')->where('id', $id)->first();
        // dd($jaminan);
        return view('master.jaminan.edit', ['data' => $jaminan]);
    }

    public function update(Request $request)
    {
        try {
            if ($request->status_bl_nama == 'YES' && empty($request->jenis_bl_nama) && empty($request->an_bl_nama)) {
                return redirect()->back()->with('error', 'Jika status balik nama YES, maka field lainnya harus diisi.');
            }


            $data = [
                'atas_nama' => strtoupper($request->atas_nama),
                'lokasi' => strtoupper($request->lokasi),
                'no_dokumen' => strtoupper($request->no_dokumen),
                'catatan' => strtoupper($request->catatan),
                'on_current' => strtoupper($request->on_current),
            ];

            DB::table('data_jaminan')->where('id', $request->input('data'))->update($data);
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal diubah.');
        }
    }
}
