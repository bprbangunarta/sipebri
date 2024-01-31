<?php

namespace App\Http\Controllers;

use Google_Client;
use Google\Service\Sheets;
use Google_Service_Sheets;
use Illuminate\Http\Request;
use Google_Service_Sheets_ValueRange;

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
        return view('perhitungan.spreadsheet.index');
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
        $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';

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

        return redirect()->route('sheet');
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

        // $tgl_lahir = $request->tgl_lahir;
        // $plafon = (int)str_replace(["Rp", " ", "."], "", $request->plafon);
        $sheetName = 'Total Lost Only';
        $range1 = 'C10:G10';
        $range1i = 'I10';
        $rangecol1 = 'C';
        $startnumb = 10;
        $data1 = [
            '1',
            $request->jenis_kendaraan1,
            $request->nopol1,
            $request->jw1,
            $request->tgl_realisasi1,
        ];

        // $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $sheetName . '!' . $range1Nama);
        // $values = $response->getValues();

        // $requestNama = new Google_Service_Sheets_ValueRange([
        //     'values' => [[$request->nama1]],
        // ]);
        // $paramrequestBody1Nama = [
        //     'valueInputOption' => 'RAW',
        // ];
        // $resultNama = $sheetsService->spreadsheets_values->update(
        //     $spreadsheetId,
        //     $sheetName . '!' . $range1Nama,
        //     $requestNama,
        //     $paramrequestBody1Nama
        // );

        foreach ($data1 as $key => $value) {
            $column = chr(ord($rangecol1) + $key);
            $range = $sheetName . '!' . $column . $startnumb;

            $requestBody1 = new Google_Service_Sheets_ValueRange([
                'values' => [[$value]],
            ]);
            $paramrequestBody1 = [
                'valueInputOption' => 'RAW',
            ];
            $resultCtoG = $sheetsService->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $requestBody1,
                $paramrequestBody1
            );
        }

        $requestBody1i = new Google_Service_Sheets_ValueRange([
            'values' => [(int)str_replace(["", " ", "."], "", $request->pertanggungan1)],
        ]);
        $paramrequestBody11i = [
            'valueInputOption' => 'RAW',
        ];
        $response1i = $sheetsService->spreadsheets_values->update(
            $spreadsheetId,
            $sheetName . '!' . $range1i,
            $requestBody1i,
            $paramrequestBody11i
        );
    }

    public function sheet()
    {
        // Inisialisasi Google Client
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Example');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path('credential.json'));

        $sheetsService = new Google_Service_Sheets($client);

        // $spreadsheetId = '1KcgzKMNfA588xKvQItoQ3wy8Ni0Z8NGbr7wpJeBdigk';
        $spreadsheetId = '1IXdjfDjQ5TRhVxKzq2TiG8OzFzPZR8_dy28G9vV-eYE';

        $range = 'Asuransi Jiwa Kematian'; // Gantilah sesuai dengan nama sheet Anda

        // Mendapatkan data dari spreadsheet
        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        // Lakukan sesuatu dengan data, seperti menampilkan di view
        // dd($values);
        return view('perhitungan.spreadsheet.sheet-view', [
            'data' => $values,
        ]);
    }
}
