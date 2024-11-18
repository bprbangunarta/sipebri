<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerhitunganMetodeController extends Controller
{
    public function musiman($hasil_bersih, $jangka_waktu, $plafon)
    {
        $ambil = ($hasil_bersih * 70) / 100;
        $jW = $jangka_waktu / 6;
        $saving = $plafon / $jW;
        $sisa_pendapatan = $ambil - $saving;
        $pendapatan_perbulan = $sisa_pendapatan / 6;

        $data = [
            'ambil' => (int) $ambil ?: 0,
            'saving' => (int) $saving ?: 0,
            'pendapatan_perbulan' => (int) $pendapatan_perbulan ?: 0,
        ];

        return $data;
    }

    public function perpadian($hasil_bersih, $jangka_waktu, $plafon)
    {
        $ambil = 0;
        $jW = $jangka_waktu / 6;
        $saving = $plafon / $jW;
        $sisa_pendapatan = $saving;
        $pendapatan_perbulan = ($hasil_bersih - $sisa_pendapatan) / 6;

        $data = [
            'ambil' => (int) $ambil ?: 0,
            'saving' => (int) $saving ?: 0,
            'pendapatan_perbulan' => (int) $pendapatan_perbulan ?: 0,
        ];

        return $data;
    }
}
