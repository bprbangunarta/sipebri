<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MemorandumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            $rc = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();

            if (is_null($rc)) {
                return redirect()->back()->with('error', 'RC tidak boleh kosong');
            }

            //cek data taksasi ada atau tidak
            if (count($taksasi) == 0) {
                return redirect()->back()->with('error', 'Data taksasi tidak boleh kosong');
            }

            $tak = [];
            for ($j = 0; $j < count($taksasi); $j++) {
                $tak[] = $taksasi[$j]->nilai_taksasi ?? 0;
            }
            //NIlai total taksasi
            $totaltaksasi = array_sum($tak);
            $plafonmax = ($totaltaksasi * 70) / 100;

            $data = [
                'max_plafon' => $plafonmax,
                'jangka_waktu' => $cek[0]->jangka_waktu,
                'rc' => $rc->rc,
                'total_taksasi' => $totaltaksasi,
            ];

            return view('analisa.memo-evaluasi-kredit', [
                'data' => $cek[0],
                'memorandum' => $data,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
