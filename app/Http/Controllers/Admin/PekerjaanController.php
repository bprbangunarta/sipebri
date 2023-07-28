<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pekerjaan::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('nama_pekerjaan', 'like', '%' . $request->name . '%');
        }

        $pekerjaan = $query->paginate(10);
        return view('master.pekerjaan.index', compact('pekerjaan'));
    }
}
