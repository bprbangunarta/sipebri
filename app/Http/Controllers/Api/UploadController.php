<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class UploadController extends Controller
{
    public function survey_upload(Request $request)
    {
        $request->validate([
            'pengajuan_kode' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'pengajuan_kode.required' => 'Kode pengajuan harus diisi',
            'pengajuan_kode.string' => 'Kode pengajuan harus berupa string',
            'foto.required' => 'Foto harus diisi',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif',
            'foto.max' => 'Foto maksimal berukuran 5MB',
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
