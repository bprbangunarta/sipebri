<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Analisa Kredit RSC</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.5cm;
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

        .text-hg {
            background-color: #f4ff81;
            color: #000;
            display: inline;
        }

        .content {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            text-align: justify;
        }

        /* new style div */
        .section {
            margin-bottom: 20px;
        }

        .title {
            margin-bottom: 7px;
            margin-left: 15px;
        }

        .contents {
            margin-left: 0;
            margin-top: 5px;
        }

        .item {
            margin-left: 28px;
            margin-bottom: 8px;
        }

        .tables {
            width: 100%;
        }

        .tables td,
        th {
            border: 1px solid black;
            padding: 5px;
        }

        .tables th {
            text-align: center;
        }

        /* new style div */

        @media print {
            body {
                font-size: 10pt;
            }

            .header {
                width: 80%;
                margin-top: 0cm;
                margin-left: 2cm;
            }

            hr {
                position: relative;
                z-index: 5;
                border: 1px solid #161717;
                border-style: double;
                width: 100%;
                margin-left: 0cm;
            }

            .content {
                margin-top: -2cm;
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }
        }
    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <td>
                    <div class="header">
                        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
                        <hr>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="content" style="margin-top: -57px;">

                        <label for="" style="font-weight:bold;">DATA-DATA KREDIT : </label>
                        <table>
                            <tr>
                                <td class="text-center" width="2%"> 1. </td>
                                <td width="30%">Nomor SPK</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->no_spk }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 2. </td>
                                <td width="30%">Nama Nasabah</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->nama_nasabah }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%" style="vertical-align: text-top;"> 3. </td>
                                <td width="30%" style="vertical-align: text-top;">Alamat</td>
                                <td class="text-center" width="3%" style="vertical-align: text-top;"> : </td>
                                <td style="text-align: justify;">
                                    {{ $data->alamat_ktp }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 4. </td>
                                <td width="30%">No. KTP</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->no_identitas }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 5. </td>
                                <td width="30%">No. Telp</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->no_telp }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 6. </td>
                                <td width="30%">Plafon</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">
                                    {{ 'Rp. ' . number_format($data->plafon, '0', ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 7. </td>
                                <td width="30%">Penggunaan</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->keterangan }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 8. </td>
                                <td width="30%">Tanggal Jatuh Tempo</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->tgl_jth_tmp }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 9. </td>
                                <td width="30%">Saldo debet per {{ ' ' . $data->update_pengajuan }}</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">
                                    {{ 'Rp. ' . number_format($data->baki_debet, '0', ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 10. </td>
                                <td width="30%">Klasifikasi Kredit</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;">{{ $data->klasifikasi_kredit }}</td>
                            </tr>
                            <tr>
                                <td class="text-center" width="2%"> 11. </td>
                                <td width="30%">Tunggakan kredit</td>
                                <td class="text-center" width="3%"> : </td>
                                <td style="text-align: justify;"></td>
                            </tr>
                        </table>

                        <p></p>

                        <table style="border: 1px solid black; padding: 100px; box-sizing: border-box; width:100%;">
                            <thead>
                                <tr style="text-align: center; border: 1px solid black;">
                                    <th style="border: 1px solid black;" class="text-center">Tunggakan</th>
                                    <th style="border: 1px solid black;" class="text-center">Jumlah Bulan</th>
                                    <th style="border: 1px solid black;" class="text-center">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border: 1px solid black; padding: 5px;">Pokok</td>
                                    <td style="border: 1px solid black; padding: 5px; text-align:center;">
                                        {{ $data->jml_tgk_pokok }}</td>
                                    <td style="border: 1px solid black; padding: 5px;">
                                        {{ 'Rp. ' . number_format($data->tunggakan_poko, '0', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; padding: 5px;">Bunga</td>
                                    <td style="border: 1px solid black; padding: 5px; text-align:center;">
                                        {{ $data->jml_tgk_bunga }}</td>
                                    <td style="border: 1px solid black; padding: 5px;">
                                        {{ 'Rp. ' . number_format($data->tunggakan_bunga, '0', ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; padding: 5px;">Denda</td>
                                    <td style="border: 1px solid black; padding: 5px; text-align:center;"></td>
                                    <td style="border: 1px solid black; padding: 5px;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; padding: 5px; text-align:center;"
                                        colspan="2">TOTAL</td>
                                    <td style="border: 1px solid black; padding: 5px;">
                                        {{ 'Rp. ' . number_format($data->total_tunggakan, '0', ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <p></p>
                        <br>

                        <label for="" style="font-weight:bold;">PENILAIAN TERHADAP DEBITUR :</label>
                        <div class="container">
                            <div class="heads">
                                1. Kondisi Usaha / Keuangan :
                            </div>

                            <div class="contents">
                                <div class="title">
                                    a. Faktor Teknis :
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_teknis1 }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_teknis2 }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->catatan_faktor_teknis }}
                                </div>

                                <div class="title">
                                    b. Faktor Ekonomi :
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_ekonomi1 }}
                                    {{ 'Rp. ' . number_format($kondisi->nominal_fe1, '0', ',', '.') ?? 'Rp. 0' }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_ekonomi2 }}
                                    {{ 'Rp. ' . number_format($kondisi->nominal_fe2, '0', ',', '.') ?? 'Rp. 0' }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->catatan_faktor_ekonomi }}
                                </div>

                                <div class="title">
                                    c. Faktor Marketing :
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_marketing1 }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_marketing2 }} {{ $kondisi->catatan_faktor_marketing2 }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_marketing2 }} {{ $kondisi->catatan_faktor_marketing3 }}
                                </div>
                                <div class="item">
                                    - {{ $kondisi->catatan_faktor_marketing_lain ?? '-' }}
                                </div>

                                <div class="title">
                                    d. Faktor Rumah Tangga :
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_rumah_tangga }}
                                    {{ 'Rp. ' . number_format($kondisi->biaya_faktor_rumah_tangga, '0', ',', '.') ?? 'Rp. 0' }}
                                    karena {{ $kondisi->catatan_frt ?? '-' }}
                                </div>
                                <div class="item">
                                    - -
                                </div>
                                <div class="item">
                                    - -
                                </div>
                                <div class="item">
                                    - -
                                </div>

                                <div class="title">
                                    e. Faktor Lain :
                                </div>
                                <div class="item">
                                    - {{ $kondisi->faktor_lainnya ?? '-' }}
                                </div>
                            </div>

                            <div class="contents">
                                <div class="heads">
                                    2. Kondisi Agunan :
                                </div>
                                <div class="title">
                                    a. Data Agunan
                                </div>
                                <div class="item" style="text-align: justify;">
                                    <table>
                                        <tr>
                                            <td style="width: 6px;">-</td>
                                            <td style="text-align: justify; margin-left:5px;">{{ $data->catatan }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="title">
                                    b. Posisi Agunan
                                </div>
                                <div class="item">
                                    - {{ $agunan->posisi_agunan ?? '-' }}
                                </div>

                                <div class="title">
                                    c. Kondisi Agunan
                                </div>
                                <div class="item">
                                    - {{ $agunan->kondisi_agunan ?? '-' }}
                                </div>

                                <div class="title">
                                    d. Nilai Taksasi Agunan
                                </div>
                                <div class="item">
                                    - {{ 'Rp. ' . number_format($agunan->nilai_taksasi, '0', ',', '.') ?? 'Rp. 0' }}
                                </div>
                            </div>

                            <div class="contents">
                                <div class="heads">
                                    3. Analisa Pembayaran :
                                </div>
                                <div class="title">
                                    Analisa kemampuan likuiditas debitur setiap bulan :
                                </div>
                                <div class="title">
                                    a. Pendapatan
                                </div>
                                <div class="item">
                                    <table class="tables">
                                        <thead>
                                            <tr>
                                                <td style="width: 60%; text-align:center;">Nama Usaha</td>
                                                <td style="text-align:center;">Nominal</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Perdagangan</td>
                                                <td>{{ 'Rp. ' . number_format($usaha->perdagangan, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pertanian</td>
                                                <td>{{ 'Rp. ' . number_format($usaha->pertanian, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jasa</td>
                                                <td>{{ 'Rp. ' . number_format($usaha->jasa, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Lain - lain</td>
                                                <td>{{ 'Rp. ' . number_format($usaha->lain, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;">TOTAL</td>
                                                <td>{{ 'Rp. ' . number_format($usaha->total_usaha, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="title">
                                    b. Biaya
                                </div>
                                <div class="item">
                                    <table class="tables">
                                        <thead>
                                            <tr>
                                                <td style="width: 60%; text-align:center;">Nama Biaya</td>
                                                <td style="text-align:center;">Nominal</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Biaya Konsumsi Pokok</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->konsumsi, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kesehatan</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->kesehatan, '0', ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pendidikan</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->pendidikan, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Gas, Air, Telepon & Listrik</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->gatel, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jajan Anak</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->jajan_anak, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Sumbangan Sosial</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->sumbangan, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Rokok</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->roko, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Kewajiban Lain</td>
                                            </tr>

                                            <tr>
                                                <td style="text-align: center;">TOTAL</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->total, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="title">
                                    c. Kewajiban
                                </div>
                                <div class="item">
                                    <table class="tables">
                                        <thead>
                                            <tr>
                                                <td style="width: 60%; text-align:center;">Nama Kewajiban</td>
                                                <td style="width: 60%; text-align:center;">Nominal</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ Str::upper($biaya->kewajiban1) }}</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->nominal_kewajiban1, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ Str::upper($biaya->kewajiban2) }}</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->nominal_kewajiban2, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ Str::upper($biaya->kewajiban3) }}</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->nominal_kewajiban3, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="text-align:
                                                    center;">
                                                    TOTAL</td>
                                                <td>{{ 'Rp. ' . number_format($biaya->total_kewajiban, '0', ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="contents">
                                <div class="heads">
                                    4. Kemampuan dan Sumber Pembayaran :
                                </div>
                                <div class="items" style="margin-left: 13px;">
                                    Alat liquid debitur =
                                    {{ 'Rp. ' . number_format($biaya->likuidasi, '0', ',', '.') }} <br>
                                    @php
                                        $liquid = ($biaya->likuidasi * 70) / 100;
                                    @endphp
                                    Debitur masih memiliki kemampuan pembayaran 70% dari alat liquid debitur sebesar
                                    {{ 'Rp. ' . number_format($liquid, '0', ',', '.') }}
                                </div>
                            </div>

                            <br>
                            <br>

                            <div class="contents" style="margin-top: 10px;">
                                <div class="heads">
                                    5. Jumlah Setoran = (Angsuran Bunga
                                    {{ 'Rp. ' . number_format($data->angsuran_bunga, '0', ',', '.') }}) + (Angsuran
                                    Pokok
                                    {{ 'Rp. ' . number_format($data->angsuran_pokok, '0', ',', '.') }}) =
                                    {{ 'Rp. ' . number_format($data->total_angsuran, '0', ',', '.') }}
                                </div>
                            </div>

                            <div class="contents" style="margin-top: 10px;">
                                <div class="heads">
                                    6. RC = (Jumlah Setoran/bln / Kemampuan Keuangan/bln) x 100 = {{ $data->rc }}
                                    %
                                </div>
                            </div>

                            <div class="contents" style="margin-top: 10px; margin-left: 13px;">
                                <div class="heads">
                                    Ket:
                                </div>
                                <table style="width: 30%;">
                                    <tr>
                                        <td>
                                            < 50% </td>
                                        <td>:</td>
                                        <td>Baik</td>
                                    </tr>
                                    <tr>
                                        <td>> 50% s/d 59 </td>
                                        <td>:</td>
                                        <td>Cukup Baik</td>
                                    </tr>
                                    <tr>
                                        <td>> 59% </td>
                                        <td>:</td>
                                        <td>Kurang Baik</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="contents" style="margin-top: 10px;">
                                <div class="heads">
                                    7. Syarat-syarat :
                                </div>
                            </div>

                            <div class="contents" style="margin-top: 10px; margin-left: 13px;">
                                <table>
                                    <tr>
                                        <td width="2%">1. </td>
                                        <td width="20%">Sebelum Realisasi</td>
                                        <td width="2%">:</td>
                                        <td>{{ $syarat->sebelum_realisasi }}</td>
                                    </tr>
                                    <tr>
                                        <td>2. </td>
                                        <td>Syarat Tambahan</td>
                                        <td>:</td>
                                        <td>{{ $syarat->syarat_tambahan }}</td>
                                    </tr>
                                    <tr>
                                        <td>3. </td>
                                        <td>Syarat Lainnya</td>
                                        <td>:</td>
                                        <td>{{ $syarat->syarat_lain }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                </td>
            </tr>

            <table style="margin-right:50px; margin-top: -40px;">
                <tr>
                    <td width="60%"></td>
                    <td class="text-center">
                        <p style="margin-top: -1.5px;"></p>
                        Pamanukan, {{ $data->update_pengajuan }} <br>
                        <p style="margin-top: 40px;"></p>
                        <center>
                            <img src="{{ asset('storage/image/qr_code/' . $qr) }}" width="100" height="100"
                                style="margin-top:-30px;">
                        </center>
                        <b>
                            <font style="text-transform: uppercase;">{{ $data->name }}</font>
                        </b>
                    </td>
                </tr>
            </table>
            </div>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>

</html>
