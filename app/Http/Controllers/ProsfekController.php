<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kantor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProsfekController extends Controller
{
    public function index()
    {
        $kantor = Kantor::get();
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');
        return view('staff.prosfek.index', compact('kantor', 'kab'));
    }

    public function simpan_prosfek(Request $request)
    {

        try {
            if (empty($request->calon_nasabah)) {
                return redirect()->back()->with('error', 'Nama tidak boleh kosong.');
            }

            $kab = DB::table('v_kabupaten')->where('kode_dati', $request->kabupaten)->first();

            $data = [
                'code_user' => Auth::user()->code_user,
                'calon_nasabah' => strtoupper($request->calon_nasabah),
                'alamat' => strtoupper($request->alamat),
                'kelurahan' => strtoupper($request->kelurahan),
                'kecamatan' => strtoupper($request->kecamatan),
                'kabupaten' => strtoupper($kab->kabupaten),
                'no_hp' => $request->no_hp,
                'prosfek1_via' => strtoupper($request->prosfek_via),
                'ket1' => strtoupper($request->keterangan),
                'fhoto_prosfek1' => '',
                'tgl_prosfek1' => now(),
                'created_at' => now(),
            ];

            if ($request->hasFile('photo_prosfek')) {
                $request->validate([
                    'photo_prosfek' => 'required|mimes:png,jpg,jpeg,svg|max:2048',
                ]);
                $photo = $request->file('photo_prosfek');
                $ekstensi = $photo->getClientOriginalExtension();
                $new = Str::uuid() . '.' . $ekstensi;
                $data['fhoto_prosfek1'] = $photo->storeAs('image/photo_prosfek', $new, 'public');
                $data['fhoto_prosfek1'] = $new;
            } else {
                return redirect()->back()->with('error', 'Fhoto harus diisi.');
            }

            $insert = DB::table('data_prosfek')->insert($data);
            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan.');
        }
    }



    // Data Prosfek
    public function data_prosfek_index()
    {
        try {
            $keyword = request('keyword');
            $role = DB::table('v_users')->where('code_user', Auth::user()->code_user)->pluck('role_name')->first();

            $data = DB::table('data_prosfek')
                ->join('v_users', 'v_users.code_user', '=', 'data_prosfek.code_user')
                ->where(function ($query) use ($keyword) {
                    $query->where('data_prosfek.calon_nasabah', 'like', '%' . $keyword . '%')
                        ->orWhere('data_prosfek.alamat', 'like', '%' . $keyword . '%')
                        ->orWhere('data_prosfek.kabupaten', 'like', '%' . $keyword . '%')
                        ->orWhere('data_prosfek.kelurahan', 'like', '%' . $keyword . '%')
                        ->orWhere('data_prosfek.kecamatan', 'like', '%' . $keyword . '%')
                        ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
                })
                ->when(in_array($role, ['Staff Analis', 'Kepala Kantor Kas', 'Customer Service']), function ($query) {
                    $query->where('data_prosfek.code_user', auth()->user()->code_user);
                })
                ->paginate(10);
            //

            return view('staff.prosfek.data_prosfek', compact('data', 'role'));
        } catch (\Throwable $th) {
        }
    }

    public function data_prosfek_get()
    {
        $id = request()->input('data');

        $data = DB::table('data_prosfek')->find($id);
        if ($data) {
            $data->alamat_calon = $data->alamat . ' ' . 'KEL. ' . $data->kelurahan . ' KEC. ' . $data->kecamatan . ' ' . $data->kabupaten;
        } else {
            $data->alamat_calon = null;
        }

        return response()->json($data);
    }

    public function data_prosfek_update(Request $request)
    {
        try {
            $cek = DB::table('data_prosfek')->find($request->id);
            if (is_null($cek)) {
                return redirect()->back()->with('error', 'Data tidak ada.');
            }

            $today = Carbon::today();

            if (
                (!is_null($cek->tgl_prosfek1) && Carbon::parse($cek->tgl_prosfek1)->isSameDay($today)) ||
                (!is_null($cek->tgl_prosfek2) && Carbon::parse($cek->tgl_prosfek2)->isSameDay($today))
            ) {
                return redirect()->route('data.prosfek.index')
                    ->with('error', 'Prosfek hanya sekali per hari dengan calon nasabah yang sama.');
            }


            if (is_null($cek->tgl_prosfek2)) {
                $data = [
                    'prosfek2_via' => strtoupper($request->prosfek_via),
                    'tgl_prosfek2' => now(),
                    'ket2' => strtoupper($request->keterangan),
                    'fhoto_prosfek2' => ''
                ];

                if ($request->hasFile('photo_prosfek')) {
                    $request->validate([
                        'photo_prosfek' => 'required|mimes:png,jpg,jpeg,svg|max:2048',
                    ]);
                    $photo = $request->file('photo_prosfek');
                    $ekstensi = $photo->getClientOriginalExtension();
                    $new = Str::uuid() . '.' . $ekstensi;

                    // $data['fhoto_prosfek2'] = $photo->storeAs('image/photo_prosfek', $new, 'public');
                    $photo->move(public_path('storage/image/photo_prosfek'), $new);

                    $data['fhoto_prosfek2'] = $new;
                } else {
                    return redirect()->back()->with('error', 'Fhoto harus diisi.');
                }

                $update = DB::table('data_prosfek')->where('id', $request->id)->update($data);

                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
                } else {
                    return redirect()->back()->with('success', 'Data gagal ditambahkan.');
                }
            } else {
                $data = [
                    'prosfek3_via' => strtoupper($request->prosfek_via),
                    'tgl_prosfek3' => now(),
                    'ket3' => strtoupper($request->keterangan),
                    'fhoto_prosfek3' => ''
                ];

                if ($request->hasFile('photo_prosfek')) {
                    $photo = $request->file('photo_prosfek');
                    $ekstensi = $photo->getClientOriginalExtension();
                    $new = Str::uuid() . '.' . $ekstensi;
                    $data['fhoto_prosfek3'] = $photo->storeAs('image/photo_prosfek', $new, 'public');
                    $data['fhoto_prosfek3'] = $new;
                } else {
                    return redirect()->back()->with('error', 'Fhoto harus diisi.');
                }

                $update = DB::table('data_prosfek')->where('id', $request->id)->update($data);

                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
                } else {
                    return redirect()->back()->with('success', 'Data gagal ditambahkan.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan, Hubungi IT.');
        }
    }

    public function data_prosfek_closing(Request $request)
    {
        try {
            $cek = DB::table('data_prosfek')->find($request->id);
            if (is_null($cek)) {
                return redirect()->back()->with('error', 'Data tidak ada.');
            }

            $data = [
                'fhoto_closing' => '',
                'tgl_closing' => now()
            ];

            if ($request->hasFile('photo_closing')) {
                $photo = $request->file('photo_closing');
                $ekstensi = $photo->getClientOriginalExtension();
                $new = Str::uuid() . '.' . $ekstensi;
                $data['fhoto_closing'] = $photo->storeAs('image/photo_prosfek', $new, 'public');
                $data['fhoto_closing'] = $new;
            } else {
                return redirect()->back()->with('error', 'Fhoto harus diisi.');
            }

            $update = DB::table('data_prosfek')->where('id', $request->id)->update($data);

            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan, Hubungi IT.');
        }
    }
}
