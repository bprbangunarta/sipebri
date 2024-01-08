<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Pengajuan;

class AdminPengajuanController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $pengajuan = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_identitas',
            )
            ->where(function ($query) use ($keyword) {
                $query->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_identitas', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_nasabah.nama_nasabah', 'ASC')
            ->paginate(10);
        //
        // dd($pengajuan);
        return view('master.pengajuan.index', ['data' => $pengajuan]);
    }

    public function edit($kode)
    {
        $pengajuan = Pengajuan::where('kode_pengajuan', $kode)->first();
        // dd($pengajuan);
        return view('master.pengajuan.edit', ['data' => $pengajuan]);
    }

    public function update(Request $request)
    {
        try {

            $data = [
                'kategori' => strtoupper($request->kategori),
                'tracking' => $request->tracking,
                'status' => $request->status,
                'on_current' => $request->on_current,
            ];

            Pengajuan::where('kode_pengajuan', $request->input('data'))->update($data);
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal diubah.');
        }
    }
}
