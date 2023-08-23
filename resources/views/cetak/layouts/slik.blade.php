<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pengecekan IDEB</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: 'Calibri', serif;
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

        input,
        textarea {
            border: 1px solid #000000;
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
            /* text-align: justify; */
        }

        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.2cm;
            }
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">FORMULIR PERMOHONAN INFORMASI DEBITUR (IDEB) PERSEORANGAN</h4>

        <table>
            <tr>
                <td>Nomot KTP</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_identitas }}</td>
                {{-- <td><input type="text" style="width: 100%;" value="{{ $data->no_identitas }}"></td> --}}
            </tr>
            <tr>
                <td style="width: 23%;">Nama Sesuai Identitas</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
                {{-- <td><input type="text" style="width: 100%;" value="{{ $data->nama_nasabah }}"></td> --}}
            </tr>
            <tr>
                <td>Tempat. Tanggal Lahir</td>
                <td class="text-center"> : </td>
                <td>{{ $data->tempat_lahir . ',' . ' ' . $data->tanggal_lahir }}</td>
                {{-- <td><input type="text" style="width: 100%;"
                        value=""></td> --}}
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td class="text-center"> : </td>
                <td>{{ $data->no_telp }}</td>
                {{-- <td><input type="text" style="width: 100%;" value="{{ $data->no_telp }}"></td> --}}
            </tr>
            <tr>
                <td>Alamat Sesuai Identitas</td>
                <td class="text-center"> : </td>
                <td>
                    {{ $data->alamat_ktp }}
                    {{-- <textarea style="width: 100.1%;"></textarea> --}}
                </td>
            </tr>
            <tr>
                <td>Tujuan Penggunaan</td>
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
                    Pelaksana IDEB,<br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">Unun Nurainun</font>
                </td>
                <td class="text-center">
                    Menyetujui,<br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">{{ $data->kasi_kode }}</font>
                </td>
                <td class="text-center">
                    Pemohon,<br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">{{ $data->surveyor_kode }}</font>
                </td>
            </tr>
        </table>
        {{-- <button onclick=" printPage()">Cetak Halaman</button> --}}
    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>
