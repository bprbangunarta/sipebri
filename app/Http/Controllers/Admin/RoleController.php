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

        $query = Role::query();
        $query->select('roles.*', 'name');
        $query->whereNot('name', 'Administrator');
        $query->orderBy('name');
        $roles = $query->paginate(10);

        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

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
            toast('The name has already been taken!', 'error');
            return redirect()->back();
        }

        $role = Role::create([
            'name'       => request('name'),
            'guard_name' => request('guard_name') ?? 'web',
        ]);

        $data = [
            'nama_role'       => request('name'),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        DB::table('testing')->insert($data);

        if ($role) {
            // toast('Saved successfully!', 'success');
            Alert::success('Saved successfully!', 'Success Message');
            return redirect()->back();
        } else {
            toast('Failed to save!', 'error');
            return redirect()->back();
        }
    }

    public function edit(Request $request)
    {
        $role_id = $request->role_id;
        $roles = DB::table('roles')->where('id', $role_id)->first();

        return view('master.role.edit', compact('roles'));
    }

    public function update($id, Request $request)
    {

        $id = $request->id;
        try {
            $data = [
                'name'       => request('name'),
                'guard_name' => request('guard_name') ?? 'web',
                'updated_at' => Carbon::now(),
            ];
            DB::table('roles')
                ->where('id', $id)
                ->update($data);

            toast('Successfully updated!', 'success');
            // Alert::success('Saved successfully!', 'Success Message');

            return redirect()->back();
        } catch (\Exception $e) {
            toast('Failed to update!', 'error');
            return redirect()->back();
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
            toast('Successfully deleted!', 'success');
            return redirect()->back();
        } else {
            toast('Failed to delete!', 'error');
            return redirect()->back();
        }
    }
}
