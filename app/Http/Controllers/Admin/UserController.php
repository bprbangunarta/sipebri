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
        $query->select('users.id AS model_id', 'roles.id AS role_id', 'users.name', 'email', 'username', 'roles.name AS position', 'region', 'code_user', 'is_active');
        $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        $query->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');

        if (!empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%');
        }

        $users = $query->paginate(10);

        $hasrole = DB::table('model_has_roles')->get();
        $roles   = DB::table('roles')->get();

        // dd($users, $hasrole, $roles);

        return view('master/user.index', compact('users', 'hasrole', 'roles'));
    }
}
