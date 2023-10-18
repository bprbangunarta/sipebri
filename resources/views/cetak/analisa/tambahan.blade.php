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
                <td style="width: 20%;">Nama SK</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_surveyor }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Kode Analis</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->kasi_kode }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Kasi Analis</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_kasi }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Kode Nasabah</td>
                <td>:</td>
                <td>{{ $data->kode_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Nama Lengkap</td>
                <td>:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Alamat</td>
                <td>:</td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">No Telepon</td>
                <td>:</td>
                <td>{{ $data->no_telp }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Penggunaan Kredit</td>
                <td>:</td>
                <td>{{ $data->penggunaan }}</td>
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
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->modal_kerja, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 200px;">Investasi</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->investasi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">c. </td>
                        <td style="width: 200px;">Konsumtif</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->konsumtif, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">d. </td>
                        <td style="width: 200px;">Pelunasan Kredit</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->pelunasan_kredit, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">e. </td>
                        <td style="width: 200px;">Take Over</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->take_over, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">g. </td>
                        <td style="width: 200px;">Asuransi</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->asuransi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">h. </td>
                        <td style="width: 200px;">Kebutuhan Dana</td>
                        <td>:</td>
                        <td>{{ 'Rp. ' . ' ' . number_format($tambah->kebutuhan_dana, 0, ',', '.') }}</td>
                    </tr>
                    @if (!empty($tambah->nama_lain))
                        <tr>
                            <td style="width: 15px;">i. </td>
                            <td style="width: 200px;">{{ $tambah->nama_lain }}</td>
                            <td>:</td>
                            <td>{{ 'Rp. ' . ' ' . number_format($tambah->nilai_lain, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <p style="margin-top: 20px;"><b>2. Catatan Lain</b></p>
        <br>
        <div class="border-1">
            <div class="penghasilan">
                <textarea name="catatan" id="" cols="80" rows="10"
                    style="border: none; font-family: 'Times New Roman', Times, serif; font-size: 12px;" readonly>{{ $tambah->catatan }}</textarea>
            </div>
            <p style="text-align: center; position:relative; float: right;">Pamanukan, {{ ' ' . $data->hari }} <br>
                Surveyor <br>
                <br>
                <br><br>
                <b>{{ $data->nama_surveyor }}</b>
            </p>
        </div>
        <br><br><br><br><br><br>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
