<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kemampuan Keuangan</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* border: 2px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th,
        td {
            padding: 1px;
            text-align: left;
            /* border-bottom: 1px solid #000000;
            border-right: 1px solid #000000; */
            /* Menambahkan border pada sisi kanan sel */
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
            margin-top: -10px;
            text-align: justify;
        }

        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.5cm;
            }
        }

        .penghasilan {
            margin-left: 26px;
            margin-top: -14px;
        }

        .taksasi th {
            font-size: 14px;
            text-align: center;
            border: 1px solid black;
            padding: 5px;
        }

        .taksasi td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Nama SK</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kasi }}</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kode Sales</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kode_surveyor }}</td>
            </tr>
            <tr>
                <td style="width: 14%;">Nama Sales</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->surveyor }}</td>
            </tr>
        </table>

        <p style="font-weight: bold;">III. PERHITUNGAN KEMAMPUAN KEUANGAN</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 200px;">Hasil Usaha Perdagangan</td>
                        <td>Rp</td>
                        <td>{{ number_format($usaha['perdagangan'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 200px;">Hasil Usaha Pertanian</td>
                        <td>Rp</td>
                        <td>{{ number_format($usaha['pertanian'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">c. </td>
                        <td style="width: 200px;">Hasil Usaha Jasa</td>
                        <td>Rp</td>
                        <td>{{ number_format($usaha['jasa'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">d. </td>
                        <td style="width: 200px;">Hasil Usaha Pengolahan</td>
                        <td>Rp</td>
                        <td>{{ number_format($usaha['lain'], 0, ',', '.') }}</td>
                    </tr>
                    <tr style="position:absolute; margin-left: 352px;">

                        <td>_______________</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 16px; margin-left: 100px;">
                        <td style="width: 200px;">Jumlah Hasil Usaha</td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td><b>{{ 'Rp. ' . ' ' . number_format($usaha['total'], 0, ',', '.') }}</b></td>
                    </tr>
                </table>
            </div>

        </div>
        <br><br>
        <p style="position: relative; margin-left: 26px;">Biaya Rumah Tangga</p>
        <div>
            <div class="penghasilan" style="margin-left: 47px;">
                <table style="border: 1px black; width: 70%;">
                    @foreach ($biaya as $item)
                        <tr>
                            <td style="width: 10px;">-</td>
                            <td style="width: 183px;">{{ $item->pengeluaran }}</td>
                            <td>Rp</td>
                            <td>{{ number_format($item->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="position:absolute; margin-top: 30px; margin-left: 80px;">
                        <td style="width: 200px; font-size:14px;">Jumlah Biaya Rumah Tangga</td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td>{{ 'Rp.' . ' ' . number_format($totalbiaya, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 30px; margin-left: 331px;">

                        <td>_______________</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 47px; margin-left: 80px;">
                        <td style="width: 200px; font-size:13px;"><b>Kemampuan keuangan per bulan</b></td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td><b>{{ 'Rp.' . ' ' . number_format($kemampuanperbulan, 0, ',', '.') }}</b></td>
                    </tr>
                </table>
            </div>

        </div>
        <br><br><br><br>
        <p style="font-weight: bold;">IV. HARTA KEMPEMILIKAN</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">1. </td>
                        <td style="width: 200px;">Rumah</td>

                        <td style="width: 50px;">:</td>
                        <td>{{ $kepemilikan['rumah'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">2. </td>
                        <td style="width: 200px;">Mobil</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['mobil'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">3. </td>
                        <td style="width: 200px;">Motor</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['motor'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">4. </td>
                        <td style="width: 200px;">Televisi</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['televisi'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">5. </td>
                        <td style="width: 200px;">Komputer</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['komputer'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">6. </td>
                        <td style="width: 200px;">Mesin Cuci</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['mesin_cuci'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">7. </td>
                        <td style="width: 200px;">Kuris Tamu</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['kursi_tamu'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">8. </td>
                        <td style="width: 200px;">Lemari Panjang</td>
                        <td style="width: 45px;">:</td>
                        <td>{{ $kepemilikan['lemari_panjang'] }}</td>
                    </tr>
                    @if (!is_null($kepemilikan['nama_lainnya1']))
                        <tr>
                            <td style="width: 15px;">9. </td>
                            <td style="width: 200px;">{{ $kepemilikan['nama_lainnya1'] }}</td>
                            <td style="width: 45px;">:</td>
                            <td>{{ $kepemilikan['isi_lainnya1'] }}</td>
                        </tr>
                    @elseif (!is_null($kepemilikan['nama_lainnya2']))
                        <tr>
                            <td style="width: 15px;">10. </td>
                            <td style="width: 200px;">{{ $kepemilikan['nama_lainnya2'] }}</td>
                            <td style="width: 45px;">:</td>
                            <td>{{ $kepemilikan['isi_lainnya2'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <p style="font-weight: bold;">V. TAKSASI JAMINAN</p>
        <div>
            <div class="taksasi">
                <table>
                    <tr>
                        <th style="width: 15px;">NO</th>
                        <th style="width: 200px;">AGUNAN</th>
                        <th style="width: 60px;">NILAI TAKSASI</th>
                    </tr>
                    @php $no = 1 @endphp
                    @foreach ($taksasi as $item)
                        <tr>
                            <td style="width: 15px; text-align:center;">{{ $no }}</td>
                            <td tyle="width: 200px;">
                                {{ $item->jenis_agunan . ',' . ' ' . $item->jenis_dokumen . ',' . ' ' . $item->no_dokumen . ',' . ' ' . $item->atas_nama . ',' . ' ' . $item->lokasi }}
                            </td>
                            <td style="width: 60px;">
                                {{ 'Rp.' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}
                            </td>
                        </tr>
                        @php $no++ @endphp
                    @endforeach
                    <tr>
                        <td tyle="width: 200px; text-align: center;" colspan="2">
                            <center><b>Jumlah
                                    Nilai Taksasi</b></center>
                        </td>
                        <td style="width: 60px;">
                            @if (!is_null($taksasi))
                                {{ 'Rp.' . ' ' . number_format($item->total, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
