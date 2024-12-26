<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->tahun ?: Carbon::now()->year;

        $data_realisasi = Pengajuan::whereYear('created_at', $currentYear)
            ->where('on_current', 1)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $data = Pengajuan::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Augustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $counts = array_fill(0, 12, 0);

        foreach ($data as $value) {
            $monthIndex = $value->month - 1;
            $counts[$monthIndex] = $value->count;
        }

        $count_realisasi = array_fill(0, 12, 0);
        foreach ($data_realisasi as $value) {
            $monthIndex = $value->month - 1;
            $count_realisasi[$monthIndex] = $value->count;
        }
        // dd($data, $counts);
        return view('chart.index', compact('months', 'counts', 'count_realisasi', 'currentYear'));
    }
}
