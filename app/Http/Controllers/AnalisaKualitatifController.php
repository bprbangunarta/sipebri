<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaKualitatifController extends Controller
{
    public function karakter(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $karakter = DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->first();

            if (is_null($karakter)) {
                return view('staff.analisa.kualitatif.karakter', [
                    'data' => $cek[0],
                ]);
            }
            // dd($karakter);
            return view('staff.analisa.kualitatif.karakter-edit', [
                'data' => $cek[0],
                'karakter' => $karakter,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankarakter(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'pengajuan_kode' => $enc,
                'bi_checking' => $request->bi_checking,
                'kewajiban1' => $request->kewajiban1,
                'ket_kewajiban1' => $request->ket_kewajiban1,
                'status1' => $request->status1,
                'kewajiban2' => $request->kewajiban2,
                'ket_kewajiban2' => $request->ket_kewajiban2,
                'status2' => $request->status2,
                'kewajiban3' => $request->kewajiban3,
                'ket_kewajiban3' => $request->ket_kewajiban3,
                'status3' => $request->status3,
                'pihak_berwajib' => $request->pihak_berwajib,
                'hubungan_tetangga' => $request->hubungan_tetangga,
                'pengalaman_tki' => $request->pengalaman_tki,
                'ket_pengalaman' => $request->ket_pengalaman,
                'pemohon_ada' => $request->pemohon_ada,
                'pendamping_ada' => $request->pendamping_ada,
                'info_masyarakat' => $request->info_masyarakat,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            DB::table('au_kualitatif')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function updatekarakter(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'pengajuan_kode' => $enc,
                'bi_checking' => $request->bi_checking,
                'kewajiban1' => $request->kewajiban1,
                'ket_kewajiban1' => $request->ket_kewajiban1,
                'status1' => $request->status1,
                'kewajiban2' => $request->kewajiban2,
                'ket_kewajiban2' => $request->ket_kewajiban2,
                'status2' => $request->status2,
                'kewajiban3' => $request->kewajiban3,
                'ket_kewajiban3' => $request->ket_kewajiban3,
                'status3' => $request->status3,
                'pihak_berwajib' => $request->pihak_berwajib,
                'hubungan_tetangga' => $request->hubungan_tetangga,
                'pengalaman_tki' => $request->pengalaman_tki,
                'ket_pengalaman' => $request->ket_pengalaman,
                'pemohon_ada' => $request->pemohon_ada,
                'pendamping_ada' => $request->pendamping_ada,
                'info_masyarakat' => $request->info_masyarakat,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function usaha(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $data = DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->first();

            if (is_null($data)) {
                return redirect()->back()->with('error', 'Isi data karakter nasabah dahulu');
            }
            // dd($data);
            return view('staff.analisa.kualitatif.usaha', [
                'data' => $cek[0],
                'usaha' => $data,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function updateusaha(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $data = [
                'bahan_baku' => $request->bahan_baku,
                'proses_olah' => $request->proses_olah,
                'target_market' => $request->target_market,
                'pembayaran' => $request->pembayaran,
                'pendukung_usaha' => $request->pendukung_usaha,
                'pengurang_usaha' => $request->pengurang_usaha,
                'trade_checking' => $request->trade_checking,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->update($data);
            return redirect()->back()->with('success', 'Berhasil mengubah data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal mengubah data');
    }

    public function analisa_swot(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $data = DB::table('a_swot')->where('pengajuan_kode', $enc)->first();

            return view('staff.analisa.kualitatif.swot', [
                'data' => $cek[0],
                'analisa' => $data,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_analisa_swot(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = DB::table('a_swot')->where('pengajuan_kode', $enc)->first();
            if (is_null($cek)) {
                $data = [
                    'pengajuan_kode' => $enc,
                    'kekuatan' => strtoupper($request->kekuatan),
                    'kelemahan' => strtoupper($request->kelemahan),
                    'peluang' => strtoupper($request->peluang),
                    'ancaman' => strtoupper($request->ancaman),
                    'created_at' => now(),
                ];
                DB::table('a_swot')->insert($data);
                return redirect()->back()->with('success', 'Berhasil menambahkan data');
            } else {
                $data = [
                    'pengajuan_kode' => $enc,
                    'kekuatan' => strtoupper($request->kekuatan),
                    'kelemahan' => strtoupper($request->kelemahan),
                    'peluang' => strtoupper($request->peluang),
                    'ancaman' => strtoupper($request->ancaman),
                    'created_at' => now(),
                ];
                DB::table('a_swot')->where('pengajuan_kode', $enc)->update($data);
                return redirect()->back()->with('success', 'Berhasil menambahkan data');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function tambahan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            $data = DB::table('a_tambahan')->where('pengajuan_kode', $enc)->first();

            if (is_null($data)) {
                $data = (object) [
                    'checking_usaha' => null
                ];
            }

            return view('staff.analisa.kualitatif.tambahan', [
                'data' => $cek[0],
                'trade' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_tambahan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $data = [
                'pengajuan_kode' => $enc,
                'checking_usaha' => strtoupper($request->trade_checking_usaha),
            ];

            $cek = DB::table('a_tambahan')->where('pengajuan_kode', $enc)->first();

            if (is_null($cek)) {
                $data['created_at'] = now();

                $insert = DB::table('a_tambahan')->insert($data);

                if ($insert) {
                    return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal ditambahkan.');
                }
            } else {
                $data['updated_at'] = now();

                $update = DB::table('a_tambahan')->where('pengajuan_kode', $enc)->update($data);

                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil diubah.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal diubah.');
                }
            }
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'HUbungi Staff IT.');
        }
    }
}
