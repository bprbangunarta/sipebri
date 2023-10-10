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

        <h4 style="text-align: center;">ANALISA KUALITATIF</h4>
        <br>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 20%;">Kode Nasabah</td>
                <td>:</td>
                <td>{{ $data->kode_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Nama Pemohon</td>
                <td>:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Alamat</td>
                <td>:</td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
        </table>

        <p style="margin-top: 15px;">1. Karakter Debitur (Sumber Informasi dari Bank Indonesia dan Masyarakat)</p>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 80%;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 500px;">BI Checking (Sumber Informasi SID Bank Indonesia)</td>
                        <td>:</td>
                        <td style="width: 200px;">{{ $kualitatif->bi_checking }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 500px;">Kewajiban Kepada Pihak Lain</td>
                        <td>:</td>
                        <td style="width: 200px;">{{ $kualitatif->kewajiban_pihak_lain }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">c. </td>
                        <td style="width: 500px;">Judi / Urusan Dengan Pihak Berwajib</td>
                        <td>:</td>
                        <td style="width: 200px;">{{ $kualitatif->pihak_berwajib }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">d. </td>
                        <td style="width: 500px;">Hubungan Dengan Tetangga / Masyarakat</td>
                        <td>:</td>
                        <td style="width: 200px;">{{ $kualitatif->hubungan_tetangga }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">e. </td>
                        <td style="width: 500px;">Pengalaman Menjadi Tenaga Kerja Indonesia (TKI)</td>
                        <td>:</td>
                        <td style="width: 200px;">{{ $kualitatif->pengalaman_tki }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <p style="margin-top: 20px;">2. Usaha Calon Debitur Saat Ini</p>
        <br>
        <div class="border-1">
            <div class="penghasilan">
                <table style="border: 1px black; width: 90%; margin-top: -30px;">
                    <tr>
                        <td style="width: 15px;">a. </td>
                        <td style="width: 490px;">Sumber Bahan Baku / Barang Dagangan</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->sumber_bahan }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">b. </td>
                        <td style="width: 490px;">Proses Aktivitas Usaha</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->aktivitas_usaha }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">c. </td>
                        <td style="width: 490px;">Wilayah Operasional</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->wilayah_operasional }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">d. </td>
                        <td style="width: 490px;">Sitem Pembayaran ( Tunai/Tempo/Transfer )</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->pembayaran }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">e. </td>
                        <td style="width: 490px;">Faktor Pendukung Usaha</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->pendukung_usaha }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">f. </td>
                        <td style="width: 490px;">Faktor Pengurang / Kendala Usaha</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->pengurang_usaha }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15px;">g. </td>
                        <td style="width: 490px;">Trade Checking</td>
                        <td>:</td>
                        <td style="width: 300px;">{{ $kualitatif->trade_checking }}</td>
                    </tr>
                </table>
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
