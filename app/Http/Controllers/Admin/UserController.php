<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Encryption\DecryptException;

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
        $cek['name'] = strtoupper($cek['name']); //Huruf kapital
        $cek['code_user'] = strtoupper($cek['code_user']); //Huruf kapital
        $cek['password'] = bcrypt('12345');
        // dd($cek);
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
        $cek['name'] = strtoupper($cek['name']); //kapital
        dd($cek);
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

    public function password_index()
    {
        $user = Auth::user()->code_user;
        $user_enc = Crypt::encrypt($user);

        $role = DB::table('v_users')->where('code_user', $user)->first();

        return view('profile.update-password', [
            'data' => $user_enc,
            'role' => $role->role_name,
        ]);
    }

    public function ubah_password(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('user'));
            if (is_null($request->password)) {
                return redirect()->back()->with('error', 'Harap Isi Password Lama Anda');
            }
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->with('error', 'Password lama salah');
            }
            $data = [
                'password' => bcrypt($request->new_password),
            ];

            $usr = User::where('code_user', $enc)->first();
            User::where('id', $usr->id)->update($data);

            return redirect()->back()->with('success', 'Anda Berhasil Merubah Password!!');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function perubahan_data_index(Request $request)
    {
        return view('menu.index');
    }

    public function ubah_data_tabel(Request $request)
    {
        try {
            //Nama Tabel
            $nama_table = $request->table;
            //Nama Field Didalam Tabel
            $field_table = $request->field_table;
            //Data yang akan dimasukan
            $value = $request->value_field_table;
            //Parameter data untuk melakukan perubahan
            $parameter = $request->parameter;
            //Fungsional perintah CRUD
            $fungsional = $request->fungsional;
            //Nama Filed Tabel Yang Akan Diubah
            $change_value = $request->change_value_field_table;

            if ($request->fungsional == 'get') {
                $cek = DB::table($nama_table)->where($field_table, $parameter)->$fungsional();
                $columns = collect(DB::select(DB::raw('SHOW COLUMNS FROM ' . $nama_table)))
                    ->pluck('Field')
                    ->all();

                return view('menu.data', [
                    'data' => $cek,
                    'field' => $columns,
                ]);
            } elseif ($request->fungsional == 'first') {
                $cek = DB::table($nama_table)->where($field_table, $parameter)->$fungsional();
                $columns = collect(DB::select(DB::raw('SHOW COLUMNS FROM ' . $nama_table)))
                    ->pluck('Field')
                    ->all();
                $cek = [(object)[$cek]];
                return view('menu.data', [
                    'data' => $cek,
                    'field' => $columns,
                ]);
            } elseif ($request->fungsional == 'latest') {
                $cek = DB::table($nama_table)->where($field_table, $parameter)->$fungsional()->get();
                $columns = collect(DB::select(DB::raw('SHOW COLUMNS FROM ' . $nama_table)))
                    ->pluck('Field')
                    ->all();

                return view('menu.data', [
                    'data' => $cek,
                    'field' => $columns,
                ]);
            }

            if ($request->fungsional == 'update') {
                $data = [
                    $change_value => $value,
                ];
                $cek = DB::table($nama_table)->where($field_table, $parameter)->$fungsional($data);
                $columns = Schema::getColumnListing($nama_table);

                return view('menu.data', [
                    'data' => $cek,
                    'field' => $columns,
                ]);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }
}
