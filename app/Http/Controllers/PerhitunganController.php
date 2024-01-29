<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;
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
