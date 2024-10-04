<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use App\Models\User;
use Google_Service_Sheets;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        if (empty($nik)) {
            return redirect()->back()->withInput()->with('error', 'NIK harus diisi.');
        }

        if (empty($nama)) {
            return redirect()->back()->withInput()->with('error', 'NAMA harus diisi.');
        }

        if (empty($catatan)) {
            $catatan = 'TIDAK TERDAFTAR';
        }

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

        if (
            !empty($dttot) ||
            !empty($dppspm) ||
            !empty($judi) ||
            $pep == 'TERDAFTAR' ||
            !empty($negative_news) ||
            !empty($watch_list)
        ) {
            $status = 'TERDAFTAR';
        } else {
            $status = 'TIDAK TERDAFTAR';
        }

        return view('skrining.hasil_skrining', compact('nik', 'nama', 'dppspm', 'dttot', 'judi', 'pep', 'negative_news', 'watch_list', 'status'));
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

        return view('skrining.print_skrining', [
            'data' => $data
        ]);
    }

    public function data_skrining(Request $request)
    {
        $search = request('keyword');

        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'SCREENING!A:N';

        $sheetsService = new Google_Service_Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $existingValues = $response->getValues();

        $data = array_slice($existingValues, 1);

        if ($search) {
            $data = array_filter($data, function ($row) use ($search) {
                return stripos(implode(' ', $row), $search) !== false;
            });
        }

        $user = DB::table('v_users')->where('code_user', Auth::user()->code_user)->pluck('role_name')->first();

        return view('skrining.data_skrining_index', compact('data', 'user'));
    }

    public function analisa_skrining_index()
    {
        $nik = request()->nik;
        $nama = request()->nama;

        return view('skrining.analisa_skrining_index', compact('nik', 'nama'));
    }

    public function cetak_analisa_skrining()
    {
        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';
        $range = 'SCREENING!A:N';

        $sheetsService = new Google_Service_Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $filteredRowIndex = null;

        foreach ($values as $index => $row) {
            if (isset($row[0]) && strpos($row[0], request()->nik) !== false && strpos($row[1], request()->nama) !== false) {
                $filteredRowIndex = $row;
            }
        }

        if (!empty($filteredRowIndex)) {
            $user = User::where('code_user', $filteredRowIndex[11])->pluck('name')->first();
            if (!empty($filteredRowIndex[13])) {

                $kabag = User::where('code_user', $filteredRowIndex[13])->pluck('name')->first();
            } else {
                $kabag = ' ';
            }
            $data = (object) [
                'nik' => $filteredRowIndex[0] ?: null,
                'nama' => $filteredRowIndex[1],
                'dttot' => $filteredRowIndex[2],
                'dppspm' => $filteredRowIndex[3],
                'judi' => $filteredRowIndex[4],
                'pep' => $filteredRowIndex[5],
                'negative' => $filteredRowIndex[6],
                'watch' => $filteredRowIndex[7],
                'staff' => $user,
                'kabag' => $kabag,
                'catatan' => $filteredRowIndex[12],
            ];
        } else {
            $data = (object) [
                'nik' => null,
                'nama' => null,
                'dttot' => null,
                'dppspm' => null,
                'judi' => null,
                'pep' => null,
                'negative' => null,
                'watch' => null,
                'catatan' => null,
            ];
        }

        if (!empty($filteredRowIndex[11])) {
            $qr_staff = $this->qrcode($filteredRowIndex[11]);
        } else {
            $qr_staff = null;
        }

        if (!empty($filteredRowIndex[13])) {
            $qr_kabag = $this->qrcode($filteredRowIndex[13]);
        } else {
            $qr_kabag = null;
        }

        $tgl = Carbon::now();
        $tgl = $tgl->locale('id')->translatedFormat('d F Y');

        return view('skrining.print_analisa_skrining', compact('data', 'qr_staff', 'qr_kabag', 'tgl'));
    }

    public function proses_analisa_skrining()
    {
        $nik = request()->nik;
        $nama = request()->nama;
        $catatan = request()->catatan;
        $analisa = 'APPROVE KABAG';
        $user = Auth::user()->code_user;

        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';
        $range = 'SCREENING!A:N';

        $sheetsService = new Google_Service_Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $filteredRowIndex = null;

        foreach ($values as $index => $row) {
            if (
                isset($row[0]) && strpos($row[0], $nik) !== false &&
                isset($row[1]) && strpos($row[1], $nama) !== false &&
                isset($row[10]) && $row[10] == 'ANALISA SKRINING'
            ) {
                $filteredRowIndex = $index + 1;
                break;
            }
        }

        if ($filteredRowIndex !== null) {
            $updateValuesF = [
                [$analisa]
            ];

            $updateRangeF = 'SCREENING!K' . $filteredRowIndex;
            $bodyF = new Google_Service_Sheets_ValueRange([
                'values' => $updateValuesF
            ]);


            $sheetsService->spreadsheets_values->update($spreadsheetId, $updateRangeF, $bodyF, [
                'valueInputOption' => 'RAW'
            ]);

            $updateValues = [
                [$user]
            ];

            $updateRangeG = 'SCREENING!L' . $filteredRowIndex;
            $bodyG = new Google_Service_Sheets_ValueRange([
                'values' => $updateValues
            ]);


            $sheetsService->spreadsheets_values->update($spreadsheetId, $updateRangeG, $bodyG, [
                'valueInputOption' => 'RAW'
            ]);


            $updateRangeI = 'SCREENING!M' . $filteredRowIndex;
            $updateValuesI = [
                [$catatan]
            ];
            $bodyI = new Google_Service_Sheets_ValueRange([
                'values' => $updateValuesI
            ]);


            $sheetsService->spreadsheets_values->update($spreadsheetId, $updateRangeI, $bodyI, [
                'valueInputOption' => 'RAW'
            ]);

            return redirect()->route('skrining.data')->with('success', 'Data berhasil dianalisa.');
        } else {
            return redirect()->back()->with('error', 'Data gagal dianalisa.');
        }
    }

    public function approve_analisa_skrining()
    {
        $nik = request()->nik;
        $nama = request()->nama;
        $user = Auth::user()->code_user;
        $analisa = "DONE";

        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';
        $range = 'SCREENING!A:N';

        $sheetsService = new Google_Service_Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $filteredRowIndex = null;

        foreach ($values as $index => $row) {
            if (
                isset($row[0]) && strpos($row[0], $nik) !== false &&
                isset($row[1]) && strpos($row[1], $nama) !== false &&
                isset($row[10]) && $row[10] == 'APPROVE KABAG'
            ) {
                $filteredRowIndex = $index + 1;
                break;
            }
        }

        if ($filteredRowIndex !== null) {
            $updateValuesF = [
                [$analisa]
            ];

            $updateRangeF = 'SCREENING!K' . $filteredRowIndex;
            $bodyF = new Google_Service_Sheets_ValueRange([
                'values' => $updateValuesF
            ]);


            $sheetsService->spreadsheets_values->update($spreadsheetId, $updateRangeF, $bodyF, [
                'valueInputOption' => 'RAW'
            ]);

            $updateValuesI = [
                [$user]
            ];

            $updateRangeI = 'SCREENING!N' . $filteredRowIndex;
            $bodyI = new Google_Service_Sheets_ValueRange([
                'values' => $updateValuesI
            ]);


            $sheetsService->spreadsheets_values->update($spreadsheetId, $updateRangeI, $bodyI, [
                'valueInputOption' => 'RAW'
            ]);

            return redirect()->route('skrining.data')->with('success', 'Data berhasil dianalisa.');
        } else {
            return redirect()->back()->with('error', 'Data gagal dianalisa.');
        }
    }

    public function daftar_analisa_skrining(Request $request)
    {
        $nik = $request->query('nik');
        $nama = $request->query('nama');
        $dttot = $request->query('dttot');
        $dppspm = $request->query('dppspm');
        $judi_online = $request->query('judi_online');
        $pep = $request->query('pep');
        $negative_news = $request->query('negative_news');
        $watch_list = $request->query('watch_list');
        $status = 'TERDAFTAR';
        $analisa = 'ANALISA SKRINING';

        // $this->create_sheet($nik, $nama, $pep, 'TERDAFTAR');
        $client = $this->google_client();
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $spreadsheetId = '1SQfHolSwSHBZhDzzdSSnQhM9JJu2m-KVlRF2wgFviLU';

        $range = 'SCREENING!A:N';

        $sheetsService = new Google_Service_Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $existingValues = $response->getValues();

        $data = [
            $nik,
            $nama,
            $dttot,
            $dppspm,
            $judi_online,
            $pep,
            $negative_news,
            $watch_list,
            Auth::user()->code_user,
            $status,
            $analisa,
        ];

        $filteredData = array_slice($existingValues, 1);

        $cek = array_filter($filteredData, function ($row) {
            return isset($row[10]) && $row[10] === 'DONE';
        });

        if (count($cek) !== count($filteredData)) {

            $notDone = array_filter($filteredData, function ($row) {
                return isset($row[10]) && $row[10] !== 'DONE';
            });

            return redirect()->back()->with('error', 'Data sedang proses' . ' ' . $notDone[0][10]);
        } else {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$data]
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $sheetsService = new Google_Service_Sheets($client);

            $sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

            return redirect()->route('skrining.index')->with('success', 'Data telah dikirim.');
        }


        // $nikExists = false;
        // $namaExists = false;

        // if (!empty($existingValues)) {
        //     foreach ($existingValues as $row) {
        //         if (isset($row[0]) && $row[0] == $nik) {
        //             $nikExists = true;
        //         }
        //         if (isset($row[1]) && $row[1] == $nama) {
        //             $namaExists = true;
        //         }
        //     }
        // }

        // if (!$nikExists && !$namaExists) {
        //     $body = new Google_Service_Sheets_ValueRange([
        //         'values' => [$data]
        //     ]);

        //     $params = [
        //         'valueInputOption' => 'RAW'
        //     ];

        //     $sheetsService = new Google_Service_Sheets($client);

        //     $sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

        //     return redirect()->route('skrining.index')->with('success', 'Data telah dikirim.');
        // } else {
        //     return redirect()->route('skrining.index')->with('error', 'Data sedang dianalisa.');
        // }
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

    private function qrcode($user)
    {
        $ttd = url('https://sipebri.bprbangunarta.co.id/storage/image/tanda_tangan/TTD_' . $user . '.png');

        $logoPath = public_path('assets/img/favicon2.png');
        $qrCode = QrCode::size(500)
            ->format('png')
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate($ttd);

        $qrCodeBase64 = base64_encode($qrCode);

        return $qrCodeBase64;
    }
}
