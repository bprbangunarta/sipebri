<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $query->select('users.id AS model_id', 'roles.id AS role_id', 'users.name', 'email', 'username', 'roles.name AS position', 'nama_kantor', 'code_user', 'is_active');
        $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        $query->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');
        $query->leftJoin('data_kantor', 'data_kantor.kode_kantor', '=', 'users.kantor_kode');

        if (!empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%');
        }

        $users = $query->paginate(10);

        $roles     = DB::table('roles')->get();
        $kantor   = DB::table('data_kantor')->get();

        return view('master/user.index', compact('users', 'roles', 'kantor'));
    }
}
