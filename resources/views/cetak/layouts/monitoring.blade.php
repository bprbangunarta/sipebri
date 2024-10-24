<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Monitoring Kredit</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* border: 1px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th,
        td {
            padding: 1px;
            text-align: left;
            /* Menambahkan border pada sisi kanan sel */
        }

        .br-1 {
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
        }

        th:last-child,
        td:last-child {
            border-right: none;
            /* Menghapus border kanan pada sel terakhir setiap baris */
        }

        th {
            background-color: #f2f2f2;
        }

        input {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        .content {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            text-align: justify;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .content {
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    {{-- Data Nasabah --}}
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <table>
            <tr>
                <td class="text-center" width="27%">
                    @if ($data->photo_nasabah == null)
                        <img src="{{ asset('assets/img/default.png') }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @else
                        <img src="{{ asset('storage/image/photo/' . $data->photo_nasabah) }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @endif
                </td>
                <td class="text-center" width="27%">
                    @if ($data->photo == null)
                        <img src="{{ asset('assets/img/default.png') }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @else
                        <img src="{{ asset('storage/image/photo/' . $data->photo) }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @endif
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Photo Pemohon</b> <br>
                    {{ $data->nama_nasabah }}
                </td>
                <td class="text-center">
                    <b>Photo Pendamping</b> <br>
                    {{ $data->nama_pendamping }}
                </td>
                <td></td>
            </tr>
        </table>

        <p></p>

        <table>
            <tr>
                <td class="text-center" width="2%"> 1. </td>
                <td width="17%">Kode Pengajuan</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->kode_pengajuan }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 2. </td>
                <td width="17%">Nama Nasabah</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 3. </td>
                <td width="17%" style="vertical-align: text-top;">Alamat</td>
                <td class="text-center" width="3%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    {{ $data->alamat_ktp }}
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 4. </td>
                <td width="17%">No. KTP</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 5. </td>
                <td width="17%">No. Telp</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->no_telp }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 6. </td>
                <td width="17%">Metode RPS</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->metode_rps }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 7. </td>
                <td width="17%">Plafon</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">
                    @if (!empty($data->temp_plafon))
                        {{ 'Rp.' . ' ' . number_format($data->temp_plafon, 0, ',', '.') }}
                    @else
                        {{ 'Rp.' . ' ' . number_format($data->plafon, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 8. </td>
                <td width="17%">Penggunaan</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->penggunaan }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 9. </td>
                <td width="17%">Tgl. Pendaftaran</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ \Carbon\Carbon::parse($data->tgl_pengajuan)->format('Y-m-d') }}
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 10. </td>
                <td width="17%">Jangka Waktu</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 11. </td>
                <td width="17%">Suku Bunga</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->suku_bunga }} % p.a</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 12. </td>
                <td width="17%">Surveyor</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->nama_surveyor }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 13. </td>
                <td width="17%">Wilayah</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->nama_kantor }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 14. </td>
                <td width="17%">Kasi Analis</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->nama_kasi }}</td>
            </tr>
        </table>

        <p></p>

        <h4 style="text-align: center;font-size: 12pt;">MONITORING PENGAJUAN KREDIT</h4>

        <table style="border:1px solid black;">
            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;"> NO </th>
                <th class="text-center" width="25%" style="border:1px solid black;">TAHAPAN</th>
                <th class="text-center" width="21%" style="border:1px solid black;">PELAKSANAAN</th>
                <th class="text-center" width="25%" style="border:1px solid black;">PETUGAS</th>
                <th class="text-center" width="25%" style="border:1px solid black;">PARAF</th>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 1. </td>
                <td style="border:1px solid black;">&nbsp; Permohonan</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 2. </td>
                <td style="border:1px solid black;">&nbsp; Terima Berkas</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 3. </td>
                <td style="border:1px solid black;">&nbsp; Proses Survey</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 4. </td>
                <td style="border:1px solid black;">&nbsp; Proses Analisa</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 5. </td>
                <td style="border:1px solid black;">&nbsp; Berkas Naik Kasi</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 6. </td>
                <td style="border:1px solid black;">&nbsp; Berkas Diterima Kas</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 7. </td>
                <td style="border:1px solid black;">&nbsp; Survey Kasi</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 8. </td>
                <td style="border:1px solid black;">&nbsp; Keputusan Komite</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 9. </td>
                <td style="border:1px solid black;">&nbsp; Notifikasi Kredit</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <th style="border:1px solid black;vertical-align: text-top;" colspan="3" rowspan="4">Keterangan
                    :</th>
                <th class="text-center" style="border:1px solid black;">Agen BRI Link</th>
                <th class="text-center" style="border:1px solid black;">Alamat</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;"><br><br><br></th>
                <th class="text-center" style="border:1px solid black;"></th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;" colspan="2">Paraf Nasabah</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;" colspan="2"><br><br><br></th>
            </tr>

        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
