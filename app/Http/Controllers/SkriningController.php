<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkriningController extends Controller
{
    public function skrining_index()
    {
        return view('skrining.index');
    }

    public function skrining_nasabah(Request $request)
    {
        $nik = $request->query('nik');
        $nama = $request->query('nama');
        $catatan = $request->query('catatan');

        $values_dttot = $this->dttot($nama);
        $values_dppspm = $this->dppspm($nama);
        $values_judi = $this->judi_online($nik, $nama);
        $values_berita = $this->berita_negatif($nama);
        $watch_list = $this->watch_list($nama);

        $tgl = Carbon::now();
        $tgl = $tgl->format('d F Y');

        $data = (object) [
            'nik' => $nik,
            'nama' => $nama,
            'dttot' => $values_dttot,
            'dppspm' => $values_dppspm,
            'judi_online' => $values_judi,
            'pep' => $catatan,
            'berita_negatif' => $values_berita,
            'watch_list' => $watch_list,
            'pemeriksa' => ucwords(strtolower(Auth::user()->name)),
            'tgl' => $tgl,
        ];

        return view('skrining.print_skrining', compact('data'));
    }

    private function google_client()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        return $client;
    }

    private function dttot($nama)
    {
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'DTTOT!A:A'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        $dttot = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });

        if (!empty($dttot)) {
            $dttot = 'Terdaftar';
        } else {
            $dttot = 'Tidak terdaftar';
        }

        return $dttot;
    }

    private function dppspm($nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'DPPSPM!B:B'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $judi = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });


        if (!empty($judi)) {
            $judi =  'Terdaftar';
        } else {
            $judi =  'Tidak terdaftar';
        }

        return $judi;
    }

    private function judi_online($nik, $nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'JUDI ONLINE!B:B';
        $rangeD = 'JUDI ONLINE!D:D';

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $responseD = $sheetsService->spreadsheets_values->get($spreadsheetId, $rangeD);

        $values = $response->getValues();
        $valuesd = $responseD->getValues();

        $judi = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });

        $judis = array_filter($valuesd, function ($row) use ($nik) {
            return isset($row[0]) && $row[0] === $nik;
        });


        if (!empty($judi) && !empty($judis)) {
            $judi_online =  'Terdaftar';
        } else {
            $judi_online =  'Tidak terdaftar';
        }

        return $judi_online;
    }

    private function berita_negatif($nama)
    {
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'Berita Negatif!E:E'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $judi = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });


        if (!empty($judi)) {
            $judi =  'Terdaftar';
        } else {
            $judi =  'Tidak terdaftar';
        }

        return $judi;
    }

    private function watch_list($nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'Watchist !G:G'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $judi = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });

        if (!empty($judi)) {
            $judi =  'Terdaftar';
        } else {
            $judi =  'Tidak terdaftar';
        }

        return $judi;
    }

    public function analisa_skrining_index()
    {
        return view('skrining.analisa_skrining_index');
    }

    public function cetak_analisa_skrining()
    {
        return view('skrining.print_analisa_skrining');
    }
}
