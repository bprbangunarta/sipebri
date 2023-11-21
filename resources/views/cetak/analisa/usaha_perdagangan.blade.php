<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Usaha Perdagangan</title>
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
            margin-left: 40px;
            margin-top: -14px;
        }

        .perdagangan tr,
        .perdagangan th,
        .perdagangan td {
            border: 1px solid black;
        }

        .perdagangan th {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">ANALISA USAHA PERDAGANGAN</h4>

        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Staff Analis</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->surveyor }}</td> --}}
            </tr>
            <tr>
                <td style="width: 14%;">Kode KAA</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->kode_surveyor }}</td> --}}
            </tr>
            <tr>
                <td style="width: 14%;">Kasi Analis</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->kasi }}</td> --}}
            </tr>
        </table>

        <p style="font-weight: bold;">I. KONDISI UMUM</p>
        <table style="margin-top: -1%; margin-left: 1%; border: 3px black;">
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 29%;">Kode Nasabah</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->kode_nasabah }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 29%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->nama_nasabah }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 29%;">Alamat</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->alamat_ktp }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 29%;">No Telepon</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->no_telp }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 29%;">Penggunaan Kredit</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->penggunaan }}</td> --}}
            </tr>
        </table>

        <p style="font-weight: bold;">II. PENGHASILAN USAHA</p>
        <table style="margin-top: -1%; margin-left: 3%;">
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;"> <b>- PERDAGANGAN</b></td>
                <td style="width: 2%;"></td>
                <td></td>
                {{-- <td>{{ $data->kode_nasabah }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;">Kode Nasabah</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->kode_nasabah }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->nama_nasabah }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;">Alamat</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->alamat_ktp }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;">No Telepon</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->no_telp }}</td> --}}
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 27%;">Penggunaan Kredit</td>
                <td style="width: 2%;">:</td>
                <td></td>
                {{-- <td>{{ $data->penggunaan }}</td> --}}
            </tr>
        </table>
        <div class="border-1">
            <div style="margin: 0;">
                <table class="perdagangan"
                    style="border: 1px solid black; width: 100%; margin-top: 25px; border-collapse: collapse;">
                    <thead style="text-align: justify;">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Harga Beli Satuan</th>
                            <th>Harga Jual Satuan</th>
                            <th>Laba</th>
                            <th>% Laba</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td style="text-align: center;">1</td>
                                <td>IKAN KOI SEDANG</td>
                                <td>Rp. 17.000</td>
                                <td>Rp. 45.000</td>
                                <td>Rp. 28.000</td>
                                <td style="text-align: center;">164.71 %</td>
                                <td style="text-align: center;">0</td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: center;">Jumlah</td>
                            <td>Rp. 170.000</td>
                            <td>Rp. 450.000</td>
                            <td>Rp. 280.000</td>
                            <td>90 %</td>
                            <td>0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        <br><br><br><br>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td>Jumlah Barang Dagangan (Modal)</td>
                        <td>Jumlah Barang Dagangan (Modal)</td>
                    </tr>
                </table>
            </div>
        </div>
        <br><br><br><br>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
