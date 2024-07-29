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
        .headers {
            margin-top: 10px;
        }

        .item {
            margin-top: -10px;
        }

        .headers p {
            margin-left: 23px;
        }

        .headers table {
            margin-left: 23px;
            margin-top: -10px;
        }

        .headers table tr td {
            padding-bottom: 5px;
        }

        .ttd p {
            margin-left: -3px;
        }

        .headers .plafon_rsc {
            box-sizing: border-box;
            margin-top: 10px;
            width: 35%;
            margin-bottom: 20px;
        }

        .headers .plafon_rsc th {
            font-weight: bold;
            text-align: center;
        }

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
            border: 1px solid black;
            text-align: center;
        }

        .plafon_rsc th,
        .plafon_rsc tr,
        .plafon_rsc td {
            /* padding-left: 7px; */
            padding-left: 0px;
            /* border: 1px solid black; */
        }

        /* new style div */

        .agunan th,
        .agunan tr,
        .agunan td {
            border: 1px solid black;
        }

        .agunan td {
            padding: 7px;
        }

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

            .page-break {
                page-break-before: always;
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
                        <h2 style="text-align: center;font-size: 12pt;"><u>ANALISA {{ $data->jenis_persetujuan }}
                                KREDIT</u>
                        </h2>
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
                                <h4 style="text-align: center;font-size: 12pt;">TAKSASI JAMINAN</h4>
                                <table style="border:1px solid black;">
                                    <tr style="border:1px solid black;">
                                        <th class="text-center" width="4%" style="border:1px solid black;">No
                                        </th>
                                        <th class="text-center" style="border:1px solid black;">Agunan</th>
                                        <th class="text-center" width="17%" style="border:1px solid black;">Nilai
                                            Taksasi</th>
                                    </tr>
                                    @forelse ($jaminan as $itemj)
                                        <tr style="border:1px solid black;text-transform:uppercase;">
                                            <td class="text-center" width="4%" style="border:1px solid black;">
                                                {{ $loop->iteration }}</td>
                                            <td style="border:1px solid black; padding-left: 10px;">
                                                {{ $itemj->catatan }}.
                                            </td>
                                            <td style="border:1px solid black;text-align:right; padding-right: 10px;">
                                                {{ 'Rp. ' . ' ' . number_format($itemj->nilai_taksasi, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;" colspan="3">
                                                TIDAK ADA AGUNAN
                                        </tr>
                                    @endforelse

                                    @if (count($jaminan) != 0)
                                        @php
                                            $totalTaksasi = $jaminan->sum('nilai_taksasi');
                                        @endphp

                                        <!-- Tampilkan baris dengan total taksasi -->
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;" colspan="2">
                                                Jumlah Nilai Taksasi
                                                Agunan</td>
                                            <td style="border:1px solid black;text-align:right; padding-right: 10px;">
                                                {{ 'Rp. ' . ' ' . number_format($totalTaksasi, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif

                                </table>

                                <table style="border:1px solid black; margin-top:20px; margin-bottom:20px;">
                                    <tr style="border:1px solid black;">
                                        <th class="text-center" width="4%" style="border:1px solid black;">No
                                        </th>
                                        <th class="text-center" style="border:1px solid black;">Posisi Agunan</th>
                                        <th class="text-center" width="17%" style="border:1px solid black;">
                                            Kondisi Agunan</th>
                                    </tr>
                                    <tr style="border:1px solid black;text-transform:uppercase;">
                                        <td class="text-center" width="4%" style="border:1px solid black;">
                                            1 </td>
                                        <td style="border:1px solid black; padding-left: 10px;" width="48%">
                                            {{ $agunan->posisi_agunan }}.
                                        </td>
                                        <td style="border:1px solid black;text-align:left; padding-left: 10px;"
                                            width="48%">
                                            {{ $agunan->kondisi_agunan }}.
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="contents">
                                <div class="heads">
                                    3. Analisa Pembayaran :
                                </div>
                                <div class="title">
                                    Analisa kemampuan likuiditas debitur setiap bulan :
                                </div>

                                {{-- Usaha Perdagangan --}}
                                @forelse ($perdagangan as $itemp)
                                    <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERDAGANGAN</h4>
                                    <table style="margin-top: -10px; margin-bottom: 5px;">
                                        <tr>
                                            <td width="13%">Nama Nasabah</td>
                                            <td class="text-center" width="3%"> : </td>
                                            <td style="text-align: justify;">{{ $itemp->kode_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kode Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $itemp->kode_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $itemp->nama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $itemp->lama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: text-top;">Alamat Usaha</td>
                                            <td class="text-center" style="vertical-align: text-top;"> : </td>
                                            <td style="text-align: justify;">{{ $itemp->lokasi_usaha }}</td>
                                        </tr>
                                    </table>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="7" style="border:1px solid black;">
                                                Biaya Barang Dagang</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;">Nama Barang</th>
                                            <th class="text-center" style="border:1px solid black;">Harga Beli</th>
                                            <th class="text-center" style="border:1px solid black;">Harga Jual</th>
                                            <th class="text-center" style="border:1px solid black;">Laba</th>
                                            <th class="text-center" style="border:1px solid black;">Stok</th>
                                            <th class="text-center" style="border:1px solid black;">%</th>
                                        </tr>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @forelse ($biayaperdagangan as $items)
                                            @if ($items->usaha_kode == $itemp->kode_usaha)
                                                <tr style="border:1px solid black;">
                                                    <td class="text-center" width="4%"
                                                        style="border:1px solid black;">
                                                        {{ $no }}
                                                    </td>
                                                    <td style="border:1px solid black;">&nbsp;
                                                        {{ $items->nama_barang }}</td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        Rp. {{ number_format($items->harga_beli, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        Rp. {{ number_format($items->harga_jual, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        Rp. {{ number_format($items->laba, 0, ',', '.') }} &nbsp;
                                                    </td>
                                                    <td class="text-center" style="border:1px solid black;">
                                                        {{ $items->stok }}</td>
                                                    <td class="text-center" style="border:1px solid black;">
                                                        {{ $items->presentase_laba }}%
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endif
                                        @empty
                                        @endforelse
                                        <tr style="border:1px solid black; colspan:2;">
                                            <td class="text-center" colspan="2" style="border:1px solid black;">
                                                TOTAL</td>
                                            <td class="text-center" style="border:1px solid black;">
                                                Rp. {{ number_format($itemp->total_beli, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center" style="border:1px solid black;">
                                                Rp. {{ number_format($itemp->total_jual, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center" style="border:1px solid black;">
                                                Rp. {{ number_format($itemp->total_laba, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center" style="border:1px solid black;">
                                                {{ number_format($itemp->total_stok, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center" style="border:1px solid black;">
                                                {{ number_format($itemp->total_pl, 2) }} %
                                            </td>
                                        </tr>
                                    </table>

                                    <table style="margin-top: 5px;">
                                        <tr>
                                            <td width="21%">Periode Belanja</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($itemp->belanja_harian, 0, ',', '.') }}
                                                | Harian</td>
                                        </tr>
                                        <tr>
                                            <td>Omset Harian</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($itemp->omset_harian, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Harga Pokok Penjualan</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($itemp->pokok_penjualan, 0, ',', '.') }}
                                                | Omset Harian /
                                                (1
                                                + rata-rata % laba)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Laba Penjualan Harian</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($itemp->laba_harian, 0, ',', '.') }} |
                                                Omset Harian -
                                                Harga Pokok Penjualan</td>
                                        </tr>
                                        <tr>
                                            <td>Laba Penjualan Bulanan</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($itemp->pendapatan, 0, ',', '.') }} |
                                                Laba Penjualan Harian *
                                                30
                                                Hari</td>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="4" style="border:1px solid black;">
                                                Analisa Keuangan</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;">Keterangan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pendapatan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pengeluaran</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">1.</td>
                                            <td style="border:1px solid black;">&nbsp; Pendapatan Dagang Perbulan</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($itemp->pendapatan, 0, ',', '.') }}
                                                &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">2.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Transportasi</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $transportasi = $itemp->transportasi * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($transportasi, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">3.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Bongkar Muat</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $bongkar_muat = $itemp->bongkar_muat * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($bongkar_muat, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">4.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pegawai</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $pegawai = $itemp->pegawai * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($pegawai, 0, ',', '.') }} &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">5.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Gas Telepon Listrik</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $gatel = $itemp->gatel * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($gatel, 0, ',', '.') }} &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">6.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Retribusi</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $retribusi = $itemp->retribusi * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($retribusi, 0, ',', '.') }} &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">7.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Sewa Tempat</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                @php $sewa_tempat = $itemp->sewa_tempat * 30; @endphp
                                                {{ 'Rp.' . ' ' . number_format($sewa_tempat, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">#</td>
                                            <th style="border:1px solid black;">&nbsp; Total</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($itemp->pendapatan, 0, ',', '.') }}
                                                &nbsp;</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($itemp->pengeluaran, 0, ',', '.') }}
                                                &nbsp;</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">8.</td>
                                            <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($itemp->penambahan, 0, ',', '.') }}
                                                &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                Hasil Bersih Usaha</th>
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                {{ 'Rp.' . ' ' . number_format($itemp->laba_bersih, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    </table>
                                @empty
                                @endforelse
                                {{-- Usaha Perdagangan --}}

                                {{-- Analisa Usaha Pertanian --}}
                                @forelse ($pertanian as $item)
                                    <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERTANIAN</h4>

                                    <table>
                                        <tr>
                                            <td width="14%">Nama Nasabah</td>
                                            <td class="text-center" width="3%"> : </td>
                                            <td width="53%" style="text-align: justify;">{{ $item->nama_nasabah }}
                                            </td>

                                            <td width="16%">Luas Milik Sendiri</td>
                                            <td style="text-align: right;" width="3%"> : </td>
                                            <td style="text-align: right;">
                                                {{ number_format($item->luas_sendiri, 0, ',', '.') }} M2 </td>
                                        </tr>
                                        <tr>
                                            <td>Kode Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->kode_usaha }}</td>

                                            <td>Luas Hasil Gadai</td>
                                            <td style="text-align: right;" width="3%"> : </td>
                                            <td style="text-align: right;">
                                                {{ number_format($item->luas_gadai, 0, ',', '.') }} M2 </td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->nama_usaha }}</td>

                                            <td>Luas Hasil Sewa</td>
                                            <td style="text-align: right;" width="3%"> : </td>
                                            <td style="text-align: right;">
                                                {{ number_format($item->luas_sewa, 0, ',', '.') }} M2 </td>
                                        </tr>
                                        <tr>
                                            <td>Sektor Ekonomi</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">Pertanian</td>

                                            <td>Hasil Panen</td>
                                            <td style="text-align: right;" width="3%"> : </td>
                                            <td style="text-align: right;"> {{ $item->hasil_panen }} KW </td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Tanaman</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->jenis_tanaman }}</td>

                                            <td>Harga Per Kwintal</td>
                                            <td style="text-align: right;" width="3%"> : </td>
                                            <td style="text-align: right;">
                                                {{ 'Rp. ' . ' ' . number_format($item->harga, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: text-top;">Alamat Usaha</td>
                                            <td class="text-center" style="vertical-align: text-top;"> : </td>
                                            <td style="text-align: justify;" colspan="4">{{ $item->lokasi_usaha }}
                                            </td>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="4" style="border:1px solid black;">
                                                Analisa Keuangan</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;">Keterangan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pendapatan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pengeluaran</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">1.</td>
                                            <td style="border:1px solid black;">&nbsp; Pendapatan Hasil Panen</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }}
                                                &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">2.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pengolahan Tanah</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pengolahan_tanah, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">3.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Bibit</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->bibit, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">4.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pupuk</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pupuk, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">5.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pestisida</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pestisida, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">6.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pengairan</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pengairan, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">7.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Panen</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->panen, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">8.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Penggarap</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->penggarap, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">9.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Tenaga Kerja</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->tenaga_kerja, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">10.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Pajak</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pajak, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">11.</td>
                                            <td style="border:1px solid black;">&nbsp; Iuran Desa</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->iuran_desa, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">12.</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Amortisasi</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->amortisasi, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">13.</td>
                                            <td style="border:1px solid black;">&nbsp; Pinjaman Bank Lain</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pinjaman_bank, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>

                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">#</td>
                                            <th style="border:1px solid black;">&nbsp; Total</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }}
                                                &nbsp;</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pengeluaran, 0, ',', '.') }}
                                                &nbsp;</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                Hasil Bersih Usaha</th>
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                {{ 'Rp.' . ' ' . number_format($item->laba_bersih, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table>
                                        <tr>
                                            <td width="23%">Hasil Bersih Usaha (70%)</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($item->saving, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Angsuran Pokok</td>
                                            <td class="text-center" width="3%">:</td>
                                            @php
                                                $angsuran = $data->penentuan_plafon / $data->jangka_waktu;
                                            @endphp
                                            <td>{{ 'Rp.' . ' ' . number_format($angsuran, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Proyeksi Penambahan</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($item->penambahan, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pendapatan Perbulan</td>
                                            <td class="text-center" width="3%">:</td>
                                            <td>{{ 'Rp.' . ' ' . number_format($item->laba_perbulan, 0, ',', '.') }} |
                                                Hasil Bersih Usaha
                                                (70%)
                                                - Saving Pokok / 6 Bulan</td>
                                        </tr>
                                    </table>
                                @empty
                                @endforelse
                                {{-- Analisa Usaha Pertanian --}}

                                {{-- Analisa Usaha Jasa --}}
                                @forelse ($jasa as $item)
                                    <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA JASA</h4>
                                    <table>
                                        <tr>
                                            <td width="13%">Nama Nasabah</td>
                                            <td class="text-center" width="3%"> : </td>
                                            <td style="text-align: justify;">{{ $item->nama_nasabah }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kode Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->kode_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->nama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $item->lama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: text-top;">Alamat Usaha</td>
                                            <td class="text-center" style="vertical-align: text-top;"> : </td>
                                            <td style="text-align: justify;">{{ $item->lokasi_usaha }}</td>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="4" style="border:1px solid black;">
                                                Analisa Keuangan</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;">Keterangan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pendapatan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pengeluaran</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">1.</td>
                                            <td style="border:1px solid black;">&nbsp; Pendapatan Usaha</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }}
                                                &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">2.</td>
                                            <td style="border:1px solid black;">&nbsp; Pajak Kendaraan (Produktif)</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->b_pajak, 0, ',', '.') }} &nbsp;
                                            </td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">3.</td>
                                            <td style="border:1px solid black;">&nbsp; Pengeluaran lain-lain</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->b_lainnya, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">#</td>
                                            <th style="border:1px solid black;">&nbsp; Total</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }}
                                                &nbsp;</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp.' . ' ' . number_format($item->pengeluaran, 0, ',', '.') }}
                                                &nbsp;</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                Hasil Bersih Usaha</th>
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                {{ 'Rp.' . ' ' . number_format($item->laba_bersih, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    </table>
                                    <p></p>

                                @empty
                                @endforelse
                                {{-- Analisa Usaha Jasa --}}

                                {{-- Analisa Usaha Lainnya --}}
                                @forelse ($lain as $items)
                                    <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA LAINNYA</h4>
                                    <table>
                                        <tr>
                                            <td width="14%">Nama Nasabah</td>
                                            <td class="text-center" width="3%"> : </td>
                                            <td style="text-align: justify;">{{ $items->nama_nasabah }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kode Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $items->kode_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ketegori Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $items->jenis_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $items->nama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lama Usaha</td>
                                            <td class="text-center"> : </td>
                                            <td style="text-align: justify;">{{ $items->lama_usaha }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: text-top;">Alamat Usaha</td>
                                            <td class="text-center" style="vertical-align: text-top;"> : </td>
                                            <td style="text-align: justify;">{{ $items->lokasi_usaha }}</td>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="7" style="border:1px solid black;">
                                                Biaya Bahan Baku</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;" width="36%">
                                                Bahan Baku</th>
                                            <th class="text-center" style="border:1px solid black;" width="10%">
                                                Jumlah</th>
                                            <th class="text-center" style="border:1px solid black;" width="25%">
                                                Harga</th>
                                            <th class="text-center" style="border:1px solid black;" width="25%">
                                                Total</th>
                                        </tr>

                                        @foreach ($bahan_baku as $item)
                                            @if ($item->usaha_kode != null && $items->kode_usaha == $item->usaha_kode)
                                                <tr style="border:1px solid black;">
                                                    <td class="text-center" width="4%"
                                                        style="border:1px solid black;">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td style="border:1px solid black;">&nbsp;
                                                        {{ $item->bahan_baku }}</td>
                                                    <td class="text-center" style="border:1px solid black;">
                                                        {{ $item->jumlah }}
                                                    </td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        {{ 'Rp. ' . ' ' . number_format($item->harga, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        {{ 'Rp. ' . ' ' . number_format($item->total, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="4" style="border:1px solid black;">
                                                Total</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->total_bahan, 0, ',', '.') }}
                                                &nbsp;</th>
                                        </tr>
                                    </table>

                                    <p></p>

                                    <table style="border:1px solid black;">
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="4" style="border:1px solid black;">
                                                Analisa Keuangan</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" width="4%" style="border:1px solid black;">No
                                            </th>
                                            <th class="text-center" style="border:1px solid black;">Keterangan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pendapatan</th>
                                            <th class="text-center" width="25%" style="border:1px solid black;">
                                                Pengeluaran</th>
                                        </tr>
                                        @foreach ($pendapatanlain as $itemd)
                                            @if ($itemd->usaha_kode != null && $items->kode_usaha == $itemd->usaha_kode)
                                                <tr style="border:1px solid black;">
                                                    <td class="text-center" style="border:1px solid black;">
                                                        {{ $loop->iteration }}</td>
                                                    <td style="border:1px solid black;">&nbsp;
                                                        {{ $itemd->penjualan }}</td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        {{ 'Rp. ' . ' ' . number_format($itemd->nominal, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                    <td style="border:1px solid black;text-align:right;"> &nbsp;
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @foreach ($pengeluaranlain as $itemb)
                                            @if ($itemb->usaha_kode != null && $items->kode_usaha == $itemb->usaha_kode)
                                                <tr style="border:1px solid black;">
                                                    <td class="text-center" style="border:1px solid black;">
                                                        {{ $loop->iteration }}</td>
                                                    <td style="border:1px solid black;">&nbsp;
                                                        {{ $itemb->pengeluaran }}</td>
                                                    <td style="border:1px solid black;text-align:right;"> &nbsp;
                                                    </td>
                                                    <td style="border:1px solid black;text-align:right;">
                                                        {{ 'Rp. ' . ' ' . number_format($itemb->nominal, 0, ',', '.') }}
                                                        &nbsp;</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">6</td>
                                            <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->proyeksi, 0, ',', '.') }}
                                                &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">7</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Bahan Baku</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->total_bahan, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">8</td>
                                            <td style="border:1px solid black;">&nbsp; Biaya Operasional</td>
                                            <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                            <td style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->pengeluaran, 0, ',', '.') }}
                                                &nbsp;</td>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <td class="text-center" style="border:1px solid black;">#</td>
                                            <th style="border:1px solid black;">&nbsp; Total</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->pendapatan, 0, ',', '.') }}
                                                &nbsp;</th>
                                            <th style="border:1px solid black;text-align:right;">
                                                {{ 'Rp. ' . ' ' . number_format($items->total_pengeluaran, 0, ',', '.') }}
                                                &nbsp;</th>
                                        </tr>
                                        <tr style="border:1px solid black;">
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                Hasil Bersih Usaha</th>
                                            <th class="text-center" colspan="2" style="border:1px solid black;">
                                                {{ 'Rp. ' . ' ' . number_format($items->laba_bersih, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    </table>
                                @empty
                                @endforelse

                                <div class="page-break"></div>
                                <h4 style="text-align: center;font-size: 12pt;">PERHITUNGAN KEMAMPUAN KEUANGAN</h4>
                                <p></p>
                                <table class="tables">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Analisa Keuangan</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 3%;">No</th>
                                            <th>Keterangan</th>
                                            <th>Pendapatan</th>
                                            <th>Pengeluaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center;">1</td>
                                            <td>Perdagangan</td>
                                            <td>{{ 'Rp. ' . number_format($usaha->perdagangan, '0', ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">2</td>
                                            <td>Pertanian</td>
                                            <td>{{ 'Rp. ' . number_format($usaha->pertanian, '0', ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">3</td>
                                            <td>Jasa</td>
                                            <td>{{ 'Rp. ' . number_format($usaha->jasa, '0', ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">4</td>
                                            <td>Lain - lain</td>
                                            <td>{{ 'Rp. ' . number_format($usaha->lain, '0', ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Biaya Rumah Tangga</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>KONSUMSI POKOK</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->konsumsi, '0', ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>KESEHATAN</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->kesehatan, '0', ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>PENDIDIKAN</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->pendidikan, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>GAS TELEPON LISTRIK</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->gatel, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>JAJAN ANAK</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->jajan_anak, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>SUMBANGAN SOSIAL</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->sumbangan, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td>ROKOK</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->roko, '0', ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Kewajiban Lainnya</td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td>{{ $keuangan->kewajiban1 }}</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->nominal_kewajiban1, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td>{{ $keuangan->kewajiban2 }}</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->nominal_kewajiban2, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>10</td>
                                            <td>{{ $keuangan->kewajiban3 }}</td>
                                            <td></td>
                                            <td>{{ 'Rp. ' . number_format($keuangan->nominal_kewajiban3, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#</td>
                                            <td style="text-align: center;"><b>TOTAL</b></td>
                                            <td><b>{{ 'Rp. ' . number_format($keuangan->p_usaha, '0', ',', '.') }}</b>
                                            </td>
                                            <td><b>{{ 'Rp. ' . number_format($keuangan->total_pengeluaran, '0', ',', '.') }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="font-weight: bold; text-align:center;">Kemampuan
                                                Keuangan Perbulan
                                            </td>
                                            <td colspan="2" style="font-weight: bold; text-align:center;">
                                                {{ 'Rp. ' . number_format($keuangan->keuangan_perbulan, '0', ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p></p>
                                <div class="items" style="margin-left: 13px;">
                                    Alat liquid debitur =
                                    {{ 'Rp. ' . number_format($keuangan->keuangan_perbulan, '0', ',', '.') }} <br>
                                </div>
                            </div>

                            <div class="contents">
                                <div class="heads">
                                    4. Kemampuan dan Sumber Pembayaran :
                                </div>
                                <div class="items" style="margin-left: 13px; margin-top: 10px;">
                                    @php
                                        $liquid = ($keuangan->keuangan_perbulan * 70) / 100;
                                    @endphp
                                    &nbsp;Debitur masih memiliki kemampuan pembayaran 70% dari alat liquid debitur
                                    sebesar
                                    {{ 'Rp. ' . number_format($liquid, '0', ',', '.') }}
                                </div>
                            </div>

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
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="content" style="margin-top: -27px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <div class="headers">
            <label for="" style="font-weight:bold;">1. &nbsp;&nbsp;&nbsp;PLAFON RSC</label>
            <table class="plafon_rsc">
                <tr>
                    <td style="width: 55%;">Baki Debet</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->baki_debet, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Tunggakan Bunga</td>
                    <td style="width: 2%;">:</td>
                    <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                        {{ number_format($data->tunggakan_bunga, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%; font-weight:bold; text-align:center;">TOTAL</td>
                    <td style="width: 2%; font-weight:bold;">:</td>
                    @php
                        $total_baki = $data->tunggakan_bunga + $data->baki_debet;
                    @endphp
                    <td style="font-weight:bold;"> &nbsp;Rp.
                        {{ number_format($total_baki, '0', ',', '.') }}</td>
                </tr>
            </table>

            <table class="plafon_rsc">
                <tr>
                    <td style="width: 55%;">Bunga Dibayar</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->bunga_dibayar, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Pokok Dibayar</td>
                    <td style="width: 2%;">:</td>
                    <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                        {{ number_format($data->poko_dibayar, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%; font-weight:bold; text-align:center;">PLAFON</td>
                    <td style="width: 2%; font-weight:bold;">:</td>
                    <td style="font-weight:bold;"> &nbsp;Rp.
                        {{ number_format($data->penentuan_plafon, '0', ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="headers">
            <label for="" style="font-weight:bold;">2. &nbsp;&nbsp;&nbsp;BIAYA RSC</label>
            <table class="plafon_rsc">
                <tr>
                    <td style="width: 55%;">Administrasi {{ $data->administrasi }}%</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->administrasi_nominal, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Asuransi Jiwa</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->asuransi_jiwa, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Asuransi TLO</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->asuransi_tlo, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Pokok Dibayar</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->poko_dibayar, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Bunga Dibayar</td>
                    <td style="width: 2%;">:</td>
                    <td> &nbsp;Rp. {{ number_format($data->bunga_dibayar, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%;">Denda</td>
                    <td style="width: 2%;">:</td>
                    <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                        {{ number_format($data->denda_dibayar, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="width: 55%; font-weight:bold; text-align:center;">TOTAL BIAYA</td>
                    <td style="width: 2%; font-weight:bold;">:</td>
                    <td style="font-weight:bold;"> &nbsp;Rp. {{ number_format($data->total, '0', ',', '.') }}
                    </td>
                </tr>
            </table>

            <p>SIMULASI ANGSURAN PLAFON Rp. {{ number_format($data->penentuan_plafon, '0', ',', '.') }},- RATE
                {{ $data->suku_bunga }} %
                {{ $data->metode_rps }} JANGKA WAKTU {{ $data->jangka_waktu }} BULAN</p>
            <p>
                Total Angsuran = Pokok (Rp. {{ number_format($data->angsuran_pokok, '0', ',', '.') }}) + Bunga
                (Rp.
                {{ number_format($data->angsuran_bunga, '0', ',', '.') }}) = Rp.
                {{ number_format($data->angsuran_pokok + $data->angsuran_bunga, '0', ',', '.') }}
            </p>

            <table style="width:97%; margin-right:50px; margin-top: 35px;">
                <tr>
                    <td class="text-left" style="float: left;">

                    </td>
                    <td class="text-right" style="float: right;">
                        <center>
                            <p style="margin-top: -1.5px;"></p>
                            <p>Pamanukan, {{ $data->tgl_usulan }}</p>
                            <p style="margin-top: 40px;"></p>
                            <br>
                            <br>
                            <b>
                                <p>
                                    <font style="text-transform: uppercase;"><u>{{ $data->nama_nasabah }}</u>
                                    </font>
                                </p>
                            </b>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <script>
        window.print();
    </script>
</body>

</html>
