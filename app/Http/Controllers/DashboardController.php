<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pengajuan = Pengajuan::count();
        $survei = Survei::where('surveyor_kode', '!=', null)->count();
        
        return view('dashboard', [
            'pengajuan' => $pengajuan,
            'survei' => $survei,
        ]);
    }
}
