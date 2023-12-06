<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Survei;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $pengajuan = Pengajuan::count();
        $disetujui = Pengajuan::where('status', 'Disetujui')->count();
        $penolakan = Pengajuan::where('status', 'Ditolak')->orWhere('status', 'Dibatalkan')->count();
        // $survei = Survei::where('surveyor_kode', '!=', null)->count();
        $survei = Pengajuan::where('status', 'Sudah Otorisasi')
            ->where(function ($query) {
                $query->orWhere('tracking', 'Proses Survei')
                    ->orWhere('tracking', 'Proses Analisa');
            })
            ->count();

        $query = DB::table('data_droping')
            ->where('tgl_perjanjian', '=', date('Y-m-d'))
            ->where('droping', '=', 1)

            ->where(function ($query) use ($keyword) {
                $query->where('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('wilayah', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('tgl_perjanjian', 'desc');
        //
        $dt = $query->get();
        // dd($dt);
        $tak = [];
        for ($i = 0; $i < count($dt); $i++) {
            $tak[] = $dt[$i]->plafon ?? 0;
        }
        $total = array_sum($tak);
        $realisasi = $query->paginate(10);

        return view('dashboard', [
            'pengajuan' => $pengajuan,
            'survei' => $survei,
            'disetujui' => $disetujui,
            'penolakan' => $penolakan,
            'data' => $realisasi,
            'total' => $total,
        ]);
    }
}
