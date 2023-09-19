<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\json;

class PermissionController extends Controller
{
    public function index(Request $request)
    {

        $query = Permission::query();
        $query->select('permissions.*', 'name');
        $query->orderBy('name');

        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
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
        $query = Permission::query();
        $query->select('permissions.*', 'name');
        $query->orderBy('name');

        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        $cek = DB::table('role_has_permissions')
                ->get();
        
        $permission = $query->paginate(10);
        
        return view('master.role.give-permission', [
            'permission' => $permission,
            'role' => $cek,
            'datas' => $request->query('id'),
        ]);
    }

    public function postpermission(Request $request){
        $permission = $request->input('id1');
        $id = $request->input('id2');
        
        try {
            $role = Role::find($id);
            $role->givePermissionTo($permission);
            return response()->json($permission);            
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function destroypermission(Request $request){
        $permission = $request->input('id1');
        $id = $request->input('id2');
        
        try {
            $role = Role::find($id);
            $role->revokePermissionTo($permission);
            return response()->json($permission);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
}