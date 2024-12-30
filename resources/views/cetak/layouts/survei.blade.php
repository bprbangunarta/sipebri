<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Hasil Survei</title>
    <style>
        @page {
            size: A4;
            margin: 0;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 1cm;
            margin-top: 1cm;
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
            padding: 5px;
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
    @forelse ($data as $item)
        <div class="content">

            <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
            <hr style="border: 1px solid 034871;">

            <h4 style="text-align: center;">LAPORAN HASIL SURVEI</h4>

            <table>
                Pada hari ini ............. tanggal, ........................... telah dilakukan proses survei oleh
                petugas survei PT BPR Bangunarta :
                <tr>
                    <td style="width: 5%;"></td>
                    <td>Nama</td>
                    <td class="text-center"> : </td>
                    <td>{{ $item->petugas }}</td>

                </tr>
                <tr>
                    <td style="width: 5%;"></td>
                    <td style="width:5%;">Jabatan</td>
                    <td class="text-center" style="width: 3%;"> : </td>
                    <td>{{ strtoupper($item->jabatan) }}</td>
                </tr>
            </table>

            <table>
                Survei ke calon nasabah :
                <tr>
                    <td style="width: 5%;"></td>
                    <td>Nama</td>
                    <td class="text-center"> : </td>
                    <td>{{ $item->nama_nasabah }}</td>

                </tr>
                <tr>
                    <td style="width: 5%;"></td>
                    <td style="width:5%;">Alamat</td>
                    <td class="text-center" style="width: 3%;"> : </td>
                    <td>{{ $item->alamat_ktp }}</td>
                </tr>
            </table>

            <p></p>

            <p style="line-height: 1.5; margin-bottom: 15px;">
                Dengan adanya surat ini, kami menyatakan bahwa telah dilakukan proses survei dengan prosedur yang
                berlaku. Survei ini meliputi verifikasi data dan kondisi di lokasi yang terkait, serta observasi
                langsung guna memastikan kesesuaian informasi yang telah diberikan sebelumnya. Hasil dari survei ini
                akan dijadikan dasar dalam pengambilan keputusan selanjutnya. Surat ini sekaligus menjadi bukti bahwa
                survei telah selesai dilaksanakan dengan baik.
            </p>

            <p style="float: right;">Pamanukan, ......................... {{ $item->created_at }}</p>

            <table style="table-layout: fixed;">
                <tr>
                    <td class="text-center"
                        style="min-width: 200px; max-width:500px; word-wrap: break-word; white-space: normal;">
                        Calon Debitur
                        ,<br><br><br><br><br><br><br>

                        <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                            {{ $item->nama_nasabah }}</font>
                    </td>
                    <td style="width: 30%;"></td>
                    <td class="text-center"
                        style="min-width: 200px; max-width:500px; word-wrap: break-word; white-space: normal;">
                        Petugas Survei,<br><br><br><br><br><br><br>

                        <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                            {{ $item->petugas }}
                        </font>
                    </td>
                </tr>
            </table>
        </div>

        <div style="page-break-before: always;"></div>
    @empty
        <tr>
            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
        </tr>
    @endforelse


    <script>
        window.print();
    </script>
</body>

</html>
