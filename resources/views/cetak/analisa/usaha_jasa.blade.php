<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Usaha Jasa</title>
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
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">ANALISA USAHA JASA</h4>

        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Staff Analis</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->surveyor }}</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kode Analis</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kode_surveyor }}</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kasi Analis</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kasi }}</td>
            </tr>
        </table>

        <p style="font-weight: bold;">1. KONDISI UMUM</p>
        <table style="margin-top: -1%; margin-left: 1%; border: 3px black;">
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Kode Nasabah</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kode_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Alamat</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">No Telepon</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->no_telp }}</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 19%;">Penggunaan Kredit</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->penggunaan }}</td>
            </tr>
        </table>

        <p style="font-weight: bold;">- JASA</p>
        <p style="margin-top: -1%;">&emsp;1. Penghasilan</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    @foreach ($jasa as $item)
                        <tr>
                            <td style="width: 15px;">{{ $loop->iteration . '.' }}</td>
                            <td style="width: 200px;">{{ $item->nama_usaha }}</td>
                            <td>{{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') ?? 0 }}</td>
                        </tr>
                        <tr style="position:absolute; margin-top: -18px; margin-left: 18px;">
                            <td></td>
                            <td>________________________________________</td>
                        </tr>
                    @endforeach
                    <tr style="position:absolute; margin-top: 30px; margin-left: 0px;">
                        <td style="width: 30%;">Jumlah</td>
                        <td style="width: 123px;"></td>
                        <td>{{ 'Rp. ' . number_format($jasa->totalpendapatan, 0, ',', '.') ?? 0 }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 30px; margin-left: 218px;">
                        <td></td>
                        <td>_______________</td>
                    </tr>
                </table>
            </div>

        </div>
        <br><br><br><br>
        <p style="margin-top: -1%;">&emsp;1. Biaya Pengeluaran</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 200px;">Pajak Kendaraan (Produktif)</td>
                        <td>{{ 'Rp. ' . number_format($jasa->totalpajak, 0, ',', '.') ?? 0 }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: -20px; margin-left: 218px;">
                        <td></td>
                        <td>_______________</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 200px;">Pengeluaran lain-lain</td>
                        <td>{{ 'Rp. ' . number_format($jasa->totallain, 0, ',', '.') ?? 0 }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 20px; margin-left: 0px;">
                        <td style="width: 30%;">Jumlah</td>
                        <td style="width: 128px;"></td>
                        <td>{{ 'Rp. ' . number_format($jasa->totalpengeluaran, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 20px; margin-left: 218px;">
                        <td></td>
                        <td>_______________</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 70px; margin-left: 0px;">
                        <td style="width: 30%;"><b>Pendapatan Jasa</b></td>
                        <td style="width: 100px;"></td>
                        <td>{{ 'Rp. ' . number_format($jasa->lababersih, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 70px; margin-left: 219px;">

                        <td>_______________</td>
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
