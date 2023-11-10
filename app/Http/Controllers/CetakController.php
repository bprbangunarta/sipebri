<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pekerjaan;
use App\Models\Pengajuan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CetakController extends Controller
{
    public function pengajuan(Request $request)
    {
        $kode = $request->query('pengajuan');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah', 'data_pengajuan.produk_kode', 'data_pengajuan.metode_rps', 'data_pengajuan.jangka_bunga', 'data_pengajuan.jangka_waktu')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            $data[0]->kd_pengajuan = $kode;

            //Hari
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            //Format Angka
            $format_angka = "Rp. " . number_format($data[0]->plafon, 0, ',', '.');
            $data[0]->rp_plafon = $format_angka;

            return view('cetak.pengajuan', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function permohonan_kredit(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            return view('cetak.layouts.permohonan_kredit', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function nik(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            return view('cetak.layouts.nik', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function pendamping(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select(
                    'data_pendamping.no_identitas',
                    'data_pendamping.nama_pendamping',
                    'data_pendamping.tempat_lahir',
                    'data_pendamping.tanggal_lahir',
                    'data_pendamping.status',
                    'data_nasabah.no_identitas as iden',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir as tempat',
                    'data_nasabah.tanggal_lahir as ttl',
                    'data_nasabah.pendidikan_kode',
                    'data_nasabah.alamat_ktp'
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();


            //Rubah tanggal
            $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->tanggal_lahir);
            $data[0]->tanggal_lahir = $carbonDate->isoformat('D MMMM Y');
            $data[0]->tempat_lahir = ucfirst(strtolower($data[0]->tempat_lahir));

            //data pekerjaan
            $job = Pendidikan::where('kode_pendidikan', $data[0]->pendidikan_kode)->get();
            $data[0]->pendidikan_kode = $job[0]->nama_pendidikan;

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('dddd D MMMM YYYY');

            return view('cetak.layouts.pendamping', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function motor(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('ja_kendaraan', 'data_jaminan.jenis_agunan_kode', '=', 'ja_kendaraan.kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', $enc)
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Bermotor Roda 2');
                })->get();

            if (count($data) == 0) {
                return redirect()->back()->with('error', 'Jaminan kendaraan tidak ada');
            }

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }

            // $surveyor = DB::table('v_users')
            //     ->where('code_user', $data[0]->surveyor_kode)
            //     ->select('nama_user', 'role_name')->get();

            // //Tahun
            // $thn = Carbon::now()->year;
            // $data[0]->thn = $thn;

            // $data[0]->nama_user = $surveyor[0]->nama_user;
            // $data[0]->role_name = $surveyor[0]->role_name;
            // dd($data);
            return view('cetak.layouts.motor', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function tanah(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);

            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('ja_tanah', 'data_jaminan.jenis_agunan_kode', '=', 'ja_tanah.kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                    'ja_tanah.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', $enc)
                        ->where('data_jaminan.jenis_jaminan', '=', 'Tanah');
                })->get();
            // ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }

            // //Surveyor
            // $surveyor = DB::table('v_users')
            //     ->where('code_user', $data[0]->surveyor_kode)
            //     ->select('nama_user', 'role_name')->get();

            // $data[0]->nama_user = $surveyor[0]->nama_user;
            // $data[0]->role_name = $surveyor[0]->role_name;

            // //Tahun
            // $thn = Carbon::now()->year;
            // $data[0]->thn = $thn;
            // dd($data);
            return view('cetak.layouts.tanah', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function mobil(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('ja_kendaraan', 'data_jaminan.jenis_agunan_kode', '=', 'ja_kendaraan.kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', $enc)
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Bermotor Roda 4');
                })->get();

            if (count($data) == 0) {
                return redirect()->back()->with('error', 'Jaminan kendaraan tidak ada');
            }

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }


            return view('cetak.layouts.mobil', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_pengajuan(Request $request)
    {
        $name = request('name');
        $usr = Auth::user()->code_user;

        //Cek Role User
        $role = DB::table('v_users')->select('v_users.role_name')->where('code_user', $usr)->get();

        $query = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->select(
                'data_pengajuan.kode_pengajuan as kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.nasabah_kode as kd_nasabah',
                'data_pengajuan.id as id',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.jangka_waktu as jk',
                'data_nasabah.nama_nasabah as nama',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_nasabah.no_telp',
                'data_nasabah.alamat_ktp as alamat',
                'data_pengajuan.status',
                'data_pengajuan.tracking',
                'data_pengajuan.kategori',
                'data_nasabah.is_entry as entry',
                'data_kantor.nama_kantor',
                'data_survei.kantor_kode as kantor',
                'data_pengajuan.created_at as tanggal'
            )
            ->where('data_pengajuan.status', 'Sudah Otorisasi')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', 'Lengkapi Data')
                    ->where('data_pengajuan.status', 'Lengkapi Data')
                    ->orWhere('data_pengajuan.status', 'Sudah Otorisasi')
                    ->orWhere('data_pengajuan.status', 'Minta Otorisasi');
            })
            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->orderBy('data_nasabah.created_at', 'ASC');
        //

        if ($role[0]->role_name == 'Customer Service') {
            $query->where('data_pengajuan.input_user', '=', $usr);
        } elseif ($role[0]->role_name == 'Head Teller') {
            $query->where('data_pengajuan.status', '=', 'Minta Otorisasi');
        } elseif ($role[0]->role_name == 'Kepala Kantor Kas') {
            $query->where('data_pengajuan.input_user', '=', $usr);
        }

        $pengajuan = $query->paginate(7);
        $auth = Auth::user()->code_user;
        $dtu = DB::table('v_users')->where('code_user', $auth)->first();

        foreach ($pengajuan as $item) {
            $item->kd_nasabah = Crypt::encrypt($item->kd_nasabah);
            $item->kd = Crypt::encrypt($item->kode);
            $item->user = $dtu->role_name;
        }
        return view('cetak.pengajuan.index', [
            'data' => $pengajuan,
            'auth' => $auth,
        ]);
    }
}
