<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tabungan;
use Illuminate\Http\Request;

use function Pest\Laravel\get;
use Illuminate\Support\Facades\DB;

class TabunganController extends Controller
{
    public function index()
    {
        $tabungan = Tabungan::paginate(10);

        foreach ($tabungan as $item) {
            $tgl_lahir = Carbon::createFromFormat('Ymd', $item->jttempoid)->format('d-m-Y');
            $item->tanggal_lahir = $tgl_lahir;
        }

        return view('tabungan', compact('tabungan'));
    }

    public function data_cif()
    {
        $keyword = request('keyword');
        $tabungan = Tabungan::where(function ($query) use ($keyword) {
            $query->where('nocif', 'like', '%' . $keyword . '%')
                ->orWhere('sname', 'like', '%' . $keyword . '%')
                ->orWhere('noid', 'like', '%' . $keyword . '%')
                ->orWhere('fname', 'like', '%' . $keyword . '%');
        })
            ->orderBy('inptgljam', 'desc')
            ->paginate(10);

        foreach ($tabungan as $item) {
            if (isset($item->jttempoid) && preg_match('/^\d{8}$/', $item->jttempoid)) {

                $year = substr($item->jttempoid, 0, 4);
                $month = substr($item->jttempoid, 4, 2);
                $day = substr($item->jttempoid, 6, 2);


                $tgl_lahir = sprintf('%02d-%02d-%04d', $day, $month, $year);

                $item->tanggal_lahir = $tgl_lahir;
            } else {
                $item->tanggal_lahir = null;
            }
        }

        return view('tabungan.tabungan', compact('tabungan'));
    }
}
