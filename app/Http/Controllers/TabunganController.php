<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\get;

class TabunganController extends Controller
{
    public function index()
    {
        $tabungan = Tabungan::all();
        // dd($tabungan);

        return view('tabungan', compact('tabungan'));
    }
}
