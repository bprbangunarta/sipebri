<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HakAksesController extends Controller
{

    public function getRouteKeyName()
    {
        return 'akses'; 
    }

    public function editakses($akses){
        $data = User::where('code_user', $akses)->get();
        
        //Cari id user ada atau tidak di model_has_roles
        $akses = DB::table('model_has_roles')->where('model_id', $data[0]->id)->first();
        

        if (is_null($akses)) {
            $data['nama_roles'] = '--Pilih Role--';
            $roles = DB::table('roles')->orderBy('name', 'asc')->get();
            return response()->json([$data, $roles]);
        }
        
        
        $roles = DB::table('roles')->where('id', $akses->role_id)->first();
        if (is_null($roles)) {
            $data['nama_roles'] = '--Pilih Role--';
            $roles = DB::table('roles')->orderBy('name', 'asc')->get();
            return response()->json([$data, $roles]);
        }
        
        //Cari id roles berdasarkan id model_has_roles
        $role = DB::table('roles')->where('id', $akses->role_id)->first();

        $data['nama_roles'] = $role->name;
        $data['kode_roles'] = $role->id;
        $roles = DB::table('roles')->orderBy('name', 'asc')->get();
        return response()->json([$data, $roles]);
    }

    public function updateakses(Request $request){
        $cek = $request->validate([
            'model_id' => 'required',
            'role_id' => 'required'
        ]);


        $cek['role_id'] = $cek['role_id'];
        $cek['model_type'] = 'App\Models\User';
        $cek['model_id'];
        
        $cekdata = DB::table('model_has_roles')->where('model_id', $request->model_id)->first();
        dd($request, $cekdata);
        if (is_null($cekdata)) {
            DB::table('model_has_roles')->insert($cek);
            return redirect()->back()->with('success', 'Hak akses berhasil ditambahkan');
        }else{
            DB::table('model_has_roles')->where('model_id', $cekdata->model_id)->update($cek);
            return redirect()->back()->with('success', 'Hak akses berhasil diubah');
        }
        return redirect()->back()->with('error', 'Hak akses gagal diubah');
    }
}
