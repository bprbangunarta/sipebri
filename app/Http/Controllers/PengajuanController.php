<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\CGC;
use App\Models\Data;
use App\Models\Midle;
use App\Models\Agunan;
use App\Models\Produk;
use App\Models\Resort;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use function Pest\Laravel\get;

use function Pest\Laravel\delete;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PengajuanController extends Controller
{

    public function index(Request $request)
    {
        $name = request('name');
        $usr = Auth::user()->code_user;

        //Cek Role User
        $role = DB::table('v_users')->select('v_users.role_name')->where('code_user', $usr)->get();

        $query = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select(
                'data_pengajuan.kode_pengajuan as kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.nasabah_kode as kd_nasabah',
                'data_pengajuan.id as id',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.jangka_waktu as jk',
                'data_nasabah.nama_nasabah as nama',
                'data_nasabah.no_telp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_nasabah.alamat_ktp as alamat',
                'data_pengajuan.status',
                'data_nasabah.is_entry as entry',
            )
            // ->where('data_pengajuan.status', 'Sudah Otorisasi')
            // ->orWhere('data_pengajuan.status', 'Lengkapi Data')
            // ->where('data_pengajuan.status', 'Disetujui')
            // ->orWhere('data_pengajuan.status', 'Batal')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', 'Lengkapi Data')
                    ->orWhere('data_pengajuan.status', 'Sudah Otorisasi')
                    ->orWhere('data_pengajuan.status', 'Minta Otorisasi');
            })
            ->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')->orderBy('data_nasabah.created_at', 'ASC');
        //

        if ($role[0]->role_name == 'Customer Service') {
            $query->where('data_pengajuan.input_user', '=', $usr);
        } elseif ($role[0]->role_name == 'Head Teller') {
            $query->where('data_pengajuan.status', '=', 'Minta Otorisasi');
        }

        $pengajuan = $query->paginate(7);
        $auth = Auth::user()->code_user;
        $dtu = DB::table('v_users')->where('code_user', $auth)->first();
        foreach ($pengajuan as $item) {
            $item->kd_nasabah = Crypt::encrypt($item->kd_nasabah);
            $item->kd = Crypt::encrypt($item->kode);
            $item->user = $dtu->role_name;
        }
        // dd($pengajuan);
        return view('pengajuan.index', [
            'data' => $pengajuan,
            'auth' => $auth,
        ]);
    }

    public function all(Request $request)
    {
        $name = request('name');

        $query = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan as kode', 'data_pengajuan.produk_kode', 'data_pengajuan.nasabah_kode as kd_nasabah', 'data_pengajuan.id as id', 'data_pengajuan.plafon as plafon', 'data_pengajuan.jangka_waktu as jk', 'data_nasabah.nama_nasabah as nama', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_nasabah.no_telp', 'data_nasabah.alamat_ktp as alamat', 'data_pengajuan.status', 'data_pengajuan.tracking', 'data_pengajuan.kategori', 'data_nasabah.is_entry as entry')
            ->where('data_pengajuan.status', '!=', 'Batal')
            ->where('data_pengajuan.tracking', '!=', 'Selesai')
            ->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')->orderBy('data_pengajuan.created_at', 'asc');;
        //

        $pengajuan = $query->paginate(7);

        foreach ($pengajuan as $item) {
            $item->kd_nasabah = Crypt::encrypt($item->kd_nasabah);
            $item->kd = Crypt::encrypt($item->kode);
        }
        // dd($pengajuan);
        return view('pengajuan.all', [
            'data' => $pengajuan,
        ]);
    }

    public function storepengajuan(Request $request)
    {

        $cek = $request->validate([
            'kode_pengajuan' => 'required', 'plafon' => 'required', 'produk_kode' => 'required', 'suku_bunga' => 'required',
            'jangka_waktu' => 'required', 'metode_rps' => 'required', 'jangka_pokok' => 'required', 'jangka_bunga' => 'required',
            'resort_kode' => '', 'penggunaan' => 'required', 'tabungan_cgc' => '', 'keterangan' => '', 'input_user' => 'required',
        ]);
        // dd($request);
        $cek['is_entry'] = 1;
        $cek['kode_pengajuan'] = Crypt::decrypt($cek['kode_pengajuan']);

        //Hapus format rupiah
        $remove = array("Rp", ".", " ");
        $cek['plafon'] = str_replace($remove, "", $cek['plafon']);
        $cek['otorisasi'] = 'N';

        try {
            Pengajuan::where('kode_pengajuan', $cek['kode_pengajuan'])->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function edit(Request $request)
    {
        $req = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);
            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
            $nasabah = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();

            //Data produk
            $produk = Produk::where('kode_produk', $pengajuan[0]->produk_kode)->first();
            if (is_null($produk)) {
                $pengajuan[0]->produk_nama = null;
                $peng = $pengajuan[0];
            } else {
                $pengajuan[0]->produk_nama = $produk->nama_produk;
                $peng = $pengajuan[0];
            }

            // mencari nama

            if (!is_null($peng->resort_kode)) {
                $query = DB::connection('sqlsrv')->table('resort')->select('kode', 'ket')
                    ->where('kode', $peng->resort_kode ?? null)->first();
                $peng->nama_resort = $query->ket;
            } else {
                $peng->nama_resort = null;
            }

            //Data auth
            $us = Auth::user()->id;
            $user = DB::table('users')
                ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.code_user')
                ->where('users.id', '=', $us)->get();
            $peng->auth = $user[0]->code_user;

            //Data resort
            $resort = DB::connection('sqlsrv')
                ->table('resort')
                ->select('kode', 'ket')
                ->get();

            $nasabah[0]->kd_nasabah = Crypt::encrypt($nasabah[0]->kode_nasabah);
            $peng->kode_pengajuan = Crypt::encrypt($peng->kode_pengajuan);

            //Produk All
            $pro = Produk::all();
            $dt = Midle::analisa_usaha($enc);

            //Ambil data CGC        
            $cgc = CGC::select('*')->get();
            $namacgc = CGC::where('noacc', $peng->tabungan_cgc)->first();
            $peng->namacgc = $namacgc->fnama ?? null;
            // dd($)
            return view('pengajuan.data-pengajuan', [
                'data' => $dt[0],
                'cgc' => $cgc,
                'pengajuan' => $peng,
                'resort' => $resort,
                'produk' => $pro,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function agunan(Request $request)
    {
        $req = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);

            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
            $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->first();
            $jaminan = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.*', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->where('data_pengajuan.kode_pengajuan', '=', $pengajuan[0]->kode_pengajuan)
                ->get();

            //Data agunan
            $agunan = Agunan::all();
            //Data jenis dokumen
            $dok = DB::table('data_jenis_dokumen')->get();
            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');
            $us = Auth::user()->id;
            $user = DB::table('users')
                ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.code_user')
                ->where('users.id', '=', $us)->get();

            $cek->auth = $user[0]->code_user;
            $cek->kd_pengajuan = $req;
            $dt = Midle::analisa_usaha($enc);
            $cek->plafon = $dt[0]->plafon;
            $cek->jangka_waktu = $dt[0]->jangka_waktu;

            for ($i = 0; $i < count($jaminan); $i++) {
                $jaminan[$i]->kd_pengajuan = Crypt::encrypt($jaminan[$i]->kode_pengajuan);
            }

            //Agunan Kendaraan
            $jenis_kendaraan = DB::table('ja_kendaraan')->get();
            $data_kendaraan = DB::table('da_kendaraan')->get();

            //Agunan Tanah
            $jenis_tanah = DB::table('ja_tanah')->get();
            $data_tanah = DB::table('da_tanah')->get();

            //Agunan Lain
            $jenis_lain = DB::table('ja_lainnya')->get();
            $data_lain = DB::table('da_lainnya')->get();

            // dd($jenis_kendaraan);
            return view('pengajuan.data-agunan', [
                'jenis_kendaraan' => $jenis_kendaraan,
                'data_kendaraan' => $data_kendaraan,
                'jenis_tanah' => $jenis_tanah,
                'data_tanah' => $data_tanah,
                'jenis_lain' => $jenis_lain,
                'data_lain' => $data_lain,
                'data' => $cek,
                'jaminan' => $jaminan,
                'pengajuan' => $pengajuan[0],
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {

        $cek = $request->validate([
            'pengajuan_kode' => 'required',
            'jenis_agunan_kode' => 'required',
            'jenis_dokumen_kode' => 'required',
            'no_dokumen' => 'required',
            'atas_nama' => 'required',
            'masa_agunan' => 'required',
            'kode_dati' => 'required',
            'lokasi' => '',
            'catatan' => '',
            'input_user' => 'required',
        ]);
        $cek['is_entry'] = 1;
        $cek['created_at'] = now();

        // Merubah tanggal 
        $carbonDate = Carbon::createFromFormat('Y-m-d', $cek['masa_agunan']);
        $cek['masa_agunan'] = $carbonDate->format('Ymd');

        try {
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function editagunan($id)
    {
        $data = DB::table('data_jaminan')
            ->where('id', $id)
            ->get();

        //Ambil data agunan
        $agunan = Agunan::where('kode', $data[0]->jenis_agunan_kode)->get();

        //Ambil data dokumen
        $dok = DB::table('data_jenis_dokumen')
            ->select('jenis_dokumen')
            ->where('kode', $data[0]->jenis_dokumen_kode)->get();

        //Data dati
        $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $data[0]->kode_dati)->get();

        //Dati all data
        $kabupaten = DB::table('v_dati')
            ->select('kode_dati', 'nama_dati')
            ->distinct()->get();

        //Agunan
        $agn = Agunan::select('kode', 'jenis_agunan')->get();

        //Dokumen
        $dokumen = DB::table('data_jenis_dokumen')
            ->select('kode', 'jenis_dokumen')->get();

        //Format tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->masa_agunan);
        $data[0]->masa_agunan = $carbonDate->format('Y-m-d');

        /* Menambahkan field baru ke variable data dari data agunan dan data dokumen */
        $data[0]->jenis_agunan = $agunan[0]->jenis_agunan;
        $data[0]->jenis_dokumen = $dok[0]->jenis_dokumen;
        $data[0]->nama_dati = $dati[0]->nama_dati;

        $data[0]->auth = Auth::user()->code_user;

        return response()->json([$data, $kabupaten, $agn, $dokumen]);
    }

    public function updateagunan(Request $request)
    {
        $cek = $request->validate([
            'jenis_agunan_kode' => 'required',
            'jenis_dokumen_kode' => 'required',
            'no_dokumen' => 'required',
            'atas_nama' => 'required',
            'masa_agunan' => 'required',
            'kode_dati' => 'required',
            'lokasi' => '',
            'catatan' => '',
            'input_user' => 'required',
        ]);
        $cek['is_entry'] = 1;
        $cek['updated_at'] = now();
        // Merubah tanggal 
        $carbonDate = Carbon::createFromFormat('Y-m-d', $cek['masa_agunan']);
        $cek['masa_agunan'] = $carbonDate->format('Ymd');
        $cek['otorisasi'] = 'N';

        try {
            DB::table('data_jaminan')
                ->where('id', $request->data)
                ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function validasiagunan(Request $request)
    {
        $req = $request->query('pengajuan');
        $data = [
            'otorisasi' => 'A',
            'auth_user' => Auth::user()->code_user,
        ];

        try {
            DB::table('data_jaminan')->where('id', '=', $request->data)->update($data);
            return redirect()->back()->with('success', 'Data berhasil divalidasi');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal divalidasi');
        }
    }

    public function deleteagunan($pengajuan)
    {

        try {

            DB::table('data_jaminan')->where('id', $pengajuan)->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function destroy($pengajuan)
    {
        try {
            $data = [
                'status' => 'Batal',
            ];
            Pengajuan::where('id', $pengajuan)->update($data);
            return redirect()->back()->with('success', 'Data berhasil dibatalkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dibatalkan');
        }
    }
}
