<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BA Pemeriksaan Tanah</title>
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

            <h4 style="text-align: center;">BERITA ACARA PEMERIKSAAN DAN PENILAIAN AGUNAN</h4>

            <table>
                Pada hari ini ............. tanggal, ........................... telah dilakukan pemeriksaan kondisi
                jaminan oleh PT BPR Bangunarta dengan Debitur :
                <tr>
                    <td style="width: 5%;"></td>
                    <td>Nama</td>
                    <td class="text-center"> : </td>
                    <td>{{ $item->nama_user }}</td>

                </tr>
                <tr>
                    <td style="width: 5%;"></td>
                    <td style="width:5%;">Jabatan</td>
                    <td class="text-center" style="width: 3%;"> : </td>
                    <td>{{ $item->role_name }}</td>
                </tr>
            </table>

            <table>
                Yang akan dijadikan agunan oleh :
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
            <table style="border: 1px solid #000000;">
                Dengan kondisi jaminan sebagai berikut :
                <tr>
                    <td style="width:35%;" class="br-1">&nbsp; Barang yang dijaminkan</td>
                    <td class="br-1">&nbsp;{{ Str::upper($item->jenis_agunan) }}</td>

                </tr>
                <tr>
                    <td class="br-1">&nbsp; Bukti Kepemilikan</td>
                    <td class="br-1">&nbsp;SETIFIKAT</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; No. Bukti Kepemilikan</td>
                    <td class="br-1">&nbsp;{{ $item->no_dokumen }}</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Atas Nama</td>
                    <td class="br-1">&nbsp;{{ Str::upper($item->atas_nama) }}</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Letak Objek</td>
                    <td class="br-1">&nbsp;{{ $item->lokasi }}</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Luas Tanah Sawah</td>
                    <td class="br-1">&nbsp; . . . . . . . . . . . . x . . . . . . . . . . . . = . . . . . . . . . . .
                        . .
                        .
                        M2</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Luas Tanah Darat</td>
                    <td class="br-1">&nbsp; . . . . . . . . . . . . x . . . . . . . . . . . . = . . . . . . . . . . .
                        . .
                        .
                        M2</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Luas Bangunan Rumah</td>
                    <td class="br-1">&nbsp; . . . . . . . . . . . . x . . . . . . . . . . . . = . . . . . . . . . . .
                        . .
                        .
                        M2</td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Atap Rumah</td>
                    <td class="br-1">&nbsp; Genteng / Asbes / . . . . . . . . . . . . . . . . . . . . . . . . . . . .
                    </td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Lantai</td>
                    <td class="br-1">&nbsp; Keramik / Tegal / Pelur / . . . . . . . . . . . . . . . . . . . . . .
                    </td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Kondisi Bangunan</td>
                    <td class="br-1">&nbsp; Bangunan Tembok Full . . . . . . . . . . . . . . . . . . . . . . . </td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Batas Utara</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Batas Selatan</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Batas Timur</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Batas Barat</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Terawat / Tidak</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Marketable</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; Sengketa / Tidak</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1">&nbsp; IMB, PBB, Asuransi Kebakaran</td>
                    <td class="br-1"></td>
                </tr>
                <tr>
                    <td class="br-1"><b>&nbsp; Nilai Taksasi Jaminan</b></td>
                    <td class="br-1"></td>
                </tr>
            </table>

            <p>
                Demikian Berita Acara Pemeriksaan ini agar dapat digunakan sesuai dengan kepentingannya.
            </p>

            <p style="float: right;">Pamanukan, ......................... {{ $item->thn }}</p>

            <table>
                <tr>
                    <td class="text-center">
                        Calon Debitur
                        ,<br><br><br><br><br>

                        <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                            {{ $item->nama_nasabah }}</font>
                    </td>
                    <td class="text-center">
                        Petugas Survei,<br><br><br><br><br>

                        <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                            {{ $item->nama_user }}</font>
                    </td>
                </tr>
            </table>
        </div>
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
