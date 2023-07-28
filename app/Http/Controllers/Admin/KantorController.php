<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantor;

class KantorController extends Controller
{
    public function index(Request $request)
    {
        $query = Kantor::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('nama_kantor', 'like', '%' . $request->name . '%');
        }

        $kantor = $query->paginate(10);
        return view('master.kantor.index', compact('kantor'));
    }
}
