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
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_bunga',
                    'data_pengajuan.jangka_waktu'
                )
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
                ->leftJoin('data_pekerjaan', 'data_pekerjaan.kode_pekerjaan', '=', 'data_nasabah.pekerjaan_kode')
                ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('data_tabungan', 'data_pengajuan.tabungan_cgc', '=', 'data_tabungan.noacc')
                ->leftJoin('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.nama_panggilan',
                    'data_nasabah.no_identitas as no_identitas_n',
                    'data_nasabah.masa_identitas as masa_identitas_n',
                    'data_nasabah.kota as kota_n',
                    'data_nasabah.tanggal_lahir as ttl_n',
                    'data_nasabah.alamat_ktp as alamat_ktp_n',
                    'data_nasabah.kode_pos',
                    'data_nasabah.no_telp',
                    'data_nasabah.no_telp_darurat',
                    'data_nasabah.jenis_kelamin',
                    'data_nasabah.agama',
                    'data_nasabah.status_pernikahan',
                    'data_pekerjaan.nama_pekerjaan',
                    'data_nasabah.nama_ibu_kandung',
                    'data_pendamping.nama_pendamping',
                    'data_pendamping.status as status_pendamping',
                    'data_pendamping.no_hp',
                    'data_pendamping.no_identitas as no_identitas_p',
                    'data_pendamping.tempat_lahir as tempat_lahir_p',
                    'data_pendamping.tanggal_lahir as ttl_p',
                    'data_pendamping.masa_identitas as masa_identitas_p',
                    'users.name as surveyor',
                    'data_tabungan.noacc',
                    'data_tabungan.fnama',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.jangka_waktu',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.penggunaan',
                    'data_pengajuan.keterangan as penggunaan_ket',
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            $masa_identitas = Carbon::createFromFormat('Ymd', $data[0]->masa_identitas_n);
            $data[0]->masa_identitas_n = $masa_identitas->format('d/m/Y', $masa_identitas);

            $tgl_nasabah = Carbon::createFromFormat('Ymd', $data[0]->ttl_n);
            $data[0]->ttl_n = $tgl_nasabah->format('d/m/Y', $tgl_nasabah);

            $tgl_pendamping = Carbon::createFromFormat('Ymd', $data[0]->ttl_p);
            $data[0]->ttl_p = $tgl_pendamping->format('d/m/Y', $tgl_pendamping);

            $masa_identitasp = Carbon::createFromFormat('Ymd', $data[0]->masa_identitas_p);
            $data[0]->masa_identitas_p = $masa_identitasp->format('d/m/Y', $masa_identitasp);

            return view('cetak.layouts.permohonan_kredit', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function monitoring(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->join('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->join('v_users', 'data_survei.surveyor_kode', '=', 'v_users.code_user')
                ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
                ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
                ->select(
                    'data_pengajuan.*',
                    'data_pengajuan.created_at as tgl_pengajuan',
                    'data_pengajuan.input_user as input_user_pengajuan',
                    'data_nasabah.*',
                    'data_nasabah.created_at as tgl_nasabah',
                    'data_nasabah.photo as photo_nasabah',
                    'data_nasabah.input_user as input_user_nasabah',
                    'data_pendamping.nama_pendamping',
                    'v_users.nama_user as nama_surveyor',
                    'data_survei.*',
                    'data_survei.created_at as tgl_survei',
                    'data_survei.input_user as input_user_survei',
                    'data_kantor.nama_kantor',
                    'data_tracking.*',
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Kasi
            $kasi = DB::table('v_users')->where('code_user', $data[0]->kasi_kode)->first();
            $data[0]->nama_kasi = $kasi->nama_user;
            //Input User Pengajuan
            $up = DB::table('v_users')->where('code_user', $data[0]->input_user_pengajuan)->first();
            $data[0]->nama_cs = $up->nama_user;
            //Input User Nasabah
            $un = DB::table('v_users')->where('code_user', $data[0]->input_user_nasabah)->first();
            $data[0]->nama_input_nasabah = $un->nama_user;
            //Input User Survei
            $ks = DB::table('v_users')->where('code_user', $data[0]->input_user_survei)->first();
            $data[0]->nama_input_survei = $ks->nama_user;

            return view('cetak.layouts.monitoring', [
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
                ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah', 'data_pendamping.no_identitas as no_identitas_pendamping', 'data_pendamping.nama_pendamping')
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
                ->leftJoin('data_pekerjaan', 'data_pekerjaan.kode_pekerjaan', '=', 'data_nasabah.pekerjaan_kode')
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
                    'data_nasabah.alamat_ktp',
                    'data_pekerjaan.nama_pekerjaan'
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
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Roda 2');
                })->get();

            foreach ($data as $item) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $item->surveyor_kode)
                    ->select('nama_user', 'role_name')
                    ->get();

                $thn = Carbon::now()->year;
                $item->thn = $thn;

                if ($surveyor->isNotEmpty()) {
                    $item->nama_user = $surveyor[0]->nama_user;
                    $item->role_name = $surveyor[0]->role_name;
                }
            }
            // for ($i = 0; $i < count($data); $i++) {
            //     $surveyor = DB::table('v_users')
            //         ->where('code_user', $data[$i]->surveyor_kode)
            //         ->select('nama_user', 'role_name')->get();

            //     $thn = Carbon::now()->year;
            //     $data[$i]->thn = $thn;
            //     $data[$i]->nama_user = $surveyor[$i]->nama_user;
            //     $data[$i]->role_name = $surveyor[$i]->role_name;
            // }

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

            foreach ($data as $item) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $item->surveyor_kode)
                    ->select('nama_user', 'role_name')
                    ->first(); // Use first() instead of get()

                $thn = Carbon::now()->year;
                $item->thn = $thn;

                // Check if $surveyor is not null before accessing its properties
                if ($surveyor) {
                    $item->nama_user = $surveyor->nama_user;
                    $item->role_name = $surveyor->role_name;
                } else {
                    // Handle the case where no matching user is found
                    $item->nama_user = null;
                    $item->role_name = null;
                }
            }

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
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Roda 4');
                })->get();
            //
            foreach ($data as $item) {

                $surveyors = DB::table('v_users')
                    ->where('code_user', $item->surveyor_kode)
                    ->select('nama_user', 'role_name')
                    ->get();

                if ($surveyors->isNotEmpty()) {
                    $thn = Carbon::now()->year;
                    foreach ($surveyors as $index => $surveyor) {
                        $item->thn = $thn;
                        $item->nama_user = $surveyor->nama_user;
                        $item->role_name = $surveyor->role_name;
                    }
                }
            }

            // for ($i = 0; $i < count($data); $i++) {
            //     $surveyor = DB::table('v_users')
            //         ->where('code_user', $item->surveyor_kode)
            //         ->select('nama_user', 'role_name')->get();

            //     $thn = Carbon::now()->year;
            //     $data[$i]->thn = $thn;
            //     $data[$i]->nama_user = $surveyor[$i]->nama_user;
            //     $data[$i]->role_name = $surveyor[$i]->role_name;
            // }

            return view('cetak.layouts.mobil', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_pengajuan(Request $request)
    {
        $keyword = request('keyword');
        $usr = Auth::user()->code_user;

        //Cek Role User
        $role = DB::table('v_users')->select('v_users.role_name')->where('code_user', $usr)->get();

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->join('users', 'users.code_user', '=', 'data_pengajuan.input_user')
            ->select(
                'data_pengajuan.kode_pengajuan as kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.nasabah_kode as kd_nasabah',
                'data_pengajuan.id as id',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.jangka_waktu as jk',
                'data_pengajuan.input_user',
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
                'data_pengajuan.created_at as tanggal',
            )

            ->where(function ($query) {
                $query->orWhere('data_pengajuan.status', 'Sudah Otorisasi')
                    ->orWhere('data_pengajuan.status', 'Disetujui');

                // $query->orWhere('data_pengajuan.status', 'Lengkapi Data')
                //     ->orWhere('data_pengajuan.status', 'Sudah Otorisasi')
                //     ->orWhere('data_pengajuan.status', 'Disetujui')
                //     ->orWhere('data_pengajuan.status', 'Minta Otorisasi');
            })

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        //

        if ($role[0]->role_name == 'Customer Service') {
            $query->where('data_pengajuan.input_user', '=', $usr);
        } elseif ($role[0]->role_name == 'Head Teller') {
            $query->where('data_pengajuan.status', '=', 'Minta Otorisasi');
        } elseif ($role[0]->role_name == 'Kepala Kantor Kas') {
            $query->where('data_pengajuan.input_user', '=', $usr);
        }

        $pengajuan = $query->paginate(10);
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

    public function index_notifikasi_kredit(Request $request)
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.no_notifikasi',
                'data_pengajuan.created_at as tanggal',
                'data_survei.kantor_kode as wilayah',
                'data_survei.surveyor_kode as surveyor',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where('data_pengajuan.status', 'Disetujui')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        if ($data->isNotEmpty()) {
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
            }
        }

        return view('cetak-berkas.notifikasi-kredit.index', [
            'data' => $data
        ]);
    }

    public function index_perjanjian_kredit(Request $request)
    {
        $usr = Auth::user()->code_user;
        $isAdminKredit = DB::table('v_users')->where('code_user', $usr)->first();

        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')

            ->where(function ($query) use ($isAdminKredit) {

                if ($isAdminKredit->role_name == 'Admin Kredit') {
                } else {
                    $query->where('data_survei.kantor_kode', '=', Auth::user()->kantor_kode);
                }
            })


            ->whereNotNull('data_spk.no_spk')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_spk.*',
                'data_pengajuan.*',
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_spk.created_at as tanggal',
                'data_spk.otorisasi as otorpk',
                'users.name'
            )
            ->orderBy('data_spk.created_at', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        return view('cetak-berkas.perjanjian-kredit.index', [
            'data' => $data
        ]);
    }

    public function standing_interaction()
    {
        $usr = Auth::user()->code_user;
        $isAdminKredit = DB::table('v_users')->where('code_user', $usr)->first();

        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')

            ->where(function ($query) {
                if (Auth::user()->kantor_kode == 'KJT') {
                    $query->whereIn('data_pengajuan.produk_kode', ['KTA', 'KPS'])
                        ->whereIn('data_pengajuan.resort_kode', ['127', '004']);
                } else if (Auth::user()->kantor_kode == 'PGD') {
                    $query->whereIn('data_pengajuan.produk_kode', ['KTA', 'KPS'])
                        ->orWhere(function ($query) {
                            $query->whereIn('data_pengajuan.resort_kode', ['119']);
                        });
                } else if (Auth::user()->kantor_kode == 'SBG') {
                    $query->whereIn('data_pengajuan.produk_kode', ['KTA', 'KPS'])
                        ->orWhere(function ($query) {
                            $query->whereIn('data_pengajuan.resort_kode', ['058', '057', '055', '129', '141', '095', '090', '130', '131', '141']);
                        });
                }
            })


            ->where(function ($query) use ($isAdminKredit) {

                if ($isAdminKredit->role_name == 'Admin Kredit') {
                } else {
                    $query->where('data_survei.kantor_kode', '=', Auth::user()->kantor_kode);
                }
            })


            ->whereNotNull('data_spk.no_spk')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_spk.*',
                'data_pengajuan.*',
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_spk.created_at as tanggal',
                'data_spk.otorisasi as otorpk',
                'users.name'
            )
            ->orderBy('data_spk.created_at', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        $kantor = DB::table('data_kantor')->get();

        $produk = DB::table('data_produk')->get();

        return view('cetak-berkas.standing-interaction.index', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
        ]);
    }

    public function cetak_standing_interaction(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_rekening',
                    'data_nasabah.no_identitas',
                    'data_nasabah.no_telp',
                    'data_pengajuan.jangka_waktu',
                    'data_pengajuan.plafon',
                    'data_pengajuan.suku_bunga',
                    'data_pengajuan.metode_rps',
                    'data_spk.updated_at',
                )
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->first();
            //

            if (!is_null($data)) {
                if ($data->metode_rps == 'FLAT') {
                    $bunga = (($data->plafon * $data->suku_bunga) / 100) / 12;
                    $pokok = $data->plafon / $data->jangka_waktu;
                    $angsuran = $bunga + $pokok;

                    $angsur = $angsuran;
                } elseif ($data->metode_rps == 'EFEKTIF ANUITAS') {
                    $ssb = $data->suku_bunga / 100;
                    $sb = $ssb / 12;
                    $anuitas = ($data->plafon * $sb) / (1 - 1 / pow(1 + $sb, $data->jangka_waktu));
                    $angsur = round($anuitas);
                }
            } else {
                $angsur = 0;
            }


            $data->angsuran = (int) round($angsur);
            $data->tanggal_lahir = Carbon::createFromFormat('Ymd', $data->tanggal_lahir)->format('d-m-Y');
            $data->tanggal_spk = Carbon::parse($data->updated_at)->translatedFormat('d F Y');
            $alamat = ucwords(strtolower($data->alamat_ktp));
            $formattedAlamat = str_replace(['Rt/rw'], ['RT/RW'], $alamat);
            $data->alamat_ktp = $formattedAlamat;

            return view('cetak.standing-interaction.index', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_standing_interaction_wanayasa(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_rekening',
                    'data_nasabah.no_identitas',
                    'data_nasabah.no_telp',
                    'data_nasabah.no_cif',
                    'data_pengajuan.jangka_waktu',
                    'data_pengajuan.plafon',
                    'data_pengajuan.suku_bunga',
                    'data_spk.updated_at',
                )
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->first();
            //

            if (!is_null($data)) {
                $get = DB::connection('sqlsrv')->table('m_loan')
                    ->where('nocif', $data->no_cif)
                    ->where('plafond', '!=', '.00')
                    // ->orderBy('tglbuka', 'DESC')
                    ->first();

                $dt = DB::connection('sqlsrv')->table('m_loan_tagihan')
                    ->where('noacc', $get->noacc)
                    ->orderBy('thn', 'ASC')
                    ->orderBy('bln', 'ASC')
                    ->first();
                //
                $ans = (int) $dt->tag_pokok + (int) $dt->tag_bunga;

                $roundedNumber = (int) $this->roundUp($ans);
                $angsur = $roundedNumber;
            } else {
                $angsur = 0;
            }

            $data->angsuran = (int) round($angsur);
            $data->tanggal_lahir = Carbon::createFromFormat('Ymd', $data->tanggal_lahir)->format('d-m-Y');
            $data->tanggal_spk = Carbon::parse($data->updated_at)->translatedFormat('d F Y');
            $alamat = ucwords(strtolower($data->alamat_ktp));
            $formattedAlamat = str_replace(['Rt/rw'], ['RT/RW'], $alamat);
            $data->alamat_ktp = $formattedAlamat;

            return view('cetak.standing-interaction.wanayasa', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_photo_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $kantor = request('nama_kantor');
        $produk = request('kode_produk');

        $dataQuery = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_realisasi', 'data_realisasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->where('data_pengajuan.on_current', 1)
            ->where('data_pengajuan.status', 'Disetujui')
            ->whereNotNull('data_spk.no_spk')

            ->select(
                'data_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_pendamping.nama_pendamping',
                'data_realisasi.foto_pemohon',
                'data_realisasi.foto_pendamping',
            );

        $dataQuery->when($tgl1 && $tgl2, function ($data) use ($tgl1, $tgl2) {
            return $data->whereBetween('data_spk.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
        });

        // Hanya tambahkan klausa where jika $kantor tidak kosong
        if ($kantor !== null && $produk !== null) {

            $dataQuery->where('data_survei.kantor_kode', $kantor)
                ->where('data_pengajuan.produk_kode', $produk);
        } elseif ($kantor !== null && $produk === null) {

            $dataQuery->where('data_survei.kantor_kode', $kantor);
        } elseif ($kantor === null && $produk !== null) {

            $dataQuery->where('data_pengajuan.produk_kode', $produk);
        }

        $data = $dataQuery->orderBy('data_pengajuan.created_at', 'asc')->get();

        return view('cetak.realisasi-kredit.cetak_realisasi', [
            'data' => $data
        ]);
    }

    public function surat_survei(Request $request)
    {
        $enc = Crypt::decrypt($request->query('kode'));

        $data = DB::table('data_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->select(
                'v_users.nama_user as petugas',
                'v_users.role_name as jabatan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.created_at',
            )
            ->where('data_pengajuan.kode_pengajuan', $enc)->get();
        //
        if (!empty($data)) {
            $data[0]->created_at = Carbon::parse($data[0]->created_at)->year;
        }

        return view('cetak.layouts.survei', compact('data'));
        try {
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_lembar_konfirmasi()
    {
        $usr = Auth::user()->code_user;

        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')
            // ->where('data_pengajuan.on_current', '1')


            ->whereNotNull('data_spk.no_spk')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_spk.*',
                'data_pengajuan.*',
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.no_identitas as nik',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_spk.created_at as tanggal',
                'data_spk.otorisasi as otorpk',
                'users.name'
            )
            ->orderBy('data_spk.created_at', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        return view('cetak.lembar-konfirmasi.index', [
            'data' => $data
        ]);
    }

    public function cetak_konfirmasi_tabungan()
    {
        $kode = Crypt::decrypt(request()->pengajuan);

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->select(
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_identitas as nik',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_rekening',
                'data_nasabah.created_at',
            )
            ->where('data_pengajuan.kode_pengajuan', $kode)
            ->first();

        // NBP
        $cek = DB::connection('sqlsrv')->table('m_cif')
            ->join('m_tabunganb', 'm_cif.nocif', '=', 'm_tabunganb.nocif')
            ->select(
                'm_tabunganb.noacc'
            )
            ->where('m_cif.noid', $data->nik)
            ->first();
        //
        if (empty($cek)) {
            $noacc = '';
        } else {
            $noacc = $cek->noacc;
        }
        $tgl = Carbon::parse($data->created_at);
        $data->tgl = $tgl->translatedFormat('d F Y');

        return view('cetak.lembar-konfirmasi.tabungan', compact('data', 'noacc'));
    }

    public function cetak_konfirmasi_kredit()
    {
        $kode = Crypt::decrypt(request()->pengajuan);

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->select(
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_identitas as nik',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_rekening',
                'data_spk.created_at',
                'data_spk.no_spk',
            )
            ->where('data_pengajuan.kode_pengajuan', $kode)
            ->first();

        // NBP
        $cek = DB::connection('sqlsrv')->table('m_cif')
            ->join('m_loan', 'm_loan.nocif', '=', 'm_cif.nocif')
            ->select(
                'm_loan.noacc'
            )
            ->where('m_cif.noid', $data->nik)
            ->where('m_loan.no_spk', $data->no_spk)
            ->first();
        //
        if (empty($cek)) {
            $noacc = '';
        } else {
            $noacc = $cek->noacc;
        }
        $tgl = Carbon::parse($data->created_at);
        $data->tgl = $tgl->translatedFormat('d F Y');

        return view('cetak.lembar-konfirmasi.kredit', compact('data', 'noacc'));
    }

    private function roundUp($number)
    {
        $length = strlen((string) $number);

        if ($length <= 3) {
            return ceil($number / 1000) * 1000;
        }

        $factor = pow(10, 3); // Faktor untuk tiga digit terakhir
        $roundedNumber = ceil($number / $factor) * $factor;

        return $roundedNumber;
    }
}
