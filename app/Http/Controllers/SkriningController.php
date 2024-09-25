<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google_Service_Sheets_ValueRange;

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
        $catatan = $request->query('pep');

        $values_dttot = $this->dttot($nik, $nama);
        $values_dppspm = $this->dppspm($nama);
        $values_judi = $this->judi_online($nik, $nama);
        $values_berita = $this->berita_negatif($nama);
        $watch_list = $this->watch_list($nama);

        $dppspm = array_values($values_dppspm);

        if (!empty($values_dttot)) {
            $dttot = array_values($values_dttot);
        } else {
            $dttot = null;
        }

        if (!empty($values_judi)) {
            $judi = array_values($values_judi);
        } else {
            $judi = null;
        }

        $pep = $catatan;

        if (!empty($values_berita)) {
            $negative_news = array_values($values_berita);
        } else {
            $negative_news = null;
        }

        if (!empty($watch_list)) {
            $watch_list = array_values($watch_list);
        } else {
            $watch_list = null;
        }

        return view('skrining.hasil_skrining', compact('nik', 'nama', 'dppspm', 'dttot', 'judi', 'pep', 'negative_news', 'watch_list'));
    }

    public function cetak_skrining()
    {
        $tgl = Carbon::now();
        $tgl = $tgl->format('d F Y');

        $data = (object) [
            'nik' => request()->nik,
            'nama' => request()->nama,
            'dttot' => request()->dttot,
            'dppspm' => request()->dppspm,
            'judi_online' => request()->judi_online,
            'pep' => request()->pep,
            'berita_negatif' => request()->negative_news,
            'watch_list' => request()->watch_list,
            'pemeriksa' => ucwords(strtolower(Auth::user()->name)),
            'tgl' => $tgl,
        ];

        // VAlidasi Data 
        // if (
        //     $data->dttot == 'TERDAFTAR' ||
        //     $data->judi_online == 'TERDAFTAR' ||
        //     $data->berita_negatif == 'TERDAFTAR' ||
        //     $data->watch_list == 'TERDAFTAR' ||
        //     $data->pep == 'TERDAFTAR'
        // ) {

        //     $status = 'TERDAFTAR';
        // } else {
        //     $status = 'TIDAK TERDAFTAR';
        // }

        // $this->create_sheet(request()->nik, request()->nama, request()->pep, $status);


        return view('skrining.print_skrining', [
            'data' => $data
        ]);
    }

    public function analisa_skrining_index()
    {
        return view('skrining.analisa_skrining_index');
    }

    public function cetak_analisa_skrining()
    {
        return view('skrining.print_analisa_skrining');
    }


    // PRIVATE FUNCTION
    private function google_client()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        return $client;
    }

    private function dttot($nik, $nama)
    {
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'DTTOT!A:H'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        $nik_dttot = array_filter($values, function ($row) use ($nik) {
            return isset($row[1]) && strpos($row[1], $nik) !== false;
        });

        $nama_dttot = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && strpos($row[0], $nama) !== false;
        });

        if (!empty($nik_dttot) && !empty($nama_dttot)) {
            return array_merge($nik_dttot, $nama_dttot);
        }

        if (!empty($nik_dttot)) {
            return $nik_dttot;
        }

        if (!empty($nama_dttot)) {
            return $nama_dttot;
        }
    }

    private function dppspm($nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'DPPSPM!A:U'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $dppspm = array_filter($values, function ($row) use ($nama) {
            return isset($row[1]) && strpos($row[1],  $nama) !== false;
        });

        $dppspmFiltered = array_map(function ($row) {
            return array_filter($row, function ($value) {
                return $value !== 'NA';
            });
        }, $dppspm);

        $reindexedData = array_map('array_values', $dppspmFiltered);

        return $reindexedData;
    }

    private function judi_online($nik, $nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'JUDI ONLINE!B:D';
        $rangeD = 'JUDI ONLINE!D:D';

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        // $responseD = $sheetsService->spreadsheets_values->get($spreadsheetId, $rangeD);

        $values = $response->getValues();

        $judi = array_filter($values, function ($row) use ($nik) {
            return isset($row[2]) && $row[2] === $nik;
        });

        $judis = array_filter($values, function ($row) use ($nama) {
            return isset($row[0]) && $row[0] === $nama;
        });

        if (!empty($judi) && !empty($judis)) {
            $judi_online =  $judis;
        } else {
            $judi_online =  null;
        }

        return $judi_online;
    }

    private function berita_negatif($nama)
    {
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'Berita Negatif!B:J'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $berita_negatif = array_filter($values, function ($row) use ($nama) {
            return isset($row[3]) && strpos($row[3], $nama) !== false;
        });

        if (!empty($berita_negatif)) {
            $berita_negatif =  $berita_negatif;
        } else {
            $berita_negatif =  null;
        }

        return $berita_negatif;
    }

    private function watch_list($nama)
    {
        $nama = Str::upper($nama);
        $client = $this->google_client();
        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'Watchist !B:R'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $watch_list = array_filter($values, function ($row) use ($nama) {
            return isset($row[5]) && strpos($row[5], $nama) !== false;
        });

        if (!empty($watch_list)) {
            $watch_list =  $watch_list;
        } else {
            $watch_list =  null;
        }

        return $watch_list;
    }

    private function create_sheet($nik, $nama, $catatan, $status)
    {
        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'SCREENING!A:E';

        $data = [
            $nik,
            $nama,
            $catatan,
            Auth::user()->code_user,
            $status,
        ];

        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$data]
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }
}
