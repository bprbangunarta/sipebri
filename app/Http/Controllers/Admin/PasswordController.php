<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
    public function datareset(Request $request, $user){
        
        $data = User::where('code_user', $user)->get();
        return response()->json($data);
    }
}
