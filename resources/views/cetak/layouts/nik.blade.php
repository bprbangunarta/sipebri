<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengecekan NIK</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 0.5cm;
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
                font-size: 12pt;
            }

            .content {
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }
        }
    </style>
</head>

<body>
    <!-- Halaman 1 -->
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">SURAT PERNYATAAN PEMOHON</h4>

        <p>Yang bertanda tangan dibawah ini :</p>

        <table>
            <tr>
                <td style="width: 5%;"></td>
                <td>NIK</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_identitas }}</td>

            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width:5%;">Nama</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
        </table>

        <p>
            Dengan ini saya menyatakan dan menyetujui untuk dilakukan pengecekan NIK, secara online yang terhubung
            dengan sistem Ditjen Dukcapil, maupun secara offline (alat baca card reader), dan hasil pengecekan akan
            dikelola sebagaimana mestinya untuk keperluan BPR/BPRS PT BPR BANGUNARTA.
        </p>

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

        <br>
        <hr>

        <h4 style="text-align: center;">SURAT PERNYATAAN PENDAMPING</h4>

        <p>Yang bertanda tangan dibawah ini :</p>

        <table>
            <tr>
                <td style="width: 5%;"></td>
                <td>NIK</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_identitas_pendamping }}</td>

            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width:5%;">Nama</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->nama_pendamping }}</td>
            </tr>
        </table>

        <p>
            Dengan ini saya menyatakan dan menyetujui untuk dilakukan pengecekan NIK, secara online yang terhubung
            dengan sistem Ditjen Dukcapil, maupun secara offline (alat baca card reader), dan hasil pengecekan akan
            dikelola sebagaimana mestinya untuk keperluan BPR/BPRS PT BPR BANGUNARTA.
        </p>

        <p>
            Demikian Surat Pernyataan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <p style="float: right;">Pamanukan, {{ $data->hari }}</p>
        <br>

        <table>
            <tr>
                <td class="text-center">
                    Yang Menyatakan,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">{{ $data->nama_pendamping }}</font>
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
