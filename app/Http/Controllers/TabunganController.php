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
        $query = Tabungan::query();
        $query->select('*');

        $tabungan = $query->paginate(10);
        return view('tabungan', compact('tabungan'));
    }
}
