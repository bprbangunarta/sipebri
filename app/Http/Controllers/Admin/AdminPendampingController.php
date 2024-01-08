<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminPendampingController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $pendamping = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_pendamping.nama_pendamping',
                'data_pendamping.status',
                'data_pendamping.input_user',
                'data_pendamping.status',
                'data_pendamping.no_identitas',
            )
            ->where(function ($query) use ($keyword) {
                $query->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pendamping.no_identitas', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pendamping.nama_pendamping', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_pendamping.nama_pendamping', 'ASC')
            ->paginate(10);

        return view('master.pendamping.index', ['data' => $pendamping]);
    }

    public function edit($kode)
    {
        $pendamping = DB::table('data_pendamping')
            ->join('users', 'users.code_user', '=', 'data_pendamping.input_user')
            ->select(
                'data_pendamping.*',
                'users.name',
            )
            ->where('data_pendamping.pengajuan_kode', $kode)
            ->first();
        //
        $pendamping->tanggal_lahir = Carbon::parse($pendamping->tanggal_lahir)->format('d-m-Y');

        return view('master.pendamping.edit', ['data' => $pendamping]);
    }
}
