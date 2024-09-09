<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanRealisasi;

class RSCExsportController extends Controller
{
    public function laporan_realisasi()
    {

        $fileName = 'Laporan Realisasi.xlsx';

        if (empty(request()->query('tgl1'))) {
            return redirect()->back()->with('error', 'Tanggal tidak boleh kosong.');
        }

        return Excel::download(new LaporanRealisasi, $fileName);
    }
}
