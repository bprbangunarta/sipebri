<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DataProspek implements FromView
{
    public function view(): View
    {
        $tgl1 = request('tgl_prospek');
        $tgl2 = request('tgl_prospek_sampai') ?? request('tgl_prospek');

        $data = DB::table('data_prosfek')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_prosfek.code_user')
            ->select(
                'data_prosfek.*',
                DB::raw("DATE_FORMAT(data_prosfek.tgl_prosfek1, '%d/%m/%Y') as tgl_prosfek1"),
                DB::raw("DATE_FORMAT(data_prosfek.tgl_prosfek2, '%d/%m/%Y') as tgl_prosfek2"),
                DB::raw("DATE_FORMAT(data_prosfek.tgl_prosfek3, '%d/%m/%Y') as tgl_prosfek3"),
                'v_users.nama_user'
            )
            ->whereBetween('created_at', [$tgl1, $tgl2])->get();
        //

        return view('analisa.exports.data_prospek', compact('data'));
    }
}
