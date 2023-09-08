<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function datareset(Request $request, $user){
        
        $data = User::where('code_user', $user)->get();
        return response()->json($data);
    }

    public function update(Request $request){
        
        $data = [
            'password' => Hash::make($request->reset)
        ];

        if ($request) {
            $user = DB::table('users')
                    ->select('id')
                    ->where('code_user', $request->code_user)->get();
            DB::table('users')
                    ->where('id', $user[0]->id)
                    ->update($data);
            return redirect()->back()->with('success', 'Password telah diubah');
        }else{
            return redirect()->back()->with('error', 'Password telah diubah');
        }
    }
}
