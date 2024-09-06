<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google\Service;
use Google\Service\Sheets;
use Google_Service_Sheets;
use Illuminate\Http\Request;
use Google_Service_Sheets_Request;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_ClearValuesRequest;

class PerhitunganController extends Controller
{
    public function flat()
    {
        return view('perhitungan.metode.flat');
    }

    public function efektif_musiman()
    {
        return view('perhitungan.metode.musiman');
    }

    public function simulasi()
    {
        return view('perhitungan.spreadsheet.asuransi_pasific');
    }

    public function simulasi_bumida()
    {
        return view('perhitungan.spreadsheet.asuransi_bumida');
    }

    public function simulasi_tlo()
    {
        return view('perhitungan.spreadsheet.tlo');
    }

    public function add(Request $request)
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        // ID spreadsheet yang ingin Anda akses
        if ($request->query('ajk_name') == 'pasific') {
            $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';
        } else {
            $spreadsheetId = '14r9E9KukXcrATdXX1AotqdDMzPs3mJzBxQkZdquqR50';
        }

        $tgl_lahir = $request->tgl_lahir;
        $plafon = (int)str_replace(["Rp", " ", "."], "", $request->plafon);

        // Data yang ingin Anda tambahkan (misalnya, array dua dimensi)
        $data1 = [
            [$plafon],
            [(int)$request->jw],
        ];

        $data2 = [
            [$tgl_lahir],
            [$request->tgl_realisasi],
        ];

        $data3 = [
            [$request->nama],
        ];
        $data4 = [
            [$request->rps],
        ];
        $data5 = [
            [$request->produk],
        ];

        // Range di spreadsheet tempat Anda ingin menambahkan data
        // $range = 'Asuransi Jiwa Kematian' . '!' . $cellToUpdate; // Gantilah dengan nama sheet dan range yang sesuai
        $range = ['Asuransi Jiwa Kematian!E9', 'Asuransi Jiwa Kematian!I9', 'Asuransi Jiwa Kematian!E6:I6', 'Asuransi Jiwa Kematian!E11', 'Asuransi Jiwa Kematian!E12',]; // Gantilah dengan nama sheet dan range yang sesuai


        // Menyimpan plafon dan jangka waktu
        $requestBody1 = new Google_Service_Sheets_ValueRange([
            'values' => $data1,
        ]);

        $params = [
            'valueInputOption' => 'RAW',
        ];

        $response1 = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range[0],
            $requestBody1,
            $params
        );

        // Menyimpan Tanggal lahir dan realisasi
        $requestBody2 = new Google_Service_Sheets_ValueRange([
            'values' => $data2,
        ]);
        $response2 = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range[1],
            $requestBody2,
            $params
        );

        // Menyimpan Nama
        $requestBody3 = new Google_Service_Sheets_ValueRange([
            'values' => $data3,
        ]);
        $response3 = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range[2],
            $requestBody3,
            $params
        );

        // Menyimpan RPS
        $requestBody4 = new Google_Service_Sheets_ValueRange([
            'values' => $data4,
        ]);
        $response4 = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range[3],
            $requestBody4,
            $params
        );

        // Menyimpan Produk
        $requestBody5 = new Google_Service_Sheets_ValueRange([
            'values' => $data5,
        ]);
        $response5 = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $range[4],
            $requestBody5,
            $params
        );

        if ($request->query('ajk_name') == 'pasific') {
            return redirect()->route('sheet');
        } else {
            return redirect()->route('sheet_bumida');
        }
    }

    public function sheet()
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';

        $range = 'Asuransi Jiwa Kematian'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        return view('perhitungan.spreadsheet.sheet-view', [
            'data' => $values,
        ]);
    }

    public function sheet_bumida()
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        $spreadsheetId = '14r9E9KukXcrATdXX1AotqdDMzPs3mJzBxQkZdquqR50';

        $range = 'Asuransi Jiwa Kematian'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        return view('perhitungan.spreadsheet.sheet-view-bumida', [
            'data' => $values,
        ]);
    }

    public function perhitungan_tlo(Request $request)
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        // ID spreadsheet yang ingin Anda akses
        $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';

        $sheetName = 'Total Lost Only';
        $range1Name = 'E6:I6';
        $range1 = 'C10:G10';
        $range1i = 'I10';
        $rangecol1 = 'C';

        // Hapus Cell tanpa hapus rumus
        $clearRanges = [
            $sheetName . '!C10:C14',
            $sheetName . '!D10:D14',
            $sheetName . '!E10:E14',
            $sheetName . '!F10:F14',
            $sheetName . '!G10:G14',
            $sheetName . '!I10:I14'
        ];

        $clearRequest = new Google_Service_Sheets_ClearValuesRequest();

        foreach ($clearRanges as $clearRange) {
            $response = $sheetsService->spreadsheets_values->clear($spreadsheetId, $clearRange, $clearRequest);
        }

        $data = [];

        for ($i = 1; $i <= 5; $i++) {
            if (!empty($request->{"jenis_kendaraan$i"})) {
                $data[] = [
                    $i,
                    $request->{"jenis_kendaraan$i"},
                    strtoupper($request->{"nopol$i"}),
                    (int) $request->{"jw$i"},
                    Carbon::parse($request->{"tgl_realisasi$i"})->format('d-m-Y'),
                    (int) str_replace(".", "", $request->{"pertanggungan$i"}),
                ];
            }
        }

        $requestNama = new Google_Service_Sheets_ValueRange([
            'values' => [[strtoupper($request->nama)]],
        ]);

        $paramrequestBody1Nama = [
            'valueInputOption' => 'RAW',
        ];
        $resultNama = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $sheetName . '!' . $range1Name,
            $requestNama,
            $paramrequestBody1Nama
        );

        $startnumb = 10;
        $row = $startnumb;

        foreach ($data as $key => $value) {
            $ranges = [
                'C' => $value[0],
                'D' => $value[1],
                'E' => $value[2],
                'F' => $value[3],
                'G' => $value[4],
                'I' => $value[5],
            ];

            foreach ($ranges as $column => $cellValue) {
                // Menggabungkan kolom dan baris untuk mendapatkan range sel
                $range = $sheetName . '!' . $column . $row;

                // Menyimpan nilai ke dalam spreadsheet
                $requestBody1 = new Google_Service_Sheets_ValueRange([
                    'values' => [[$cellValue]],
                ]);
                $paramrequestBody1 = [
                    'valueInputOption' => 'RAW',
                ];
                $result = $sheetsService->spreadsheets_values->update(
                    $spreadsheetId,
                    $range,
                    $requestBody1,
                    $paramrequestBody1
                );
            }
            $row++;
        }

        return self::sheet_tlo();
    }

    public function sheet_tlo()
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        // $spreadsheetId = '1KcgzKMNfA588xKvQItoQ3wy8Ni0Z8NGbr7wpJeBdigk';
        $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';

        $range = 'Total Lost Only'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        // Lakukan sesuatu dengan data, seperti menampilkan di view
        // dd($values);
        return view('perhitungan.spreadsheet.tlo-view', [
            'data' => $values,
        ]);
    }
}
