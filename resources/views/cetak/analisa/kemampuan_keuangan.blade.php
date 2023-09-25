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

        <table style="font-size: 12px;">
            <tr>
                <td style="width: 14%;">Nama SK</td>
                <td style="width: 2%;">:</td>
                <td>Sutrisno</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kode Sales</td>
                <td style="width: 2%;">:</td>
                <td>RDI</td>
            </tr>
            <tr>
                <td style="width: 14%;">Nama Sales</td>
                <td style="width: 2%;">:</td>
                <td>RIAN DESTILA</td>
            </tr>
        </table>

        <p style="font-weight: bold;">III. PERHITUNGAN KEMAMPUAN KEUANGAN</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 200px;">Gaji Suami Perawat</td>
                        <td>Rp</td>
                        <td>10.000.000</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 200px;">Gaji Suami Perawat</td>
                        <td>Rp</td>
                        <td>10.000.000</td>
                    </tr>
                    <tr style="position:absolute; margin-left: 352px;">

                        <td>_______________</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 16px; margin-left: 100px;">
                        <td style="width: 200px;">Jumlah Hasil Usaha</td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td><b>Rp. 10.000.000</b></td>
                    </tr>
                </table>
            </div>

        </div>
        <br><br>
        <p style="position: relative; margin-left: 26px;">Biaya Rumah Tangga</p>
        <div>
            <div class="penghasilan" style="margin-left: 47px;">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 10px;">-</td>
                        <td style="width: 183px;">Gaji Suami Perawat</td>
                        <td>Rp</td>
                        <td>10.000.000</td>
                    </tr>
                    <tr>
                        <td style="width: 10px;">-</td>
                        <td style="width: 183px;">Gaji Suami Perawat</td>
                        <td>Rp</td>
                        <td>10.000.000</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 20px; margin-left: 80px;">
                        <td style="width: 200px;">Jumlah Biaya Rumah Tangga</td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td><b>Rp. 10.000.000</b></td>
                    </tr>
                    <tr style="position:absolute; margin-top: 20px; margin-left: 331px;">

                        <td>_______________</td>
                    </tr>
                    <tr style="position:absolute; margin-top: 37px; margin-left: 80px;">
                        <td style="width: 200px; font-size:14px;"><b>Kemampuan keuangan perbulan</b></td>
                        <td style="width: 45px;"></td>
                        <td></td>
                        <td><b>Rp. 10.000.000</b></td>
                    </tr>
                </table>
            </div>

        </div>
        <br><br><br>
        <p style="font-weight: bold;">IV. HARTA KEMPEMILIKAN</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">1. </td>
                        <td style="width: 100px;">Rumah</td>
                        <td>:</td>
                        <td>Permanen</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">2. </td>
                        <td style="width: 100px;">Mobil</td>
                        <td>:</td>
                        <td>1 Unit</td>
                    </tr>
                </table>
            </div>
        </div>
        <br>
        <p style="font-weight: bold;">V. TAKSASI JAMINAN</p>
        <div>
            <div class="taksasi">
                <table>
                    <tr>
                        <th style="width: 15px;">NO</th>
                        <th style="width: 200px;">AGUNAN</th>
                        <th style="width: 60px;">NILAI TAKSASI</th>
                    </tr>
                    <tr>
                        <td style="width: 15px; text-align:center;">1</td>
                        <td tyle="width: 200px;">Mobil</td>
                        <td style="width: 60px;">Rp. 100.000.000</td>
                    </tr>
                    <tr>
                        <td tyle="width: 200px; text-align: center;" colspan="2"><b>Jumlah Nilai Taksasi</b></td>
                        <td style="width: 60px;">Rp. 100.000.000</td>
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
