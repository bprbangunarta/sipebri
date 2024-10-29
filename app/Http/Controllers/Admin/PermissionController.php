<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function Pest\Laravel\json;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        $query = Permission::query();
        $query->select('permissions.*', 'name');
        $query->orderBy('name');

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $permission = $query->paginate(10);

        return view('master.permission.index', compact('permission'));
    }

    public function create(Request $request)
    {
        $name = $request->name;

        request()->validate([
            'name'  => 'required',
            'guard_name' => request('guard_name') ?? 'web',
        ]);

        $check = DB::table('permissions')->where('name', $name)->count();
        if ($check > 0) {
            return redirect()->back()->with('toast_warning', 'The name has already been taken!');
        }

        $permission = Permission::create([
            'name'  => request('name'),
        ]);

        if ($permission) {
            return redirect()->back()->with('toast_success', 'Saved successfully!');
        } else {
            toast('Failed to save!', 'error');
            return redirect()->back()->with('toast_warning', 'Failed to save!');
        }
    }

    public function edit(Request $request, $id)
    {

        if ($id) {
            $permission = DB::table('permissions')
                ->where('id', '=', $id)->get();

            return response()->json($permission);
        }
    }

    public function update(Request $request, $id)
    {
        $data = ['name' => $request->name];

        if ($id) {
            DB::table('permissions')->where('id', $id)->update($data);
            return redirect()->back()->with('success', 'Data permission berhasil diubah');
        } else {
            return redirect()->back()->with('error', 'Data permission gagal diubah');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            DB::table('permissions')
                ->where('id', '=', $id)->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    // givePermissionTo
    public function givepermission(Request $request)
    {
        $query = User::query();
        $query->select(
            'users.name',
            'roles.name AS position',
            'roles.id AS role_id',
        );
        $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        $query->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');
        $query->where('users.id', $request->user_id);
        $user = $query->first();

        $permission = DB::table('permissions')->orderBy('name', 'ASC')->get();

        $role = DB::table('role_has_permissions')
            ->get();
        // dd($permission);
        return view('master.user.give_permission', [
            'data' => $user,
            'role' => $role,
            'permission' => $permission,
        ]);
    }

    public function postpermission(Request $request)
    {
        $permission = $request->input('id1');
        $role = $request->input('id2');

        $name_persmission = DB::table('permissions')->where('id', $permission)->first();

        try {
            $role = Role::find($role);
            $role->givePermissionTo($permission);
            return response()->json($name_persmission->name);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function destroypermission(Request $request)
    {
        $permission = $request->input('id1');
        $id = $request->input('id2');

        $name_persmission = DB::table('permissions')->where('id', $permission)->first();

        try {
            $role = Role::find($id);
            $role->revokePermissionTo($permission);
            return response()->json($name_persmission->name);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
}
