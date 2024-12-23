<?php

namespace App\Http\Controllers;

use App\Exports\DataBerkas;
use App\Models\Kantor;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BerkasController extends Controller
{
    // Kirim Berkas
    public function index()
    {
        $kantor = Kantor::get();
        return view('pengajuan.kirim-berkas.index', compact('kantor'));
    }


    public function simpan_berkas(Request $request)
    {
        try {
            if (empty($request->kode_pengajuan) ||  empty($request->dari_kantor) || empty($request->ke_kantor)) {
                return redirect()->back()->with('error', 'Data harus diisi semua.');
            }

            $validasi = Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->first();
            if ($validasi->tracking !== 'Penjadwalan') {
                return redirect()->back()->with('error', 'Tidak bisa melakukan pengiriman berkas.');
            }

            $cek = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            $data = [
                'pengajuan_kode' => $request->kode_pengajuan,
                'dari_kantor' => $request->dari_kantor,
                'ke_kantor' => $request->ke_kantor,
                'user_pengirim' => Auth::user()->code_user,
            ];

            if (is_null($cek)) {
                $data['tgl_kirim'] = now();
                $data['created_at'] = now();

                $insert = DB::table('data_berkas')->insert($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Berkas berhasil dikirim.');
                } else {
                    return redirect()->back()->with('error', 'Berkas gagal dikirim.');
                }
            } else {
                $data['updated_at'] = now();
                $update = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil diubah.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal diubah.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan, Hubungi IT');
        }
    }

    public function serah_terima()
    {
        // $kantor = Kantor::get();
        $user = DB::table('v_users')
            ->whereIn('role_name', ['Staff Analis', 'Kasi Analis'])
            ->where('is_active', 1)
            ->orderBy('v_users.role_name', 'ASC')
            ->get();
        //

        return view('pengajuan.kirim-berkas.serah_terima', compact('user'));
    }

    public function simpan_serah_terima(Request $request)
    {
        try {
            $cek = $request->validate([
                "kode_pengajuan" => "required",
                "user" => "required"
            ]);

            $cekData = Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->first();

            if (empty($cekData)) {
                return redirect()->back()->with('error', 'Kode pengajuan tidak ada.');
            }

            $kantor = DB::table('v_users')->where('code_user', Auth::user()->code_user)->pluck('kantor_kode')->first();

            $data = [
                'pengajuan_kode' => $request->kode_pengajuan,
                'dari_kantor' => $kantor,
                'ke_kantor' => $kantor,
                'user_pengirim' => Auth::user()->code_user,
                'user_tujuan' => $request->user,
                'tgl_kirim' => now(),
                'created_at' => now(),
            ];

            $cek = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->first();

            if (is_null($cek)) {
                $insert = DB::table('data_berkas')->insert($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Data berhasil disimpan.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal disimpan.');
                }
            } else {
                $update = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil diubah.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal diubah.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan, Hubungi IT.');
        }
    }

    // Terima Berkas
    public function terima_berkas()
    {
        try {
            $keyword = request('keyword');
            $role = DB::table('v_users')->where('code_user', Auth::user()->code_user)->pluck('role_name')->first();

            $data = DB::table('data_berkas')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_berkas.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('v_users', 'v_users.code_user', '=', 'data_berkas.user_pengirim')
                ->select(
                    'data_berkas.tgl_kirim as tanggal',
                    'data_berkas.tgl_terima',
                    'data_berkas.user_tujuan',
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.plafon',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'v_users.nama_user',
                )

                ->where(function ($query) use ($role) {
                    if ($role == 'Staff Analis' || $role == 'Kasi Analis') {
                        $query->whereNull('data_berkas.tgl_terima')
                            ->where('data_berkas.user_tujuan', '=', Auth::user()->code_user);
                    } else {
                        $query->whereNull('data_berkas.tgl_terima')
                            ->whereNull('data_berkas.user_tujuan')
                            ->where('data_berkas.ke_kantor', Auth::user()->kantor_kode);
                    }
                })

                ->where(function ($query) use ($keyword) {
                    $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                        ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
                })
                ->orderBy('data_berkas.tgl_kirim', 'DESC')
                ->orderByRaw('data_berkas.tgl_terima IS NULL DESC')
                ->paginate(10);
            //

            return view('pengajuan.terima-berkas.index', compact('data'));
        } catch (\Throwable $th) {
        }
    }

    public function get_berkas()
    {
        $kode = request()->input('data');
        $data = DB::table('data_berkas')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_berkas.pengajuan_kode')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select(
                'data_berkas.tgl_kirim as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
            )
            ->where('data_berkas.pengajuan_kode', $kode)
            ->first();

        return response()->json($data);
    }

    public function simpan_terima_berkas(Request $request)
    {
        try {
            if (empty($request->kode_pengajuan)) {
                return redirect()->back()->with('error', 'Data harus diisi semua.');
            }

            $validasi = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->first();

            if (is_null($validasi)) {
                return redirect()->back()->with('error', 'Data tidak di temukan.');
            }

            $data = [
                'user_penerima' => Auth::user()->code_user,
                'tgl_terima' => now(),
            ];

            $update = DB::table('data_berkas')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Berkas berhasil diterima.');
            } else {
                return redirect()->back()->with('error', 'Berkas gagal diterima.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan, Hubungi IT.');
        }
    }

    public function laporan_data_berkas()
    {
        try {
            $keyword = request('keyword');
            $data = DB::table('data_berkas')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_berkas.pengajuan_kode')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('v_users as pengirim', 'pengirim.code_user', '=', 'data_berkas.user_pengirim')
                ->leftJoin('v_users as penerima', 'penerima.code_user', '=', 'data_berkas.user_penerima')
                ->leftJoin('v_users as tujuan', 'tujuan.code_user', '=', 'data_berkas.user_tujuan')
                ->select(
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'pengirim.nama_user as user_pengirim',
                    'penerima.nama_user as user_penerima',
                    'tujuan.nama_user as users_tujuan',
                    'data_berkas.user_tujuan',
                    'data_berkas.dari_kantor',
                    'data_berkas.ke_kantor',
                    'data_berkas.tgl_kirim',
                    'data_berkas.tgl_terima',
                )
                ->where(function ($query) use ($keyword) {
                    $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                        ->orWhere('pengirim.nama_user', 'like', '%' . $keyword . '%')
                        ->orWhere('penerima.nama_user', 'like', '%' . $keyword . '%');
                })
                ->orderBy('data_berkas.tgl_kirim', 'DESC')
                ->paginate(10);
            //

            //Data Kantor
            $kantor = DB::table('data_kantor')->get();
            //Data Produk
            $produk = DB::table('data_produk')->get();

            return view('laporan.data_berkas', compact('data', 'kantor', 'produk'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dimuat, Hubungi IT.');
        }
    }

    public function export_data_berkas()
    {
        try {
            if (empty(request('tgl_kirim')) && !empty(request('tgl_kirim_sampai'))) {
                return redirect()->back()->with('error', 'Tanggal kirim harus diisi.');
            }

            if (empty(request('tgl_terima')) && !empty(request('tgl_terima_sampai'))) {
                return redirect()->back()->with('error', 'Tanggal terima harus diisi.');
            }

            $filename = "Data Berkas.xlsx";
            return Excel::download(new DataBerkas, $filename);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal di export.');
        }
    }
}
