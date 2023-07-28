<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendidikan::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('nama_pendidikan', 'like', '%' . $request->name . '%');
        }

        $pendidikan = $query->paginate(10);
        return view('master.pendidikan.index', compact('pendidikan'));
    }
}
