<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();
        $query->select('*');

        if (!empty($request->name)) {
            $query->where('kode_produk', 'like', '%' . $request->name . '%');
        }

        $produk = $query->paginate(10);
        return view('master.produk.index', compact('produk'));
    }
}
