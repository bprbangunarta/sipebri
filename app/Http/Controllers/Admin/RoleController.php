<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        $query = Role::query();
        $query->select('roles.*', 'name');
        $query->whereNot('name', 'Administrator');
        $query->orderBy('name');

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $roles = $query->paginate(10);

        return view('master.role.index', compact('roles'));
    }

    public function create(Request $request)
    {
        $name = $request->name;

        request()->validate([
            'name'       => 'required',
        ]);

        $check = DB::table('roles')->where('name', $name)->count();
        if ($check > 0) {
            return redirect()->back()->with('toast_warning', 'The name has already been taken!');
        }

        $role = Role::create([
            'name'       => request('name'),
            'guard_name' => request('guard_name') ?? 'web',
        ]);

        if ($role) {
            return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Role gagal ditambahkan.');
        }
    }

    public function edit(Request $request, $id)
    {
        if ($id) {
            $permission = DB::table('roles')
                ->where('id', '=', $id)->first();

            return response()->json($permission);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = [
                'name'       => request('name'),
                'guard_name' => request('guard_name') ?? 'web',
                'updated_at' => Carbon::now(),
            ];
            DB::table('roles')
                ->where('id', $request->id)
                ->update($data);

            return redirect()->back()->with('success', 'Successfully updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update!');
        }
    }

    public function delete(Request $request)
    {
        $role_id = $request->role_id;
        $roles = DB::table('roles')->where('id', $role_id)->first();

        return view('master.role.delete', compact('roles'));
    }

    public function destroy($id, Request $request)
    {
        $id = $request->id;

        $delete = DB::table('roles')->where('id', $id)->delete();

        if ($delete) {
            return redirect()->back()->with('success', 'Successfully deleted!');
        } else {
            toast('Failed to delete!', 'error');
            return redirect()->back()->with('error', 'Failed to deleted!');
        }
    }
}
