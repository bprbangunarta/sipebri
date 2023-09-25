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
            margin-left: 14px;
            margin-top: -14px;
        }

        .titik {
            border: none;
            border-top: 1px dotted #000;
            height: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">ANALISA TAMBAHAN</h4>
        <br>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Nama SK</td>
                <td style="width: 2%;">:</td>
                <td>DIDI JUNEIDI</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kode Analis</td>
                <td style="width: 2%;">:</td>
                <td>RDI</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kasi Analis</td>
                <td style="width: 2%;">:</td>
                <td>RIAN DESTILA</td>
            </tr>
            <tr>
                <td>Kode Nasabah</td>
                <td>:</td>
                <td>089933283</td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td>Yandi</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>Pabuaran, Subang</td>
            </tr>
            <tr>
                <td>No Telepon</td>
                <td>:</td>
                <td>087828046711</td>
            </tr>
            <tr>
                <td>Penggunaan Kredit</td>
                <td>:</td>
                <td>Modal Usaha</td>
            </tr>
        </table>

        <p style="margin-top: 20px;"><b>1. Penghasilan</b></p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 70%;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 200px;">Modal Kerja</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 200px;">Investasi</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">c. </td>
                        <td style="width: 200px;">Konsumtif</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">d. </td>
                        <td style="width: 200px;">Pelunasan Kredit</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">e. </td>
                        <td style="width: 200px;">Take Over</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">f. </td>
                        <td style="width: 200px;">Administrasi</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">g. </td>
                        <td style="width: 200px;">Asuransi</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">h. </td>
                        <td style="width: 200px;">Lain-lain</td>
                        <td>:</td>
                        <td>Rp. 100.000.00</td>
                    </tr>
                </table>
            </div>
        </div>
        <p style="margin-top: 20px;"><b>2. Catatan Lain</b></p>
        <br>
        <div class="border-1">
            <div class="penghasilan">
                <table>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
                    </tr>
                    <tr style="position:relative; height: 15px;">
                        <td class="titik"></td>
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
        <br><br><br><br>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
