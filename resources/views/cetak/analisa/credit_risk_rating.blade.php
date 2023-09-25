<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Credit Risk Rating</title>
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

        <p style="font-weight: bold; text-align: center;">PENILAIAN CREDIT RISK RATING</p>
        <div>
            <div class="taksasi">
                <table>
                    <tr>
                        <th style="width: 50%;">JENIS RESIKO</th>
                        <th>RATING RESIKO</th>
                    </tr>
                    <tr>
                        <td style="width: 50%;">1. RESIKO INDUSTRI (SEKTOR EKONOMI)</td>
                        <td>4 (Tinggi)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">2. RESIKO DEBITU</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">a. Karakter</td>
                        <td>3 (Sedang)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">b. Pengalaman Usaha</td>
                        <td>1 (Sangant Rendah)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">c. Nilai Agunan / Kredit</td>
                        <td>2 (Rendah)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">d. Aspek Hukum Agunan</td>
                        <td>2 (Reqndah)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">e. Debt Service Coverage Ratio (DSR)</td>
                        <td>2 (Rendah)</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">f. Jangka Waktu</td>
                        <td>5 (Sangat Tinggi)</td>
                    </tr>
                </table>
            </div>
        </div>
        <br>
        <p style="text-align: center; position:relative; float: right;">Pamanukan, ......2023 <br>
            Surveyor <br>
            <br>
            <br><br>
            <b>Muhidin Herlambang</b>
        </p>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
