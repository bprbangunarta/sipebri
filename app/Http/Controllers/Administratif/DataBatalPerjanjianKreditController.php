<?php

namespace App\Http\Controllers\Administratif;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DataBatalPerjanjianKreditController extends Controller
{
    public function index(Request $request)
    {

        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();
        $keyword = request('keyword');
        // dd($keyword);
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_penolakan', 'data_penolakan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')

            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.plafon',
                'data_pengajuan.updated_at',
                'data_pengajuan.kategori',
                'data_pengajuan.status',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'data_pengajuan.created_at as tgl_daftar',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.jangka_waktu as jk',
                'data_tracking.keputusan_komite as tanggal',
                'data_penolakan.no_penolakan',
                'data_penolakan.nomor as no_st',
                'data_penolakan.keterangan as ket_tolak',
            )
            ->where('data_pengajuan.status', '=', 'Dibatalkan')

            ->where(function ($query) use ($keyword) {
                $query->Where('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_tracking.keputusan_komite', 'desc');

        $alasan = DB::table('data_alasan_penolakan')->get();

        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        return view('administratif.data-batal-perjanjian-kredit.index', compact('data', 'alasan'));
    }
}
