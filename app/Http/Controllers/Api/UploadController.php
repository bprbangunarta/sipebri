<?php

namespace App\Http\Controllers\Api;

use App\Models\Survey;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function survey(Request $request)
    {
        $request->validate([
            'kode'    => 'required|string',
            'foto'    => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'pengajuan_kode.required'   => 'No Tugas tidak boleh kosong',
            'foto.required' => 'Foto tidak boleh kosong',
            'foto.image'    => 'Foto harus berupa gambar',
            'foto.mimes'    => 'Foto harus berformat jpeg, png, jpg, gif, atau svg',
            'foto.max'      => 'Ukuran foto maksimal 5MB',
        ]);

        $pengajuan_kode = $request->kode;

        $cek = DB::table('rsc_data_survei')
            ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_data_survei.kode_rsc')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'data_nasabah.nama_nasabah'
            )
            ->where('rsc_data_survei.kode_rsc', $pengajuan_kode)->first();
        //
        if (!$cek) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $file = $request->file('foto');

        $fileName = $pengajuan_kode . '_' . $cek->nama_nasabah . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/image/uploads/rsc', $fileName);

        $foto = ['foto' => $fileName];
        $survey = DB::table('rsc_data_survei')->where('kode_rsc', $pengajuan_kode)->update($foto);

        return response()->json(['message' => 'Foto berhasil diunggah'], 200);
    }

    public function survey_eks(Request $request)
    {
        $request->validate([
            'kode'    => 'required|string',
            'foto'    => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'foto.required' => 'Foto tidak boleh kosong',
            'foto.image'    => 'Foto harus berupa gambar',
            'foto.mimes'    => 'Foto harus berformat jpeg, png, jpg, gif, atau svg',
            'foto.max'      => 'Ukuran foto maksimal 5MB',
        ]);

        $pengajuan_kode = $request->kode;

        $cek = DB::table('rsc_data_survei')
            ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_data_survei.kode_rsc')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'data_nasabah.nama_nasabah',
            )
            ->where('rsc_data_survei.kode_rsc', $pengajuan_kode)->first();
        //

        if (is_null($cek->nama_nasabah)) {
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
                ->where('m_loan.noacc', $cek->pengajuan_kode)->first();
            $cek->nama_nasabah = trim($data_eks->fnama);
        }

        //

        if (!$data_eks) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $file = $request->file('foto');

        $fileName = $pengajuan_kode . '_' . trim($cek->nama_nasabah) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/image/uploads/rsc', $fileName);

        $foto = ['foto' => $fileName];
        $survey = DB::table('rsc_data_survei')->where('kode_rsc', $pengajuan_kode)->update($foto);

        if ($survey) {
            return response()->json(['message' => 'Foto berhasil diunggah'], 200);
        } else {
            return response()->json(['message' => 'Foto gagal diunggah'], 400);
        }
    }
}
