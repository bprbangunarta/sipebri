<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NasabahController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $nasabah = DB::table('data_nasabah')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_nasabah.*',
                'users.name',
            )
            ->where(function ($query) use ($keyword) {
                $query->where('kode_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('no_identitas', 'like', '%' . $keyword . '%')
                    ->orWhere('no_cif', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_nasabah.nama_nasabah', 'ASC')
            ->paginate(10);

        return view('master.nasabah.index', ['data' => $nasabah]);
    }

    public function edit($id)
    {
        $nasabah = DB::table('data_nasabah')
            ->join('users', 'users.code_user', '=', 'data_nasabah.input_user')
            ->select(
                'data_nasabah.*',
                'users.name',
            )
            ->where('data_nasabah.id', $id)
            ->first();

        return view('master.nasabah.edit', ['data' => $nasabah]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Add validation rules
        ]);

        // Update logic
        DB::table('data_nasabah')
            ->where('id', $id)
            ->update([
                'no_cif' => $request->no_cif,
                'no_identitas' => $request->no_identitas,
                'masa_identitas' => $request->masa_identitas,
                'nama_nasabah' => $request->nama_nasabah,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'kode_pos' => $request->kode_pos,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kota' => $request->kota,
                'alamat_ktp' => $request->alamat_ktp,
                'alamat_sekarang' => $request->alamat_sekarang,
                'nama_ibu_kandung' => $request->nama_ibu_kandung,
                'no_rekening' => $request->no_rekening,
                'no_npwp' => $request->no_npwp,
                'no_telp' => $request->no_telp,
                'no_telp_darurat' => $request->no_telp_darurat,
                'email' => $request->email,
                'tempat_kerja' => $request->tempat_kerja,
                'no_telp_kantor' => $request->no_telp_kantor,
                'no_karyawan' => $request->no_karyawan,
                'update_user' => Auth::user()->code_user,
            ]);

        return back()->with('success', 'Nasabah updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('data_nasabah')->where('id', $id)->delete();
        return back()->with('success', 'Nasabah deleted successfully.');
    }
}
