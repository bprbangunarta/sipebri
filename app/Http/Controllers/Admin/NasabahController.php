<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $nasabah = DB::table('data_nasabah')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_nasabah.*',
                'users.name',
            )
            ->where(function ($query) use ($keyword) {
                $query->where('no_cif', 'like', '%' . $keyword . '%')
                    ->orWhere('no_identitas', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('kecamatan', 'like', '%' . $keyword . '%')
                    ->orWhere('kelurahan', 'like', '%' . $keyword . '%')
                    ->orWhere('kota', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_nasabah.nama_nasabah', 'ASC')
            ->paginate(10);

        return view('master.nasabah.index', ['data' => $nasabah]);
    }
}
