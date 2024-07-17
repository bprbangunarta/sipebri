<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use App\Models\Midle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPersetujuanController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

        if ($user->role_name == 'Staff Analis') {
            $cek = RSC::persetujuan_rsc_staff($keyword);
        } elseif ($user->role_name == 'Kasi Analis') {
            $cek = RSC::persetujuan_rsc_kasi($keyword);
        } elseif ($user->role_name == 'Kabag Analis') {
            $cek = RSC::persetujuan_rsc_kabag_analis($keyword);
        } elseif ($user->role_name == 'Direksi') {
            $cek = RSC::persetujuan_rsc_direksi($keyword);
        }

        $data = $cek->paginate(10);

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->plafon = $data_eks->plafond_awal;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
            $item->status_rsc = $item->status_rsc;
        }

        return view('rsc.persetujuan.index', [
            'data' => $data,
        ]);
    }

    public function informasi(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_persetujuan($enc_rsc);

            foreach ($data as $item) {
                $item->kode = Crypt::encrypt($item->kode_pengajuan);
                $item->rsc = Crypt::encrypt($item->kode_rsc);
                $item->status_rsc = $status_rsc;
            }

            return view('rsc.persetujuan.informasi', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function catatan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = RSC::get_data_persetujuan($enc_rsc);

            foreach ($data as $item) {
                $item->kode = Crypt::encrypt($item->kode_pengajuan);
                $item->rsc = Crypt::encrypt($item->kode_rsc);
            }

            $catatan = DB::table('rsc_data_usulan')->where('kode_rsc', $enc_rsc)->get();

            if ($catatan) {
                $note = [];
                foreach ($catatan as $item) {
                    $note[] = $item;
                }

                $catatan = RSC::catatan($note);
            }

            return view('rsc.persetujuan.catatan', [
                'data' => $data[0],
                'catatan' => $catatan
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function persetujuan_index(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = RSC::get_data_persetujuan($enc_rsc);

            foreach ($data as $item) {
                $item->kode = Crypt::encrypt($item->kode_pengajuan);
                $item->rsc = Crypt::encrypt($item->kode_rsc);
            }

            $rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $data[0]->kode_pengajuan)->where('kode_rsc', $enc_rsc)->first();
            $keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();

            $biaya = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();

            $user = DB::table('v_users')->where('code_user', Auth::user()->code_user)->first();

            return view('rsc.persetujuan.persetujuan', [
                'data' => $data[0],
                'pengusulan' => $rsc,
                'keuangan' => $keuangan,
                'biaya' => $biaya,
                'user' => $user,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function persetujuan_simpan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $role = DB::table('v_users')->where('code_user', Auth::user()->code_user)->first();

            $data = [
                'kode_rsc' => $enc_rsc,
                'role_name' => $role->role_name,
                'input_user' => Auth::user()->code_user,
                'administrasi' => $request->persen_adm,
                'administrasi_nominal' => $request->nominal_adm,
                'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh) ?? 0,
                'metode_rps' => $request->metode_rps,
                'suku_bunga' => $request->suku_bunga,
                'jangka_waktu' => $request->jangka_waktu,
                'jangka_bunga' => $request->jangka_bunga,
                'jangka_pokok' => $request->jangka_pokok,
                'rc' => str_replace(["%"], "", $request->rc),
                'usulan_plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->plafon) ?? 0,
                'catatan' => strtoupper($request->catatan),
                'angsuran_pokok' => (int)str_replace(["Rp.", " ", "."], "", $request->angsuran_pokok) ?? 0,
                'angsuran_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->angsuran_bunga) ?? 0,
                'total_angsuran' => (int)str_replace(["Rp.", " ", "."], "", $request->total_angsuran) ?? 0,
                'created_at' => now(),
            ];

            if ($role->role_name == 'Staff Analis') {
                $rl = 'Naik Kasi';
            } elseif ($role->role_name == 'Kasi Analis') {
                $rl = 'Komite I';
            }

            if ($role->role_name == 'Kabag Analis' && $request->status != '') {
                $rl = $request->status;
            } elseif ($role->role_name == 'Kabag Analis' && $request->status == '') {
                $rl = 'Komite II';
            }

            if ($role->role_name == 'Direksi' && $request->status != '') {
                $rl = $request->status;
            } elseif ($role->role_name == 'Direksi' && $request->status == '') {
                $rl = 'Notifikasi';
            }


            $data2 = [
                'metode_rps' => $request->metode_rps,
                'suku_bunga' => $request->suku_bunga,
                'jangka_waktu' => $request->jangka_waktu,
                'jangka_bunga' => $request->jangka_bunga,
                'jangka_pokok' => $request->jangka_pokok,
                'angsuran_pokok' => (int)str_replace(["Rp.", " ", "."], "", $request->angsuran_pokok) ?? 0,
                'angsuran_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->angsuran_bunga) ?? 0,
                'total_angsuran' => (int)str_replace(["Rp.", " ", "."], "", $request->total_angsuran) ?? 0,
                'penentuan_plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->plafon) ?? 0,
                'status' => $rl
            ];

            $cek_biaya_rsc = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();
            $jml = $cek_biaya_rsc->administrasi_nominal + $cek_biaya_rsc->asuransi_jiwa + $cek_biaya_rsc->asuransi_tlo + $cek_biaya_rsc->poko_dibayar + $cek_biaya_rsc->bunga_dibayar + $cek_biaya_rsc->denda_dibayar + $request->ujroh;

            $data3 = [
                'administrasi' => $request->persen_adm,
                'administrasi_nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->nominal_adm) ?? 0,
                'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh) ?? 0,
                'total' => (int)str_replace(["Rp.", " ", "."], "", $jml) ?? 0,
            ];

            $cek_usulan = DB::table('rsc_data_usulan')->where('kode_rsc', $enc_rsc)->where('input_user', Auth::user()->code_user)->first();
            if (is_null($cek_usulan)) {
                DB::transaction(function () use ($enc_rsc, $data, $data2, $data3) {
                    DB::table('rsc_data_usulan')->insert($data);
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->update($data3);
                });
            } else {
                DB::transaction(function () use ($enc_rsc, $data, $data2, $data3) {
                    DB::table('rsc_data_usulan')->where('kode_rsc', $enc_rsc)->where('input_user', Auth::user()->code_user)->update($data);
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->update($data3);
                });
            }


            return redirect()->route('rsc.persetujuan.index')->with('success', 'Berhasil menambahkan data.');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
