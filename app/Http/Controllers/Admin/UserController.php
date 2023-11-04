<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $query->select(
            'users.id AS model_id', 
            'roles.id AS role_id', 
            'users.name', 
            'email', 
            'username', 
            'roles.name AS position', 
            'nama_kantor', 
            'code_user', 
            'kantor_kode', 
            'kode_surveyor', 
            'is_active'
        );
        $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        $query->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');
        $query->leftJoin('data_kantor', 'data_kantor.kode_kantor', '=', 'users.kantor_kode');
        $query->orderBy('name', 'asc');

        if (!empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%')
                    ->orWhere('roles.name', 'like', '%' . $request->name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $request->name . '%');
        }

        $users = $query->paginate(10);

        $roles     = DB::table('roles')->get();
        $kantor   = DB::table('data_kantor')->get();

        return view('master/user.index', compact('users', 'roles', 'kantor'));
    }

    public function store(Request $request)
    {

        $cek = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required|unique:users,username',
            'code_user' => 'required',
            'kode_surveyor' => '',
            'kode_kolektor' => '',
            'kantor_kode' => 'required',
            'is_active' => 'required',
        ]);
        $cek['kantor_kode'] = strtoupper($cek['kantor_kode']); //Huruf kapital
        $cek['code_user'] = strtoupper($cek['code_user']); //Huruf kapital
        $cek['password'] = bcrypt('12345');

        if ($cek) {
            User::create($cek);
            return redirect()->back()->with('success', 'Data user berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Data user gagal ditambahkan');
        }
    }

    public function edit($user)
    {
        $data = User::where('code_user', $user)->get();
        $kntr = Kantor::where('kode_kantor', $data[0]->kantor_kode)->get();
        $data[0]['nama_kantor'] = $kntr[0]->nama_kantor;

        return response()->json($data);
    }

    public function update(Request $request)
    {

        $cek = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'code_user' => 'required',
            'kode_surveyor' => '',
            'kode_kolektor' => '',
            'kantor_kode' => 'required',
            'is_active' => 'required',
        ]);

        $cek['code_user'] = strtoupper($cek['code_user']); //kapital
        $cek['kantor_kode'] = strtoupper($cek['kantor_kode']); //kapital

        if ($cek) {
            $data = User::where('code_user', $request->code_user)->get();
            User::where('id', $data[0]->id)
                ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }

    public function destroy($user)
    {
        $data = User::where('code_user', $user)->get();
        if ($data) {
            User::destroy($data[0]->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
