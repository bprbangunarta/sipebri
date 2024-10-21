<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ResortController extends Controller
{
    public function index()
    {
        try {
            $keyword = request('keyword');
            $data = DB::table('data_resort')
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('kode_resort', 'like', '%' . $keyword . '%')
                        ->orWhere('nama_resort', 'like', '%' . $keyword . '%');
                })
                ->orderBy('nama_resort', 'ASC')
                ->paginate(10);

            return view('master.resort.index', [
                'data' => $data
            ]);
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Data gagal dimuat.');
        }
    }

    public function simpan_resort(Request $request)
    {
        dd($request);
    }
}
