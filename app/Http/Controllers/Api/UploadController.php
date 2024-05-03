<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class UploadController extends Controller
{
    public function survey(Request $request)
    {
        $request->validate([
            'pengajuan_kode'    => 'required|string',
            'foto'              => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'pengajuan_kode.required'   => 'No Tugas tidak boleh kosong',
            'foto_pelaksanaan.required' => 'Foto tidak boleh kosong',
            'foto_pelaksanaan.image'    => 'Foto harus berupa gambar',
            'foto_pelaksanaan.mimes'    => 'Foto harus berformat jpeg, png, jpg, gif, atau svg',
            'foto_pelaksanaan.max'      => 'Ukuran foto maksimal 5MB',
        ]);

        $pengajuan_kode = $request->pengajuan_kode;
        $file = $request->file('foto');

        $fileName = $pengajuan_kode . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/uploads/survey', $fileName);

        $survey = Survey::where('pengajuan_kode', $pengajuan_kode)->first();

        if (!$survey) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $survey->foto = $fileName;
        $survey->save();

        return response()->json(['message' => 'Foto berhasil diunggah'], 200);
    }
}
