<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use App\Models\Midle;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class PendampingController extends Controller
{
    public function edit(Request $request)
    {
        $nasabah = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($nasabah);
            //Ambil kode pengajuan
            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
            $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->first();

            //Ambil kode pendamping
            $pendamping = Pendamping::where('pengajuan_kode', $pengajuan[0]->kode_pengajuan)->get();

            $data_pengajuan = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->get();
            if (count($data_pengajuan) > 1) {
                $data_pendamping = Pendamping::where('pengajuan_kode', $data_pengajuan[0]->kode_pengajuan)->first();
                $data_p = [
                    'no_identitas' => $data_pendamping->no_identitas,
                    'nama_pendamping' => $data_pendamping->nama_pendamping,
                    'tempat_lahir' => $data_pendamping->tempat_lahir,
                    'tanggal_lahir' => $data_pendamping->tanggal_lahir,
                    'status' => $data_pendamping->status,
                    'no_hp' => $data_pendamping->no_hp,
                    'tanggungan' => $data_pendamping->tanggungan,
                    'pisah_harta' => $data_pendamping->pisah_harta,
                ];
                Pendamping::where('pengajuan_kode', $pengajuan[0]->kode_pengajuan)->update($data_p);
            }

            //Ambil ulang kode pendamping
            $pendamping = Pendamping::where('pengajuan_kode', $pengajuan[0]->kode_pengajuan)->get();

            //Ubah format masa identitas Ymd menjadi m-d-Y
            if (!is_null($pendamping[0]->masa_identitas)) {
                $carbonid = Carbon::createFromFormat('Ymd', $pendamping[0]->masa_identitas);
                $pendamping[0]->masa_identitas = $carbonid->format('Y-m-d');
            }

            //Ubah format tanggal lahir Ymd menjadi m-d-Y
            if (!is_null($pendamping[0]->tanggal_lahir) && $pendamping[0]->tanggal_lahir !== "") {
                $carbonDate = Carbon::createFromFormat('Ymd', $pendamping[0]->tanggal_lahir);
                $pendamping[0]->tanggal_lahir = $carbonDate->format('Y-m-d');
            } else {
                $pendamping[0]->tanggal_lahir = null;
            }

            //Ubah identitas dari nomor id menjadi data string
            $id = Data::identitas($pendamping[0]->identitas);
            $pendamping[0]['iden'] = $id;

            //Ubah tanggungan dari nomor id menjadi data string
            $pend = Data::tanggungan($pendamping[0]->tanggungan);
            $pendamping[0]['tgn'] = $pend;

            //Ubah pisah harta dari nomor id menjadi data string
            if ($pendamping[0]->pisah_harta == "Y") {
                $pendamping[0]['pisah'] = 'Iya';
            } elseif ($pendamping[0]->pisah_harta == "T") {
                $pendamping[0]['pisah'] = 'Tidak';
            }

            //Auth user
            $us = Auth::user()->id;
            $user = DB::table('users')
                ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.code_user')
                ->where('users.id', '=', $us)->get();
            $cek->auth = $user[0]->code_user;
            $cek->kd_nasabah = Crypt::encrypt($cek->kode_nasabah);
            $cek->kd_pengajuan = $nasabah;
            $dt = Midle::analisa_usaha($enc);

            return view('pengajuan.data-pendamping', [
                'data' => $dt[0],
                'nasabah' => $cek,
                'pengajuan' => $pengajuan,
                'pendamping' => $pendamping,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update(Request $request)
    {

        $cek = $request->validate([
            'identitas' => 'required',
            'no_identitas' => 'required',
            'masa_identitas' => 'required',
            'nama_pendamping' => 'required',
            'no_hp' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'status' => 'required',
            'tanggungan' => 'required',
            'pisah_harta' => 'required',
            'photo' => '',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_ktp' => '',
            'photo_ktp.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $cek['input_user'] = $request->input_user;
        $cek['is_entry'] = 1;

        // Pengecekan format "m-d-Y"
        $tg = explode('-', $request->masa_identitas);
        if (strlen($tg[0]) == 2) {
            $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        } else if (strlen($tg[0]) == 4) {
            $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        }

        //Hapus format tanggal lahir Y-M-D menjadi YMD
        $tl = explode('-', $request->tanggal_lahir);
        if (strlen($tl[0])) {
            $cek['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');
        } else {
            $cek['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');
        }

        //Cek Photo
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete('public/image/photo/' . $request->oldphoto);
            }
            $files = $cek['photo']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_pendamping . '.' . $files;
            $cek['photo'] = $request->file('photo')->storeAs('image/photo', $new, 'public');
            $cek['photo'] = $new;
        } else {
            $cek['photo'] = $request->oldphoto;
        }

        //Cek Photo KTP
        if ($request->file('photo_ktp')) {
            if ($request->oldphotoktp) {
                Storage::delete('public/image/photo_ktp/' . $request->oldphotoktp);
            }
            $files = $cek['photo_ktp']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_pendamping . '.' . $files;
            $cek['photo_ktp'] = $request->file('photo_ktp')->storeAs('image/photo_ktp', $new, 'public');
            $cek['photo_ktp'] = $new;
        } else {
            $cek['photo_ktp'] = $request->oldphotoktp;
        }
        $cek['otorisasi'] = 'N';
        $cek['nama_pendamping'] = strtoupper($cek['nama_pendamping']);
        $cek['tempat_lahir'] = strtoupper($cek['tempat_lahir']);


        if (!is_null($request->fhotoformal)) {
            if ($request->oldphoto) {
                Storage::delete('public/image/photo/' . $request->oldphoto);
            }
            $cek['photo'] = $request->no_identitas . '_' . $request->nama_pendamping . '.jpg';
            Storage::put('public/image/photo/' . $cek['photo'], base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->fhotoformal)));
        }

        if (!is_null($request->fhotoktp)) {
            if ($request->oldphotoktp) {
                Storage::delete('public/image/photo_ktp/' . $request->oldphotoktp);
            }
            $cek['photo_ktp'] = $request->no_identitas . '_' . $request->nama_pendamping . '.jpg';
            Storage::put('public/image/photo_ktp/' . $cek['photo_ktp'], base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->fhotoktp)));
        }

        try {
            $pend = Pendamping::where('pengajuan_kode', $request->query('pendamping'))->get();
            Pendamping::where('id', $pend[0]->id)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }
}
