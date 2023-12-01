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
            ->where('tgl_perjanjian', '=', date('Y-m-d'));
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
