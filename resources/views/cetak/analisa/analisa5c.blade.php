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
            font-family: "calibri", serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* border: 2px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th:last-child,
        td:last-child {
            border-right: none;
            /* Menghapus border kanan pada sel terakhir setiap baris */
        }

        th {
            background-color: #ffffff;
        }

        input {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        /* .content {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            margin-top: -10px;
            text-align: justify;
        } */

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

        .taksasi {
            border: 1px solid black;
            padding: 2px;
            margin-top: -8px;
        }

        .taksasi th {
            font-size: 14px;
            text-align: center;
            border-bottom: 1px solid black;
            padding: 5px;
        }

        .rg {
            font-size: 12px;
            border-right: 1px solid black;
            padding: 5px;
        }

        .taksasi td {
            position: relative;
            padding-left: 15px;
            padding-top: 1px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <p style="font-weight: bold; margin-top: -8px;">VI. ANALISA 5C</p>
        <div class="taksasi">
            <table>
                <tr>
                    <th style="width: 250px;">KRITERIA</th>
                    <th style="width: 150px;" colspan="2">PENILAIAN</th>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>1. CHARACTER</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">Mobil</td>
                    <td style="width: 200px;">Rp. 100.000.000</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>2. CAPACITY</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>3. CAPITAL</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Sumber Pemodala
                    </td>
                    <td class="rg" tyle="width: 150px;">Modal Sendiri</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>4. COLLATERAL</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Sumber Pemodala
                    </td>
                    <td class="rg" tyle="width: 150px;">Modal Sendiri</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>5. CONDITION</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Persaingan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Kondisi Alam
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;c. Regulasi Pemerintah
                    </td>
                    <td class="rg" tyle="width: 150px;">Meningkat</td>
                    <td style="width: 200px;">Baik</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; position:relative; float: right;">Pamanukan, ......2023 <br>
            Surveyor <br>
            <br>
            <br><br>
            <b>Muhidin Herlambang</b>
        </p>
    </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
