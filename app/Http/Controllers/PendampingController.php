<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PendampingController extends Controller
{
    public function edit(Request $request)
    {
        $nasabah = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $nasabah)->first();
        
        //Ambil kode pengajuan
        $pengajuan = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->get();
        
        //Ambil kode pendamping
        $pendamping = Pendamping::where('pengajuan_kode',$pengajuan[0]->kode_pengajuan)->get();

        //Ubah format masa identitas Ymd menjadi m-d-Y
         if (!is_null($pendamping[0]->masa_identitas)) {
            $carbonid = Carbon::createFromFormat('Ymd', $pendamping[0]->masa_identitas);
            $pendamping[0]->masa_identitas = $carbonid->format('m-d-Y');
        }
        //Ubah format tanggal lahir Ymd menjadi m-d-Y
        if (!is_null($pendamping[0]->masa_identitas)) {
            $carbonDate = Carbon::createFromFormat('Ymd', $pendamping[0]->tanggal_lahir);
            $pendamping[0]->tanggal_lahir= $carbonDate->format('m-d-Y');
        }
        
        // dd($pendamping[0]->tanggal_lahir);

        //Ubah identitas dari nomor id menjadi data string
        if ($pendamping[0]->identitas == "1") {
            $pendamping[0]['iden'] = 'KTP';
        }elseif ($pendamping[0]->identitas == "2") {
            $pendamping[0]['iden'] = 'SIM';
        }elseif ($pendamping[0]->identitas == "3"){
            $pendamping[0]['iden'] = 'Passport';
        }elseif ($pendamping[0]->identitas == "9"){
            $pendamping[0]['iden']= 'Lainnya';
        }

        //Ubah tanggungan dari nomor id menjadi data string
        if ($pendamping[0]->tanggungan == "0") {
            $pendamping[0]['tgn'] = 'Tidak Ada';
        }elseif ($pendamping[0]->tanggungan == "1") {
            $pendamping[0]['tgn'] = '1 Orang';
        }elseif ($pendamping[0]->tanggungan == "2") {
            $pendamping[0]['tgn'] = '2 Orang';
        }elseif ($pendamping[0]->tanggungan == "3"){
            $pendamping[0]['tgn'] = '3 Orang';
        }elseif ($pendamping[0]->tanggungan == "4"){
            $pendamping[0]['tgn']= '4 Orang';
        }elseif ($pendamping[0]->tanggungan == "5"){
            $pendamping[0]['tgn']= '5 Orang';
        }

        //Ubah pisah harta dari nomor id menjadi data string
        if ($pendamping[0]->pisah_harta == "Y") {
            $pendamping[0]['pisah'] = 'Iya';
        }elseif ($pendamping[0]->pisah_harta == "T") {
            $pendamping[0]['pisah'] = 'Tidak';
        }
        
        return view('pendamping.edit', [
            'nasabah' => $cek,
            'pengajuan' => $pengajuan,
            'pendamping' => $pendamping,
        ]);
    }

    public function update(Request $request){
        
        $cek = $request->validate([
            'identitas' => 'required',
            'no_identitas' => 'required',
            'masa_identitas' => 'required',
            'nama_pendamping' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'status' => 'required',
            'tanggungan' => 'required',
            'pisah_harta' => 'required',
            'photo' => '',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_selfie' => '',
            'photo_selfie.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pengecekan format "m-d-Y"
        $tg = explode('-', $request->masa_identitas);
        if (strlen($tg[0]) == 2) {
            $cek['masa_identitas'] = Carbon::createFromFormat('m-d-Y', $request->masa_identitas)->format('Ymd');
        }else if (strlen($tg[0]) == 4){
            $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        }

        //Hapus format tanggal lahir Y-M-D menjadi YMD
        $tl = explode('-', $request->tanggal_lahir);
        if (strlen($tl[0])) {
            $cek['tanggal_lahir'] = Carbon::createFromFormat('m-d-Y', $request->tanggal_lahir)->format('Ymd');
        }else{
            $cek['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');
        }
        
        // dd($cek['tanggal_lahir']);
        
        //Cek Photo
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete('public/image/photo/'.$request->oldphoto);
            }        
            $filename = $cek['photo']->getClientOriginalName(); 
            $cek['photo'] = $request->file('photo')->storeAs('image/photo', 'pendamping'.'_'.$request->nama_pendamping.'_'.$filename, 'public'); 
            $cek['photo'] = 'pendamping'.'_'.$request->nama_pendamping.'_'.$filename;     
        }else{
            $cek['photo'] = $request->oldphoto;
        }
        
         //Cek Photo Selfie
        if ($request->file('photo_selfie')) {
            if ($request->oldphotoselfie) {
                Storage::delete('public/image/photo_selfie/'.$request->oldphotoselfie);
            }
            $files = $cek['photo_selfie']->getClientOriginalName();         
            $cek['photo_selfie'] = $request->file('photo_selfie')->storeAs('image/photo_selfie', 'pendamping'.'_'.$request->nama_pendamping.'_'.$files, 'public');         
            $cek['photo_selfie'] = 'pendamping'.'_'.$request->nama_pendamping.'_'.$files;
        }else{
            $cek['photo_selfie'] = $request->oldphotoselfie;
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
