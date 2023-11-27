<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengecekan IDEB</title>
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

        <h4 style="text-align: center;">FORMULIR PERMOHONAN INFORMASI DEBITUR (IDEB) PERSEORANGAN</h4>

        <p>Yang bertanda tangan dibawah ini :</p>

        <table>
            <tr>
                <td>Nomot KTP</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td style="width: 23%;">Nama Sesuai Identitas</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td>Tempat. Tanggal Lahir</td>
                <td class="text-center"> : </td>
                <td>{{ $data->tempat_lahir . ',' . ' ' . $data->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_telp }}</td>
            </tr>
            <tr>
                <td style="position: absolute;vertical-align: text-top;">Alamat Sesuai Identitas</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td>
                    {{ $data->alamat_ktp }}
                </td>
            </tr>
            <tr>
                <td style="position: absolute;">Tujuan Penggunaan</td>
                <td class="text-center"> : </td>
                <td>
                    <input type="checkbox"> Pemeriksaan Calon Debitur&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox"> Dalam Rangka Audit<br>

                    <input type="checkbox"> Monitoring Debitr Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox"> Penanganan Pengaduan Debitur<br>

                    <input type="checkbox"> Melayani Permintaan Debitur
                    <input type="checkbox"> Penilaian Karyawan/Calon Kayyawan
                </td>
            </tr>
        </table>

        <p style="float: right;">Pamanukan, {{ $data->hari }}</p>
        <br>

        <table>
            <tr>
                <td class="text-center">
                    Pelaksana IDEB,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">Unun Nurainun
                    </font>
                </td>
                <td class="text-center">
                    Menyetujui,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">{{
                        $data->kasi_kode }}</font>
                </td>
                <td class="text-center">
                    Pemohon,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">{{
                        $data->surveyor_kode }}</font>
                </td>
            </tr>
        </table>

        <br>
        <hr>

        <h4 style="text-align: center;">FORMULIR PERMOHONAN INFORMASI DEBITUR (IDEB) PERSEORANGAN</h4>

        <p>Yang bertanda tangan dibawah ini :</p>

        <table>
            <tr>
                <td>Nomot KTP</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td style="width: 23%;">Nama Sesuai Identitas</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td>Tempat. Tanggal Lahir</td>
                <td class="text-center"> : </td>
                <td>{{ $data->tempat_lahir . ',' . ' ' . $data->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_telp }}</td>
            </tr>
            <tr>
                <td style="position: absolute;vertical-align: text-top;">Alamat Sesuai Identitas</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td>
                    {{ $data->alamat_ktp }}
                </td>
            </tr>
            <tr>
                <td style="position: absolute;">Tujuan Penggunaan</td>
                <td class="text-center"> : </td>
                <td>
                    <input type="checkbox"> Pemeriksaan Calon Debitur&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox"> Dalam Rangka Audit<br>

                    <input type="checkbox"> Monitoring Debitr Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox"> Penanganan Pengaduan Debitur<br>

                    <input type="checkbox"> Melayani Permintaan Debitur
                    <input type="checkbox"> Penilaian Karyawan/Calon Kayyawan
                </td>
            </tr>
        </table>

        <p style="float: right;">Pamanukan, {{ $data->hari }}</p>
        <br>

        <table>
            <tr>
                <td class="text-center">
                    Pelaksana IDEB,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">Unun Nurainun
                    </font>
                </td>
                <td class="text-center">
                    Menyetujui,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">{{
                        $data->kasi_kode }}</font>
                </td>
                <td class="text-center">
                    Pemohon,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">{{
                        $data->surveyor_kode }}</font>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
