<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pengecekan NIK</title>
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
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">ANALISA USAHA JASA</h4>

        <table style="font-size: 12px;">
            <tr>
                <td style="width: 14%;">Staff Analis</td>
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
        </table>

        <p style="font-weight: bold;">1. KONDISI UMUM</p>
        <table style="margin-top: -1%; margin-left: 1%; border: 3px black;">
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Kode Nasabah</td>
                <td style="width: 2%;">:</td>
                <td>0823737830</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td>Yandi</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">Alamat</td>
                <td style="width: 2%;">:</td>
                <td>Dengan ini saya menyatakan dan menyetujui untuk dilakukan pengecekan NIK</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 16%;">No Telepon</td>
                <td style="width: 2%;">:</td>
                <td>087828046711</td>
            </tr>
            <tr>
                <td style="width: 1%;"></td>
                <td style="width: 19%;">Penggunaan Kredit</td>
                <td style="width: 2%;">:</td>
                <td>Modal Usaha</td>
            </tr>
        </table>

        <p style="font-weight: bold;">- JASA</p>
        <p style="margin-top: -1%;">1. Penghasilan</p>
        <div class="border-1">

            <table style="border: 1px black; margin-top: -1%;">
                <tr>
                    <td>1</td>
                </tr>
            </table>

        </div>
        <p>
            Demikian Surat Pernyataan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <p style="float: right;">Pamanukan, {{ $data->hari }}</p>
        <br>

        <table>
            <tr>
                <td class="text-center">
                    Yang Menyatakan,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">{{ $data->nama_nasabah }}</font>
                </td>
                <td class="text-center">
                    Petugas Pengecek NIK,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">Unun Nurainun
                    </font>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
