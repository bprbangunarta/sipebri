<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisa Kredit</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
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
                font-size: 10pt;
            }

            .content {
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
    {{-- Data Nasabah --}}
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <table>
            <tr>
                <td class="text-center" width="27%">
                    @if ($cetak->photo_nasabah == null)
                        <img src="{{ asset('assets/img/default.png') }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @else
                        <img src="{{ asset('storage/image/photo/' . $cetak->photo_nasabah) }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @endif
                </td>
                <td class="text-center" width="27%">
                    @if ($cetak->photo == null)
                        <img src="{{ asset('assets/img/default.png') }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @else
                        <img src="{{ asset('storage/image/photo/' . $cetak->photo) }}"
                            style="width:150px;hight:225px;border: 1px solid black;">
                    @endif
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Photo Pemohon</b> <br>
                    {{ $cetak->nama_nasabah }}
                </td>
                <td class="text-center">
                    <b>Photo Pendamping</b> <br>
                    {{ $cetak->nama_pendamping }}
                </td>
                <td></td>
            </tr>
        </table>

        <p></p>

        <table>
            <tr>
                <td class="text-center" width="2%"> 1. </td>
                <td width="17%">Kode Pengajuan</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->kode_pengajuan }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 2. </td>
                <td width="17%">Nama Nasabah</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->nama_nasabah }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 3. </td>
                <td width="17%" style="vertical-align: text-top;">Alamat</td>
                <td class="text-center" width="3%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    {{ $cetak->alamat_ktp }}
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 4. </td>
                <td width="17%">No. KTP</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->no_identitas }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 5. </td>
                <td width="17%">No. Telp</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->no_telp }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 6. </td>
                <td width="17%">Metode RPS</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->metode_rps }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 7. </td>
                <td width="17%">Plafon</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ 'Rp.' . ' ' . number_format($cetak->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 8. </td>
                <td width="17%">Penggunaan</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->penggunaan }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 9. </td>
                <td width="17%">Tgl. Pendaftaran</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ \Carbon\Carbon::parse($cetak->created_at)->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 10. </td>
                <td width="17%">Jangka Waktu</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 11. </td>
                <td width="17%">Suku Bunga</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->suku_bunga }} % p.a</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 12. </td>
                <td width="17%">Surveyor</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->nama_surveyor }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 13. </td>
                <td width="17%">Wilayah</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->nama_kantor }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 14. </td>
                <td width="17%">Kasi Analis</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $cetak->nama_kasi }}</td>
            </tr>
        </table>

        <p></p>

        <h4 style="text-align: center;font-size: 12pt;">MONITORING PENGAJUAN KREDIT</h4>

        <table style="border:1px solid black;">
            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;"> NO </th>
                <th class="text-center" width="20%" style="border:1px solid black;">TAHAPAN</th>
                <th class="text-center" width="21%" style="border:1px solid black;">PELAKSANAAN</th>
                <th class="text-center" width="30%" style="border:1px solid black;">PETUGAS</th>
                <th class="text-center" width="25%" style="border:1px solid black;">PARAF</th>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 1. </td>
                <td style="border:1px solid black;">&nbsp; Permohonan</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->tgl_pengajuan)->format('Y-m-d') }}</td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_cs }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 2. </td>
                <td style="border:1px solid black;">&nbsp; Terima Berkas</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->tgl_nasabah)->format('Y-m-d') }}</td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_input_nasabah }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 3. </td>
                <td style="border:1px solid black;">&nbsp; Proses Survey</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->tgl_survei)->format('Y-m-d') }}</td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_input_survei }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 4. </td>
                <td style="border:1px solid black;">&nbsp; Proses Analisa</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->analisa_kredit)->format('Y-m-d') }}</td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_surveyor }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 5. </td>
                <td style="border:1px solid black;">&nbsp; Berkas Naik Kasi</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 6. </td>
                <td style="border:1px solid black;">&nbsp; Berkas Diterima Kas</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 7. </td>
                <td style="border:1px solid black;">&nbsp; Survey Kasi</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 8. </td>
                <td style="border:1px solid black;">&nbsp; Keputusan Komite</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 9. </td>
                <td style="border:1px solid black;">&nbsp; Notifikasi Kredit</td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <th style="border:1px solid black;vertical-align: text-top;" colspan="3" rowspan="4">Keterangan
                    :</th>
                <th class="text-center" style="border:1px solid black;">Agen BRI Link</th>
                <th class="text-center" style="border:1px solid black;">Alamat</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;"><br><br><br></th>
                <th class="text-center" style="border:1px solid black;"></th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;" colspan="2">Paraf Nasabah</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" style="border:1px solid black;" colspan="2"><br><br><br></th>
            </tr>

        </table>
    </div>

    {{-- Analisa Usaha Perdagangan --}}
    @forelse ($perdagangan as $itemp)
        <div class="page-break"></div>
        <div class="content" style="margin-top: -57px;">
            <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
            <hr style="border: 1px solid 034871;">

            <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERDAGANGAN</h4>

            <table>
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
                    <th class="text-center" colspan="7" style="border:1px solid black;">Biaya Barang Dagang</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
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
                    @foreach ($items as $item)
                        @if ($item->usaha_kode == $itemp->kode_usaha)
                            <tr style="border:1px solid black;">
                                <td class="text-center" width="4%" style="border:1px solid black;">
                                    {{ $no }}
                                </td>
                                <td style="border:1px solid black;">&nbsp; {{ $item->nama_barang }}</td>
                                <td style="border:1px solid black;text-align:right;">
                                    Rp. {{ number_format($item->harga_beli, 0, ',', '.') }} &nbsp;</td>
                                <td style="border:1px solid black;text-align:right;">
                                    Rp. {{ number_format($item->harga_jual, 0, ',', '.') }} &nbsp;</td>
                                <td style="border:1px solid black;text-align:right;">
                                    Rp. {{ number_format($item->laba, 0, ',', '.') }} &nbsp;</td>
                                <td class="text-center" style="border:1px solid black;">{{ $item->stok }}</td>
                                <td class="text-center" style="border:1px solid black;">{{ $item->presentase_laba }}%
                                </td>
                            </tr>
                            @php
                                $no++;
                            @endphp
                        @endif
                    @endforeach
                @empty
                @endforelse
                <tr style="border:1px solid black; colspan:2;">
                    <td class="text-center" colspan="2" style="border:1px solid black;">TOTAL</td>
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

            <p></p>

            <table>
                <tr>
                    <td width="21%">Periode Belanja</td>
                    <td class="text-center" width="3%">:</td>
                    <td>{{ 'Rp.' . ' ' . number_format($itemp->belanja_harian, 0, ',', '.') }} | Harian</td>
                </tr>
                <tr>
                    <td>Omset Harian</td>
                    <td class="text-center" width="3%">:</td>
                    <td>{{ 'Rp.' . ' ' . number_format($itemp->omset_harian, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Harga Pokok Penjualan</td>
                    <td class="text-center" width="3%">:</td>
                    <td>{{ 'Rp.' . ' ' . number_format($itemp->pokok_penjualan, 0, ',', '.') }} | Omset Harian /
                        (1
                        + rata-rata % laba)
                    </td>
                </tr>
                <tr>
                    <td>Laba Penjualan Harian</td>
                    <td class="text-center" width="3%">:</td>
                    <td>{{ 'Rp.' . ' ' . number_format($itemp->laba_harian, 0, ',', '.') }} | Omset Harian -
                        Harga Pokok Penjualan</td>
                </tr>
                <tr>
                    <td>Laba Penjualan Bulanan</td>
                    <td class="text-center" width="3%">:</td>
                    <td>{{ 'Rp.' . ' ' . number_format($itemp->laba_bersih, 0, ',', '.') }} | Laba Penjualan Harian *
                        30
                        Hari</td>
                </tr>
            </table>

            <p></p>

            <table style="border:1px solid black;">
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="4" style="border:1px solid black;">Analisa Keuangan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;">Keterangan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pendapatan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pengeluaran</th>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">1.</td>
                    <td style="border:1px solid black;">&nbsp; Pendapatan Dagang Perbulan</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($itemp->pendapatan, 0, ',', '.') }} &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">2.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Transportasi</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        @php $transportasi = $itemp->transportasi * 30; @endphp
                        {{ 'Rp.' . ' ' . number_format($transportasi, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Bongkar Muat</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        @php $bongkar_muat = $itemp->bongkar_muat * 30; @endphp
                        {{ 'Rp.' . ' ' . number_format($bongkar_muat, 0, ',', '.') }} &nbsp;</td>
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
                        {{ 'Rp.' . ' ' . number_format($sewa_tempat, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">#</td>
                    <th style="border:1px solid black;">&nbsp; Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($itemp->pendapatan, 0, ',', '.') }} &nbsp;</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($itemp->pengeluaran, 0, ',', '.') }} &nbsp;</th>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">8.</td>
                    <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($itemp->penambahan, 0, ',', '.') }} &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                    <th class="text-center" colspan="2" style="border:1px solid black;">
                        {{ 'Rp.' . ' ' . number_format($itemp->laba_bersih, 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    @empty
    @endforelse

    {{-- Analisa Usaha Pertanian --}}
    @forelse ($pertanian as $item)
        <div class="page-break"></div>
        <div class="content" style="margin-top: -57px;">
            <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
            <hr style="border: 1px solid 034871;">

            <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERTANIAN</h4>

            <table>
                <tr>
                    <td width="14%">Nama Nasabah</td>
                    <td class="text-center" width="3%"> : </td>
                    <td width="53%" style="text-align: justify;">{{ $item->nama_nasabah }}</td>

                    <td width="16%">Luas Milik Sendiri</td>
                    <td style="text-align: right;" width="3%"> : </td>
                    <td style="text-align: right;">{{ number_format($item->luas_sendiri, 0, ',', '.') }} M2 </td>
                </tr>
                <tr>
                    <td>Kode Usaha</td>
                    <td class="text-center"> : </td>
                    <td style="text-align: justify;">{{ $item->kode_usaha }}</td>

                    <td>Luas Hasil Gadai</td>
                    <td style="text-align: right;" width="3%"> : </td>
                    <td style="text-align: right;"> {{ number_format($item->luas_gadai, 0, ',', '.') }} M2 </td>
                </tr>
                <tr>
                    <td>Nama Usaha</td>
                    <td class="text-center"> : </td>
                    <td style="text-align: justify;">{{ $item->nama_usaha }}</td>

                    <td>Luas Hasil Sewa</td>
                    <td style="text-align: right;" width="3%"> : </td>
                    <td style="text-align: right;"> {{ number_format($item->luas_sewa, 0, ',', '.') }} M2 </td>
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
                    <td style="text-align: right;"> {{ 'Rp. ' . ' ' . number_format($item->harga, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: text-top;">Alamat Usaha</td>
                    <td class="text-center" style="vertical-align: text-top;"> : </td>
                    <td style="text-align: justify;" colspan="4">{{ $item->lokasi_usaha }}</td>
                </tr>
            </table>

            <p></p>

            <table style="border:1px solid black;">
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="4" style="border:1px solid black;">Analisa Keuangan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;">Keterangan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pendapatan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pengeluaran</th>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">1.</td>
                    <td style="border:1px solid black;">&nbsp; Pendapatan Hasil Panen</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }} &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">2.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Pengolahan Tanah</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pengolahan_tanah, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Bibit</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->bibit, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">4.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Pupuk</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pupuk, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">5.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Pestisida</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pestisida, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">6.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Pengairan</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pengairan, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">7.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Panen</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->panen, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">8.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Penggarap</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->penggarap, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">9.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Tenaga Kerja</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->tenaga_kerja, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">10.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Pajak</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pajak, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">11.</td>
                    <td style="border:1px solid black;">&nbsp; Iuran Desa</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->iuran_desa, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">12.</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Amortisasi</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->amortisasi, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">13.</td>
                    <td style="border:1px solid black;">&nbsp; Pinjaman Bank Lain</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pinjaman_bank, 0, ',', '.') }} &nbsp;</td>
                </tr>

                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">#</td>
                    <th style="border:1px solid black;">&nbsp; Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }} &nbsp;</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pengeluaran, 0, ',', '.') }} &nbsp;</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                    <th class="text-center" colspan="2" style="border:1px solid black;">
                        {{ 'Rp.' . ' ' . number_format($item->laba_bersih, 0, ',', '.') }}</th>
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
                        $angsuran = $item->plafon / $item->jangka_waktu;
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
                    <td>{{ 'Rp.' . ' ' . number_format($item->laba_perbulan, 0, ',', '.') }} | Hasil Bersih Usaha
                        (70%)
                        - Saving Pokok / 6 Bulan</td>
                </tr>
            </table>
        </div>
    @empty
    @endforelse


    {{-- Analisa Usaha Jasa --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        @forelse ($jasa as $item)
            <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA JASA</h4>

            <table>
                <tr>
                    <td width="13%">Nama Nasabah</td>
                    <td class="text-center" width="3%"> : </td>
                    <td style="text-align: justify;">{{ $cetak->nama_nasabah }}</td>
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
                    <th class="text-center" colspan="4" style="border:1px solid black;">Analisa Keuangan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;">Keterangan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pendapatan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pengeluaran</th>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">1.</td>
                    <td style="border:1px solid black;">&nbsp; Pendapatan Usaha</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }} &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">2.</td>
                    <td style="border:1px solid black;">&nbsp; Pajak Kendaraan (Produktif)</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->b_pajak, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Pengeluaran lain-lain</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->b_lainnya, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">#</td>
                    <th style="border:1px solid black;">&nbsp; Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pendapatan, 0, ',', '.') }} &nbsp;</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp.' . ' ' . number_format($item->pengeluaran, 0, ',', '.') }} &nbsp;</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                    <th class="text-center" colspan="2" style="border:1px solid black;">
                        {{ 'Rp.' . ' ' . number_format($item->laba_bersih, 0, ',', '.') }}</th>
                </tr>
            </table>
            <br>
        @empty
        @endforelse
    </div>

    {{-- Analisa Usaha Lainnya --}}
    @forelse ($lain as $items)
        <div class="page-break"></div>
        <div class="content" style="margin-top: -57px;">
            <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
            <hr style="border: 1px solid 034871;">

            <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA LAINNYA</h4>

            <table>
                <tr>
                    <td width="14%">Nama Nasabah</td>
                    <td class="text-center" width="3%"> : </td>
                    <td style="text-align: justify;">{{ $cetak->nama_nasabah }}</td>
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
                    <th class="text-center" colspan="7" style="border:1px solid black;">Biaya Bahan Baku</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;" width="36%">Bahan Baku</th>
                    <th class="text-center" style="border:1px solid black;" width="10%">Jumlah</th>
                    <th class="text-center" style="border:1px solid black;" width="25%">Harga</th>
                    <th class="text-center" style="border:1px solid black;" width="25%">Total</th>
                </tr>
                {{-- @forelse ($bahan as $item)
                    @if ($item->bahan_baku != null)
                        <tr style="border:1px solid black;">
                            <td class="text-center" width="4%" style="border:1px solid black;">
                                {{ $loop->iteration }}
                            </td>
                            <td style="border:1px solid black;">&nbsp; {{ $item->bahan_baku }}</td>
                            <td class="text-center" style="border:1px solid black;">{{ $item->jumlah }}</td>
                            <td style="border:1px solid black;text-align:right;">
                                {{ 'Rp. ' . ' ' . number_format($item->harga, 0, ',', '.') }} &nbsp;</td>
                            <td style="border:1px solid black;text-align:right;">
                                {{ 'Rp. ' . ' ' . number_format($item->total, 0, ',', '.') }} &nbsp;</td>
                        </tr>
                    @endif
                @empty
                @endforelse --}}
                @foreach ($bahan as $item)
                    @foreach ($item as $item_bahan)
                        @if ($item_bahan->bahan_baku != null && $items->kode_usaha == $item_bahan->usaha_kode)
                            <tr style="border:1px solid black;">
                                <td class="text-center" width="4%" style="border:1px solid black;">
                                    {{ $loop->iteration }}
                                </td>
                                <td style="border:1px solid black;">&nbsp; {{ $item_bahan->bahan_baku }}</td>
                                <td class="text-center" style="border:1px solid black;">{{ $item_bahan->jumlah }}
                                </td>
                                <td style="border:1px solid black;text-align:right;">
                                    {{ 'Rp. ' . ' ' . number_format($item_bahan->harga, 0, ',', '.') }} &nbsp;</td>
                                <td style="border:1px solid black;text-align:right;">
                                    {{ 'Rp. ' . ' ' . number_format($item_bahan->total, 0, ',', '.') }} &nbsp;</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="4" style="border:1px solid black;">Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->total_bahan, 0, ',', '.') }} &nbsp;</th>
                </tr>
            </table>

            <p></p>

            <table style="border:1px solid black;">
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="4" style="border:1px solid black;">Analisa Keuangan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;">Keterangan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pendapatan</th>
                    <th class="text-center" width="25%" style="border:1px solid black;">Pengeluaran</th>
                </tr>
                @foreach ($du as $itemd)
                    @foreach ($itemd as $item_du)
                        @if ($item_du->usaha_kode != null && $items->kode_usaha == $item_du->usaha_kode)
                            <tr style="border:1px solid black;">
                                <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                                <td style="border:1px solid black;">&nbsp; {{ $item_du->penjualan }}</td>
                                <td style="border:1px solid black;text-align:right;">
                                    {{ 'Rp. ' . ' ' . number_format($item_du->nominal, 0, ',', '.') }} &nbsp;</td>
                                <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
                {{-- @forelse ($du as $itemd)
                    <tr style="border:1px solid black;">
                        <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                        <td style="border:1px solid black;">&nbsp; {{ $itemd->penjualan }}</td>
                        <td style="border:1px solid black;text-align:right;">
                            {{ 'Rp. ' . ' ' . number_format($itemd->nominal, 0, ',', '.') }} &nbsp;</td>
                        <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
                    </tr>
                @empty
                @endforelse --}}

                {{-- @forelse ($bu as $itemb)
                    <tr style="border:1px solid black;">
                        <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                        <td style="border:1px solid black;">&nbsp; {{ $itemb->pengeluaran }}</td>
                        <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                        <td style="border:1px solid black;text-align:right;">
                            {{ 'Rp. ' . ' ' . number_format($itemb->nominal, 0, ',', '.') }} &nbsp;</td>
                    </tr>
                @empty
                @endforelse --}}
                @foreach ($bu as $itemb)
                    @foreach ($itemb as $item_bu)
                        @if ($item_du->usaha_kode != null && $items->kode_usaha == $item_du->usaha_kode)
                            <tr style="border:1px solid black;">
                                <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                                <td style="border:1px solid black;">&nbsp; {{ $item_bu->pengeluaran }}</td>
                                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                                <td style="border:1px solid black;text-align:right;">
                                    {{ 'Rp. ' . ' ' . number_format($item_bu->nominal, 0, ',', '.') }} &nbsp;</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach

                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">6</td>
                    <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->proyeksi, 0, ',', '.') }} &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">7</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Bahan Baku</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->total_bahan, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">7</td>
                    <td style="border:1px solid black;">&nbsp; Biaya Operasional</td>
                    <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->pengeluaran, 0, ',', '.') }} &nbsp;</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">#</td>
                    <th style="border:1px solid black;">&nbsp; Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->pendapatan, 0, ',', '.') }} &nbsp;</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->total_pengeluaran, 0, ',', '.') }} &nbsp;</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                    <th class="text-center" colspan="2" style="border:1px solid black;">
                        {{ 'Rp. ' . ' ' . number_format($items->laba_bersih, 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>

    @empty
    @endforelse

    {{-- Kemampuan Keuangan || Harta Kepemilikan || Taksasi Jaminan --}}

    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">PERHITUNGAN KEMAMPUAN KEUANGAN</h4>
        <table style="border:1px solid black;">
            <tr style="border:1px solid black;">
                <th class="text-center" colspan="4" style="border:1px solid black;">Analisa Keuangan</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                <th class="text-center" style="border:1px solid black;">Keterangan</th>
                <th class="text-center" width="25%" style="border:1px solid black;">Pendapatan</th>
                <th class="text-center" width="25%" style="border:1px solid black;">Pengeluaran</th>
            </tr>

            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">1.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Perdagangan</td>
                <td style="border:1px solid black;text-align:right;">
                    {{ 'Rp. ' . ' ' . number_format($total_usaha->laba_bersih_perdagangan, 0, ',', '.') }} &nbsp;
                </td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Pertanian</td>
                <td style="border:1px solid black;text-align:right;">
                    {{ 'Rp. ' . ' ' . number_format($total_usaha->laba_bersih_pertanian, 0, ',', '.') }} &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Jasa</td>
                <td style="border:1px solid black;text-align:right;">
                    {{ 'Rp. ' . ' ' . number_format($total_usaha->laba_bersih_jasa, 0, ',', '.') }} &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Lainnya</td>
                <td style="border:1px solid black;text-align:right;">
                    {{ 'Rp. ' . ' ' . number_format($total_usaha->laba_bersih_lainnya, 0, ',', '.') }} &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            @forelse ($keuangan as $items)
                <tr style="border:1px solid black;">
                    <td style="border:1px solid black;" colspan="4">&nbsp; Biaya Rumah Tangga</td>
                </tr>
                @forelse ($bu_keuangan as $key => $item)
                    @if (!is_null($item->pengeluaran) && $key <= 6)
                        <tr style="border:1px solid black;">
                            <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                            <td style="border:1px solid black;">&nbsp; {{ $item->pengeluaran }}</td>
                            <td style="border:1px solid black;text-align:right;"></td>
                            <td style="border:1px solid black;text-align:right;">
                                {{ 'Rp. ' . ' ' . number_format($item->nominal, 0, ',', '.') }} &nbsp;</td>
                        </tr>
                    @endif
                @empty
                @endforelse

                <tr style="border:1px solid black;">
                    <td style="border:1px solid black;" colspan="4">&nbsp; Kewajiban Lainnya</td>
                </tr>
                @forelse ($bu_keuangan as $key => $item)
                    @if ($key > 6 && $key <= 9)
                        <tr style="border:1px solid black;">
                            <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                            <td style="border:1px solid black;">&nbsp; {{ $item->pengeluaran }}</td>
                            <td style="border:1px solid black;text-align:right;"></td>
                            <td style="border:1px solid black;text-align:right;">
                                {{ 'Rp. ' . ' ' . number_format($item->nominal, 0, ',', '.') }} &nbsp;</td>
                        </tr>
                    @endif
                @empty
                @endforelse
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">#</td>
                    <th style="border:1px solid black;">&nbsp; Total</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($total_usaha->total_laba_usaha, 0, ',', '.') }} &nbsp;</th>
                    <th style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($items->bu_total, 0, ',', '.') }} &nbsp;
                    </th>
                </tr>

                <tr style="border:1px solid black;">
                    <th class="text-center" colspan="2" style="border:1px solid black;">Kemampuan Keuangan
                        Perbulan
                    </th>
                    <th class="text-center" colspan="2" style="border:1px solid black;">
                        {{ 'Rp. ' . ' ' . number_format($items->keuangan_perbulan, 0, ',', '.') }}</th>
                </tr>
            @empty
            @endforelse

        </table>

        @if ($cetak->produk_kode != 'KTA')
            <h4 style="text-align: center;font-size: 12pt;">HARTA KEPEMILIKAN</h4>
            <table style="border:1px solid black;">
                <tr style="border:1px solid black;">
                    <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                    <th class="text-center" style="border:1px solid black;">Nama Harta</th>
                    <th class="text-center" width="20%" style="border:1px solid black;">Keterangan</th>
                </tr>

                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">1.</td>
                    <td style="border:1px solid black;">&nbsp; Rumah</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->rumah }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">2.</td>
                    <td style="border:1px solid black;">&nbsp; Mobil</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->mobil }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Motor</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->motor }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Televisi</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->televisi }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">4.</td>
                    <td style="border:1px solid black;">&nbsp; Komputer</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->komputer }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">5.</td>
                    <td style="border:1px solid black;">&nbsp; Mesin Cuci</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->mesin_cuci }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">6.</td>
                    <td style="border:1px solid black;">&nbsp; Kursi Tamu</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->kursi_tamu }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">7.</td>
                    <td style="border:1px solid black;">&nbsp; Lemari Panjang</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->lemari_panjang }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">8.</td>
                    <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->nama_lain1 }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">9.</td>
                    <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->nama_lain2 }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">10.</td>
                    <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->nama_lain3 }}</td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" width="4%" style="border:1px solid black;">11.</td>
                    <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                    <td class="text-center" style="border:1px solid black;">{{ $items->nama_lain4 }}</td>
                </tr>
            </table>
        @endif

        <h4 style="text-align: center;font-size: 12pt;">TAKSASI JAMINAN</h4>
        <table style="border:1px solid black;">
            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                <th class="text-center" style="border:1px solid black;">Agunan</th>
                <th class="text-center" width="17%" style="border:1px solid black;">Nilai Taksasi</th>
            </tr>
            @forelse ($jaminan as $itemj)
                <tr style="border:1px solid black;text-transform:uppercase;">
                    <td class="text-center" width="4%" style="border:1px solid black;">
                        {{ $loop->iteration }}</td>
                    @if ($itemj->jenis_jaminan == 'Kendaraan')
                        <td style="border:1px solid black;">
                            BPKB {{ $itemj->jenis_agunan }}, {{ $itemj->merek }} {{ $itemj->tipe_kendaraan }},
                            {{ $itemj->tahun }}, {{ $itemj->no_rangka }}, {{ $itemj->no_mesin }},
                            {{ $itemj->no_polisi }}, {{ $itemj->no_dokumen }}, {{ $itemj->warna }},
                            {{ $itemj->atas_nama }}.
                        </td>
                        <td style="border:1px solid black;text-align:right;">
                            {{ 'Rp. ' . ' ' . number_format($itemj->nilai_taksasi, 0, ',', '.') }}</td>
                    @elseif ($itemj->jenis_jaminan == 'Tanah')
                        <td style="border:1px solid black;">
                            SERTIFIKAT {{ $itemj->jenis_jaminan }} NO {{ $itemj->no_dokumen }}, LUAS
                            {{ number_format($itemj->luas, 0, ',', '.') }} M2, ATAS NAMA {{ $itemj->atas_nama }}.
                        </td>
                        <td style="border:1px solid black;text-align:right;">
                            {{ 'Rp. ' . ' ' . number_format($itemj->nilai_taksasi, 0, ',', '.') }}</td>
                    @elseif ($itemj->jenis_jaminan == 'Lainnya')
                        <td style="border:1px solid black;">
                            @if ($itemj->nama_jenis_dokumen == 'Kartu Jamsostek')
                                KARTU DAN SALDO JAMSOSTEK
                            @else
                                {{ $itemj->nama_jenis_dokumen }}
                            @endif
                            ATAS NAMA {{ $itemj->atas_nama }} NO {{ $itemj->no_dokumen }}.
                        </td>
                        <td style="border:1px solid black;text-align:right;">
                            {{ 'Rp. ' . ' ' . number_format($itemj->nilai_taksasi, 0, ',', '.') }}</td>
                    @endif
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
                    <td class="text-center" style="border:1px solid black;" colspan="2">Jumlah Nilai Taksasi
                        Agunan</td>
                    <td style="border:1px solid black;text-align:right;">
                        {{ 'Rp. ' . ' ' . number_format($totalTaksasi, 0, ',', '.') }}
                    </td>
                </tr>
            @endif

        </table>
    </div>




    {{-- Analisa 5C --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA 5C</h4>
        <table style="border:1px solid black;">
            <tr>
                <th class="text-center">Kriteria</th>
                <th class="text-center" colspan="2">Penilaian</th>
            </tr>

            {{-- Character --}}
            <tr>
                <th style="border-top:1px solid black;border-right:1px solid black;" width="40%">&nbsp; 1.
                    Character</th>
                <th style="border-top:1px solid black;border-right:1px solid black;" width="40%"></th>
                <th style="border-top:1px solid black;" width="20%"></th>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Tidak Melakukan Hal-Hal
                    Tercela</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->perbuatan_tercela }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Terbuka dan Konsisten</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->konsisten }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Gaya Hidup</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->gaya_hidup }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Kepatuhan Terhadap
                    Kewajiban</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->kepatuhan }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Keharmonisan Rumah Tangga
                </td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->harmonis }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Hubungan Sosial dengan
                    Lingkungan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->hubungan_sosial }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Pengendalian Emosi</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $character->pengendalian_emosi }}</td>
                <td>&nbsp; -</td>
            </tr>

            {{-- Capacity --}}
            <tr>
                <th style="border-right:1px solid black;">
                    <br>
                    &nbsp; 2. Capacity
                </th>
                <th style="border-right:1px solid black;" width="25%"></th>
                <th width="25%"></th>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Riwayat Hidup</td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Pengalaman Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->pengalaman_usaha }}</td>
                <td>&nbsp; </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Pertumbuhan Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->pertumbuhan_usaha }}</td>
                <td>&nbsp; </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3.
                    kontinuitas Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->kontinuitas }}</td>
                <td>&nbsp; </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Aset / Kekayaan</td>
                <td style="border-right:1px solid black;">{{ $capacity->kontinuitas }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Aset Terkait Usaha (Likuid)</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->aset_terkait_usaha }}</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Aset Diluar Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->aset_diluar_usaha }}</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Repacyment Capacity</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->rc }}%</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Pendataan Laporan Keuangan
                </td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->laporan_keuangan }}</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Pengalaman Kredit Masa
                    Lalu</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->catatan_kredit }}</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. SLIK</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->kondisi_slik }}</td>
                <td>&nbsp; {{ $capacity->evaluasi_capacity }}</td>
            </tr>

            {{-- Capital --}}
            <tr>
                <th style="border-right:1px solid black;">
                    <br>
                    &nbsp; 3. Capital
                </th>
                <th style="border-right:1px solid black;" width="30%"></th>
                <th width="30%"></th>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Sumber Permodalan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $capacity->capital_sumber_modal }}</td>
                <td>&nbsp; {{ $capacity->capital_evaluasi_capital }}</td>
            </tr>

            {{-- Collateral --}}
            <tr>
                <th style="border-right:1px solid black;">
                    <br>
                    &nbsp; 4. Collateral
                </th>
                <th style="border-right:1px solid black;" width="30%"></th>
                <th width="30%"></th>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Kepemilikan</td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Agunan Utama</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->agunan_utama }}</td>
                <td>&nbsp; {{ $collateral->evaluasi_collateral }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Agunan Tambahan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->agunan_tambahan }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Legalitas Kepemilikan</td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Agunan Utama</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->legalitas_agunan }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Agunan Tambahan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->legalitas_agunan_tambahan }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Mudah Diuangkan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->mudah_diuangkan }}</td>
                <td>&nbsp; {{ $collateral->evaluasi_collateral }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Stabilitas Harga</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->stabilitas_harga }}</td>
                <td>&nbsp; Kurang Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Lokasi atau Kondisi Fisik
                    Agunan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->lokasi_shm }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Kendaraan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->kondisi_kendaraan }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    SHM</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->lokasi_shm }}</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Permohonan Thd Taksasi
                    Agunan</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->taksasi_agunan }}%</td>
                <td>&nbsp; {{ $collateral->evaluasi_collateral }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Aspek Hukum</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $collateral->aspek_hukum }}</td>
                <td>&nbsp; {{ $collateral->evaluasi_collateral }}</td>
            </tr>

            {{-- Condition --}}
            <tr>
                <th style="border-right:1px solid black;">
                    <br>
                    &nbsp; 5. Condition
                </th>
                <th style="border-right:1px solid black;" width="30%"></th>
                <th width="30%"></th>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Persaingan Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $condition->persaingan_usaha }}</td>
                <td>&nbsp; {{ $condition->evaluasi_condition }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Kondisi Alam</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $condition->kondisi_alam }}</td>
                <td>&nbsp; {{ $condition->evaluasi_condition }}</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Regulisai Pemerintah</td>
                <td style="border-right:1px solid black;">&nbsp; {{ $condition->regulasi_pemerintah }}</td>
                <td>&nbsp; {{ $condition->evaluasi_condition }}</td>
            </tr>
        </table>
    </div>

    {{-- CRR || Analisa Kualitatif || Kebutuhan Dana --}}

    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">CREDIT RISK RATING (CRR)</h4>
        <table style="border:1px solid black;">
            <tr>
                <th class="text-center" width="50%">Jenis Resiko</th>
                <th class="text-center" width="50%">Rating Resiko</th>
            </tr>

            <tr>
                <th style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; 1. Resiko Industri (Resiko Ekonomi)
                </th>
                <td style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; 4 &nbsp;&nbsp;&nbsp; ( Tinggi )
                </td>
            </tr>

            <tr>
                <th style="border-right:1px solid black;">
                    &nbsp; 2. Resiko Nasabah
                </th>
                <td style="border-right:1px solid black;"></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Karakter
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 3 &nbsp;&nbsp;&nbsp; ( Sedang )
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Pengalaman Usaha
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 3 &nbsp;&nbsp;&nbsp; ( Sedang )
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Nilai Agunan / Kredit
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 3 &nbsp;&nbsp;&nbsp; ( Sedang )
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Aspek Hukum Agunan
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 3 &nbsp;&nbsp;&nbsp; ( Sedang )
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Debt Service Coverage Ratio (DSR)
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 1 &nbsp;&nbsp;&nbsp; ( Sangat Rendah )
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Jangka Waktu
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; 1 &nbsp;&nbsp;&nbsp; ( Sedang )
                </td>
            </tr>

            <tr>
                <th style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; Credit Risk Rating (CRR)
                </th>
                <th style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; 3 &nbsp;&nbsp;&nbsp; ( Sedang )
                </th>
            </tr>
        </table>
        @if ($cetak->produk_kode != 'KTA')
            <h4 style="text-align: center;font-size: 12pt;">ANALISA KUALITATIF</h4>
            <table style="border:1px solid black;">
                <tr>
                    <th class="text-center" width="50%">Kategori</th>
                    <th class="text-center" width="50%">Keterangan</th>
                </tr>

                <tr>
                    <th style="border-top:1px solid black;border-right:1px solid black;">
                        &nbsp; 1. Karakter Debitur
                    </th>
                    <td style="border-top:1px solid black;border-right:1px solid black;"></td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. SLIK (Sumber Informasi SID Bank Indonesia)
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->bi_checking }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Kewajiban Kepada Pihal Lain
                    </td>
                    <td style="border-right:1px solid black;">{{ $kualitatif->bi_checking }}</td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 1
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->kewajiban1 }} - {{ $kualitatif->ket_kewajiban1 }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 2
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->kewajiban2 }} - {{ $kualitatif->ket_kewajiban3 }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 3
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->kewajiban3 }} - {{ $kualitatif->ket_kewajiban3 }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Judi / Urusan dengan Pihak Berwajib
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->pihak_berwajib }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Hubungan dengan Tetangga / Masyarakat
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->hubungan_tetangga }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Pengalaman Menjadi Tenaga Kerja Indonesia (TKI)
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->pengalaman_tki }} ({{ $kualitatif->ket_pengalaman }})
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Keberadaan Dirumah
                    </td>
                    <td style="border-right:1px solid black;"></td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Pemohon
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; Jam {{ $kualitatif->pemohon_ada }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Pendamping
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; Jam {{ $kualitatif->pendamping_ada }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Sumber Informasi Masyarakat
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->info_masyarakat }}
                    </td>
                </tr>

                <tr>
                    <th style="border-right:1px solid black;">
                        &nbsp; 2. Usaha Debitur Saat Ini
                    </th>
                    <td style="border-right:1px solid black;"></td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Sumber Bahan Baku
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->bahan_baku }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Proses Pengolahan
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->proses_olah }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Market Wilayah
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->target_market }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Sistem Pembayaran
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->pembayaran }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Faktor Pendukung Usaha
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->pendukung_usaha }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Faktor Pengurang Usaha
                    </td>
                    <td style="border-right:1px solid black;">
                        &nbsp; {{ $kualitatif->pengurang_usaha }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:1px solid black;vertical-align: text-top;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Trade Checking
                    </td>
                    <td style="border-right:1px solid black;">
                        <div width="94%;" style="text-align: justify;padding-left:7px;padding-right:7px;">
                            {{ $kualitatif->trade_checking }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif
        <h4 style="text-align: center;font-size: 12pt;">KEBUTUHAN DANA</h4>
        <table style="border:1px solid black;">
            <tr>
                <th class="text-center" width="50%">Kebutuhan</th>
                <th class="text-center" width="50%">Nominal</th>
            </tr>

            <tr>
                <td style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; 1. Modal Kerja
                </td>
                <td style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; {{ 'Rp.' . ' ' . number_format($kebutuhan_dana->modal_kerja, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 2. Investasi
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; {{ 'Rp.' . ' ' . number_format($kebutuhan_dana->investasi, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 3. Konsumtif
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; {{ 'Rp.' . ' ' . number_format($kebutuhan_dana->konsumtif, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 4. Pelunasan Kredit
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; {{ 'Rp.' . ' ' . number_format($kebutuhan_dana->pelunasan_kredit, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 5. Take Over
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; {{ 'Rp.' . ' ' . number_format($kebutuhan_dana->take_over, 0, ',', '.') }}
                </td>
            </tr>
        </table>

    </div>

    {{-- Memorandum --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;font-size:12.5px;">
        <h4 style="text-align: center;font-size: 12pt;margin-top:-1px;">MEMORANDUM USULAN KREDIT (MUK)</h4>
        <table style="border:1px solid black;">
            <tr>
                <th width="44%">&nbsp; 1. Identitas Pemohon dan Usulan</th>
                <td width="1%"></td>
                <td width="55%"></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Nama Lengkap Pemohon</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->nama_nasabah }}</td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Alamat</td>
                <td style="vertical-align: text-top;"> : </td>
                <td>
                    <div width="94%;" style="text-align: justify;padding-left:7px;padding-right:7px;">
                        {{ $memorandum->alamat_ktp }}
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. No. Telepon</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->no_telp }} / {{ $memorandum->no_telp_darurat }}</td>
            </tr>
            {{-- <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Bentuk Usaha</td>
                <td> : </td>
                <td>&nbsp; PT</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Pengalaman Usaha</td>
                <td> : </td>
                <td>&nbsp; Bukan Lapangan Usaha - Lainnya</td>
            </tr> --}}
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Susunan Pengurus & Pemegang Saham</td>
                <td> : </td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Riwayat Usaha dan Grup</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->pengalaman_usaha }}, {{ $memorandum->pertumbuhan_usaha }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Riwayat Pinjaman di Bank Lain</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->catatan_kredit }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Legalitas dan Izin Usaha</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - KTP
                </td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; {{ $memorandum->no_identitas }}</div>
                    <div style="width:59%;float:right">- SIUP &nbsp;&nbsp;&nbsp;&nbsp; : -</div>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Akta Pendirian
                </td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; -</div>
                    <div style="width:59%;float:right">- TDP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : -</div>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - NPWP
                </td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; {{ $memorandum->no_npwp }}</div>
                    <div style="width:59%;float:right">- TDPP &nbsp;&nbsp;&nbsp; : -</div>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - SITU
                </td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; -</div>
                    <div style="width:59%;float:right">- Lainnya : -</div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; h. Permohonan Kredit</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Besar Permohonan
                </td>
                <td> : </td>
                <td>&nbsp; {{ 'Rp. ' . ' ' . number_format($memorandum->temp_plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Obyek yang Dibiayai
                </td>
                <td> : </td>
                <td>&nbsp; Kredit Konsumsi Lainnya ( Konsumtif )</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; i. Lokasi Usaha dan atau Lokasi Kantor</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->lokasi_shm }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; j. Sumber Dana Pelunasan</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->sumber_dana_pelunasan }}</td>
            </tr>

            <tr>
                <th>
                    &nbsp; 2. Analisa dan Evaluasi Kredit
                </th>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Analisa Karakter</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; {{ $memorandum->nilai_karakter }}</div>
                    <div style="width:59%;float:right">d. Analisa Agunan : {{ $memorandum->nilai_collateral }}</div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Analisa Kemampuan</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; {{ $memorandum->evaluasi_capacity }}</div>
                    <div style="width:59%;float:right">e. Analisa Kondisi : {{ $memorandum->nilai_condition }}</div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Analisa Modal</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->capital_evaluasi_capital }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Analisa SWOT</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kekuatan
                </td>
                <td> : </td>
                <td>&nbsp; {{ $swot->kekuatan }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kelemahan
                </td>
                <td> : </td>
                <td>&nbsp; {{ $swot->kelemahan }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Peluang
                </td>
                <td> : </td>
                <td>&nbsp; {{ $swot->peluang }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Ancaman
                </td>
                <td> : </td>
                <td>&nbsp; {{ $swot->ancaman }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. CRR</td>
                <td> : </td>
                <td>&nbsp; 3 (Sedang )</td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kesimpulan</th>
                <td> : </td>
                <th>&nbsp; Dengan demikian usaha debitur LAYAK untuk dibiayai</th>
            </tr>

            <tr>
                <th>
                    &nbsp; 3. Rekomendasi
                </th>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. Nama Peminjam</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->nama_nasabah }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Plafon Maksimum yang Bisa Dibiayai</td>
                <td> : </td>
                <td>&nbsp; {{ 'Rp. ' . ' ' . number_format($memorandum->max_plafond, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Usulan Plafon Kredit</td>
                <td> : </td>
                <th>&nbsp; {{ 'Rp. ' . ' ' . number_format($memorandum->plafon_usulan, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Jenis Kredit</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->produk_kode }} - {{ $memorandum->nama_produk }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Tujuan Penggunaan</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->penggunaan }} - {{ $memorandum->keterangan }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Sektor Ekonomi</td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->bi_sek_ekonomi_kode }}. {{ $memorandum->ket_sektor_ekonomi }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Jangka Waktu</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>{{ $memorandum->jk }} Bulan</b></div>
                    <div style="width:59%;float:right">h. Suku Bunga &nbsp; : <b>{{ $memorandum->suku_bunga }}%</b>
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; i. Sistem Angsuran</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>{{ $memorandum->metode_rps }}</b></div>
                    <div style="width:59%;float:right">j. Biaya Provisi : <b>{{ $memorandum->b_provisi }}%</b></div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; k. Biaya Penalti</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>{{ $memorandum->biaya_denda }}% per hari</b></div>
                    <div style="width:59%;float:right">l. Biaya Admin : <b>{{ $memorandum->b_admin }}%</b></div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; m. Pengikatan Agunan</td>
                <td> : </td>
                <th>&nbsp;
                    @if ($memorandum->pengikatan == '1')
                        Tanpa Pengikatan
                    @elseif ($memorandum->pengikatan == '2')
                        APHT
                    @elseif ($memorandum->pengikatan == '3')
                        Fiducia
                    @else
                        AHPT dan Fiducia
                    @endif
                </th>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; n. Kepesertaan Asuransi</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kendaraan (TLO)
                </td>
                <td> : </td>
                <td>&nbsp; @if ($adm->asuransi_kendaraan_motor > 0)
                        Ya
                    @else
                        Tidak
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Jiwa (Wkawaktu)
                </td>
                <td> : </td>
                <td>&nbsp; @if ($adm->asuransi_jiwa_menurun2 > 0)
                        Ya
                    @else
                        Tidak
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kecelakaan Plus
                </td>
                <td> : </td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; o. Syarat-Syarat Kredit</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Sebelum Realisasi
                </td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->sebelum_realisasi }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Syarat Tambahan
                </td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->syarat_tambahan }}</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Syarat Lainnya
                </td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->syarat_lainnya }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="70%"></td>
                <td class="text-center">
                    <p style="margin-top: -10.5px;"></p>
                    Pamanukan, {{ $memorandum->hari }} <br>
                    <p style="margin-top: 30px;"></p>
                    <center>
                        {{-- <p style="margin-top:10px;"></p> --}}
                        <img src="{{ asset('storage/image/qr_code/' . $qr) }}" width="100" height="100"
                            style="margin-top:-30px;">
                    </center>
                    <b>
                        <font style="text-transform: uppercase;">{{ $memorandum->nama_surveyor }}</font>
                    </b>
                </td>
            </tr>

            {{-- <tr>
                <td width="70%"></td>
                <td class="text-center">
                    <p style="margin-top: -10.5px;"></p>
                    Pamanukan, {{ $memorandum->hari }} <br>
                    <center>
                        <img src="https://firebase.google.com/static/docs/ml-kit/images/examples/qrcode.png?hl=id"
                            style="width:100px;hight:100px;">
                    </center>
                    <b>
                        <font style="text-transform: uppercase;">{{ $memorandum->nama_surveyor }}</font>
                    </b>
                </td>
            </tr> --}}
        </table>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
