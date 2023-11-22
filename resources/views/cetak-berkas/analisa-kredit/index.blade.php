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
                    <img src="{{ asset('storage/image/photo/' . $cetak->photo_nasabah) }}"
                        style="width:150px;hight:225px;border: 1px solid black;">
                </td>
                <td class="text-center" width="27%">
                    <img src="{{ asset('storage/image/photo/' . $cetak->photo) }}"
                        style="width:150px;hight:225px;border: 1px solid black;">
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Photo Pemohon</b> <br>
                    CICIH CAHYATI
                </td>
                <td class="text-center">
                    <b>Photo Pendamping</b> <br>
                    SURYANA
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
                <td style="text-align: justify;">{{ $cetak->kode_pengajuan }}</td>
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
                <td style="text-align: justify;">{{ $cetak->plafon }}</td>
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
                <td style="text-align: justify;">{{ $cetak->jangka_waktu }}</td>
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
                <th class="text-center" width="25%" style="border:1px solid black;">TAHAPAN</th>
                <th class="text-center" width="21%" style="border:1px solid black;">PELAKSANAAN</th>
                <th class="text-center" width="25%" style="border:1px solid black;">PETUGAS</th>
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
                <td class="text-center" style="border:1px solid black;"></td>
                <td class="text-center" style="border:1px solid black;"></td>
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
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERDAGANGAN</h4>

        <table>
            <tr>
                <td width="13%">Jenis Usaha</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">PERDAGANGAN</td>
            </tr>
            <tr>
                <td>Kode Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">AUPG00002</td>
            </tr>
            <tr>
                <td>Nama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Warung Sembako</td>
            </tr>
            <tr>
                <td>Lama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">3 Tahun</td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">Alamat Usaha</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">DUSUN PABUARAN RT/RW 03/01 DESA. SINDANGSARI KECAMATAN. CIKAUM -
                    SUBANG</td>
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

            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">1.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">9.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">10.</td>
                <td style="border:1px solid black;">&nbsp; Sampurna Mild</td>
                <td style="border:1px solid black;text-align:right;">Rp. 20.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 25.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000.000 &nbsp;</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td class="text-center" style="border:1px solid black;">50.00%</td>
            </tr>

            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;">#</th>
                <th class="text-center" style="border:1px solid black;">Semua Barang</th>
                <th style="border:1px solid black;text-align:right;">Rp. 200.000.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 250.000.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 50.000 &nbsp;</th>
                <th class="text-center" style="border:1px solid black;">100</th>
                <th class="text-center" style="border:1px solid black;">50.00%</th>
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
                <td style="border:1px solid black;text-align:right;">Rp. 5.505.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Transportasi</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Bongkar Muat</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pegawai</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Gas Telepon Listrik</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Retribusi</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Sewa Tempat</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">#</td>
                <th style="border:1px solid black;">&nbsp; Total</th>
                <th style="border:1px solid black;text-align:right;">Rp. 5.605.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                <th class="text-center" colspan="2" style="border:1px solid black;">Rp. 5.485.000</th>
            </tr>
        </table>

        <p></p>

        <table>
            <tr>
                <td width="20%">Periode Belanja</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 200.000 | Harian</td>
            </tr>
            <tr>
                <td>Omset Harian</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 300.000</td>
            </tr>
            <tr>
                <td>Harga Pokok Penjualan</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 200.000 | Omset Harian / (1 + rata-rata % laba)</td>
            </tr>
            <tr>
                <td>Laba Penjualan Harian</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 100.000 | Omset Harian - Harga Pokok Penjualan</td>
            </tr>
        </table>
    </div>

    {{-- Analisa Usaha Pertanian --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA PERTANIAN</h4>

        <table>
            <tr>
                <td width="14%">Jenis Usaha</td>
                <td class="text-center" width="3%"> : </td>
                <td width="53%" style="text-align: justify;">PERTANIAN</td>

                <td width="16%">Luas Milik Sendiri</td>
                <td style="text-align: right;" width="3%"> : </td>
                <td style="text-align: right;"> 18.000 M2 </td>
            </tr>
            <tr>
                <td>Kode Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">AUP00001</td>

                <td>Luas Hasil Gadai</td>
                <td style="text-align: right;" width="3%"> : </td>
                <td style="text-align: right;"> 18.000 M2 </td>
            </tr>
            <tr>
                <td>Nama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Padi Ketan</td>

                <td>Luas Hasil Sewa</td>
                <td style="text-align: right;" width="3%"> : </td>
                <td style="text-align: right;"> 18.000 M2 </td>
            </tr>
            <tr>
                <td>Sektor Ekonomi</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Pertanian</td>

                <td>Hasil Panen</td>
                <td style="text-align: right;" width="3%"> : </td>
                <td style="text-align: right;"> 120 KW </td>
            </tr>
            <tr>
                <td>Jenis Tanaman</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Padi Ketan</td>

                <td>Harga Per Kwintal</td>
                <td style="text-align: right;" width="3%"> : </td>
                <td style="text-align: right;"> 450.000 </td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">Alamat Usaha</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;" colspan="4">DUSUN PABUARAN RT/RW 03/01 DESA. SINDANGSARI
                    KECAMATAN. CIKAUM - SUBANG</td>
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
                <td style="border:1px solid black;text-align:right;">Rp. 5.505.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pengolahan Tanah</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Bibit</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pupuk</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pestisida</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pengairan</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Panen</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Penggarap</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">9.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Tenaga Kerja</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">10.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pajak</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">11.</td>
                <td style="border:1px solid black;">&nbsp; Iuran Desa</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">12.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Amortisasi</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">13.</td>
                <td style="border:1px solid black;">&nbsp; Pinjaman Bank Lain</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">14.</td>
                <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">#</td>
                <th style="border:1px solid black;">&nbsp; Total</th>
                <th style="border:1px solid black;text-align:right;">Rp. 5.605.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                <th class="text-center" colspan="2" style="border:1px solid black;">Rp. 5.485.000</th>
            </tr>
        </table>

        <p></p>

        <table>
            <tr>
                <td width="23%">Hasil Bersih Usaha (70%)</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 104.930.000</td>
            </tr>
            <tr>
                <td>Saving Pokok</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 30.000.000</td>
            </tr>
            <tr>
                <td>Pendapatan Perbulan</td>
                <td class="text-center" width="3%">:</td>
                <td>Rp. 2.500.000 | Hasil Bersih Usaha (70%) - Saving Pokok / 6 Bulan</td>
            </tr>
        </table>
    </div>

    {{-- Analisa Usaha Jasa --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA JASA</h4>

        <table>
            <tr>
                <td width="13%">Jenis Usaha</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">JASA</td>
            </tr>
            <tr>
                <td>Kode Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">AUJ00003</td>
            </tr>
            <tr>
                <td>Nama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Karyawan</td>
            </tr>
            <tr>
                <td>Lama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">1 Tahun</td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">Alamat Usaha</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">DUSUN PABUARAN RT/RW 03/01 DESA. SINDANGSARI KECAMATAN. CIKAUM -
                    SUBANG</td>
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
                <td style="border:1px solid black;text-align:right;">Rp. 5.505.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Pajak Kendaraan (Produktif)</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Pengeluaran lain-lain</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">#</td>
                <th style="border:1px solid black;">&nbsp; Total</th>
                <th style="border:1px solid black;text-align:right;">Rp. 5.505.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                <th class="text-center" colspan="2" style="border:1px solid black;">Rp. 5.385.000</th>
            </tr>
        </table>
    </div>

    {{-- Analisa Usaha Lainnya --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA USAHA LAINNYA</h4>

        <table>
            <tr>
                <td width="14%">Jenis Usaha</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">LAINNYA</td>
            </tr>
            <tr>
                <td>Kode Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">AUL00001</td>
            </tr>
            <tr>
                <td>Ketegori Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">HOME INDUSTRI</td>
            </tr>
            <tr>
                <td>Nama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">Joki Motor</td>
            </tr>
            <tr>
                <td>Lama Usaha</td>
                <td class="text-center"> : </td>
                <td style="text-align: justify;">3 Tahun</td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">Alamat Usaha</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">DUSUN PABUARAN RT/RW 03/01 DESA. SINDANGSARI KECAMATAN. CIKAUM -
                    SUBANG</td>
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

            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">1.</td>
                <td style="border:1px solid black;">&nbsp; Boled</td>
                <td class="text-center" style="border:1px solid black;">10</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 50.000 &nbsp;</td>
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
                <td style="border:1px solid black;">&nbsp; Nama Pendapatan 1</td>
                <td style="border:1px solid black;text-align:right;">Rp. 5.505.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp; </td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pendapatan 2</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pendapatan 3</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pendapatan 4</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pengeluaran 1</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pengeluaran 2</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pengeluaran 3</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Nama Pengeluaran 4</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">9.</td>
                <td style="border:1px solid black;">&nbsp; Proyeksi Penambahan</td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">#</td>
                <th style="border:1px solid black;">&nbsp; Total</th>
                <th style="border:1px solid black;text-align:right;">Rp. 5.605.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 120.000 &nbsp;</th>
            </tr>
            <tr style="border:1px solid black;">
                <th class="text-center" colspan="2" style="border:1px solid black;">Hasil Bersih Usaha</th>
                <th class="text-center" colspan="2" style="border:1px solid black;">Rp. 5.685.000</th>
            </tr>
        </table>
    </div>

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
                <td style="border:1px solid black;text-align:right;">Rp. 1.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Pertanian</td>
                <td style="border:1px solid black;text-align:right;">Rp. 1.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Jasa</td>
                <td style="border:1px solid black;text-align:right;">Rp. 1.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Hasil Usaha Lainnya</td>
                <td style="border:1px solid black;text-align:right;">Rp. 1.000.000 &nbsp;</td>
                <td style="border:1px solid black;text-align:right;"></td>
            </tr>

            <tr style="border:1px solid black;">
                <td style="border:1px solid black;" colspan="4">&nbsp; Biaya Rumah Tangga</td>
            </tr>

            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Konsumsi Pokok</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Kesehatan</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Pendidikan</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Gas, Air, Telepon, Listrik</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">9.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Jajan Anak</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">10.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Sumbangan Sosial</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">11.</td>
                <td style="border:1px solid black;">&nbsp; Biaya Rokok</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>

            <tr style="border:1px solid black;">
                <td style="border:1px solid black;" colspan="4">&nbsp; Kewajiban Lainnya</td>
            </tr>

            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">12.</td>
                <td style="border:1px solid black;">&nbsp; Nama Kewajiban</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;">Rp. 100.000 &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">13.</td>
                <td style="border:1px solid black;">&nbsp; Kewajiban Lainnya</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">14.</td>
                <td style="border:1px solid black;">&nbsp; Kewajiban Lainnya</td>
                <td style="border:1px solid black;text-align:right;"></td>
                <td style="border:1px solid black;text-align:right;"> &nbsp;</td>
            </tr>

            <tr style="border:1px solid black;">
                <td class="text-center" style="border:1px solid black;">#</td>
                <th style="border:1px solid black;">&nbsp; Total</th>
                <th style="border:1px solid black;text-align:right;">Rp. 4.000.000 &nbsp;</th>
                <th style="border:1px solid black;text-align:right;">Rp. 800.000 &nbsp;</th>
            </tr>

            <tr style="border:1px solid black;">
                <th class="text-center" colspan="2" style="border:1px solid black;">Kemampuan Keuangan Perbulan
                </th>
                <th class="text-center" colspan="2" style="border:1px solid black;">Rp. 3.200.000</th>
            </tr>
        </table>

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
                <td class="text-center" style="border:1px solid black;">Permanen</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">2.</td>
                <td style="border:1px solid black;">&nbsp; Mobil</td>
                <td class="text-center" style="border:1px solid black;">Tidak Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">3.</td>
                <td style="border:1px solid black;">&nbsp; Motor</td>
                <td class="text-center" style="border:1px solid black;">3 Unit</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">4.</td>
                <td style="border:1px solid black;">&nbsp; Komputer</td>
                <td class="text-center" style="border:1px solid black;">Tidak Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">5.</td>
                <td style="border:1px solid black;">&nbsp; Mesin Cuci</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">6.</td>
                <td style="border:1px solid black;">&nbsp; Kursi Tamu</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">7.</td>
                <td style="border:1px solid black;">&nbsp; Lemari Panjang</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">8.</td>
                <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">9.</td>
                <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">10.</td>
                <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">11.</td>
                <td style="border:1px solid black;">&nbsp; Harta Lainnya</td>
                <td class="text-center" style="border:1px solid black;">Ada</td>
            </tr>
        </table>

        <h4 style="text-align: center;font-size: 12pt;">TAKSASI JAMINAN</h4>
        <table style="border:1px solid black;">
            <tr style="border:1px solid black;">
                <th class="text-center" width="4%" style="border:1px solid black;">No</th>
                <th class="text-center" style="border:1px solid black;">Agunan</th>
                <th class="text-center" width="17%" style="border:1px solid black;">Nilai Taksasi</th>
            </tr>

            <tr style="border:1px solid black;">
                <td class="text-center" width="4%" style="border:1px solid black;">1.</td>
                <td style="border:1px solid black;">
                    KARTU DAN SALDO JAMSOSTEK ATAS NAMA CICIH CAHYATI NO 06K90147831
                </td>
                <td style="border:1px solid black;text-align:right;">Rp. 29.000.000</td>
            </tr>
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
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Terbuka dan Konsisten</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Gaya Hidup</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Kepetuhan Terhadap
                    Kewajiban</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Keharmonisan Rumah Tangga
                </td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Hubungan Sosial dengan
                    Lingkungan</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Pengendalian Emosi</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
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
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Pertumbuhan Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3.
                    kontinuitas Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Aset / Kekayaan</td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Aset Terkait Usaha (Likuid)</td>
                <td style="border-right:1px solid black;">&nbsp; Mengcover</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Aset Diluar Usaha</td>
                <td style="border-right:1px solid black;">&nbsp; Likuid</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Repacyment Capacity</td>
                <td style="border-right:1px solid black;">&nbsp; 15%</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Pendataan Laporan Keuangan
                </td>
                <td style="border-right:1px solid black;">&nbsp; Mencatat Transaksi Harian</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Pengalaman Kredit Masa
                    Lalu</td>
                <td style="border-right:1px solid black;">&nbsp; Lancar Tidak Ada Tunggakan</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. SLIK</td>
                <td style="border-right:1px solid black;">&nbsp; Lancar</td>
                <td>&nbsp; Baik</td>
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
                <td style="border-right:1px solid black;">&nbsp; Modal Sendiri</td>
                <td>&nbsp; Baik</td>
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
                <td style="border-right:1px solid black;">&nbsp; Milik Sendiri</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Agunan Tambahan</td>
                <td style="border-right:1px solid black;">&nbsp; -</td>
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
                <td style="border-right:1px solid black;">&nbsp; -</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    Agunan Tambahan</td>
                <td style="border-right:1px solid black;">&nbsp; -</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Mudah Diuangkan</td>
                <td style="border-right:1px solid black;">&nbsp; Baik</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Stabilitas Harga</td>
                <td style="border-right:1px solid black;">&nbsp; Lainnya</td>
                <td>&nbsp; Kurang Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Lokasi atau Kondisi Fisik
                    Agunan</td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.
                    Kendaraan</td>
                <td style="border-right:1px solid black;">&nbsp; -</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.
                    SHM</td>
                <td style="border-right:1px solid black;">&nbsp; -</td>
                <td>&nbsp; -</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Permohonan Thd Taksasi
                    Agunan</td>
                <td style="border-right:1px solid black;">&nbsp; 169%</td>
                <td>&nbsp; Tidak baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Aspek Hukum</td>
                <td style="border-right:1px solid black;">&nbsp; Agunan lain yang tidak memenuhi syarat</td>
                <td>&nbsp; Tidak Baik</td>
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
                <td style="border-right:1px solid black;">&nbsp; Persaingan Usaha Tidak Ketat</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Kondisi Alam</td>
                <td style="border-right:1px solid black;">&nbsp; Resiko Sangat Rendah</td>
                <td>&nbsp; Baik</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Regulisai Pemerintah</td>
                <td style="border-right:1px solid black;">&nbsp; Tidak Mendukung</td>
                <td>&nbsp; Baik</td>
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
                    &nbsp; Lancar
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Kewajiban Kepada Pihal Lain
                </td>
                <td style="border-right:1px solid black;"></td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 1
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; -
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 2
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; -
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kewajiban 3
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; -
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Judi / Urusan dengan Pihak Berwajib
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Tidak Pernah
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Hubungan dengan Tetangga / Masyarakat
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Baik
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Pengalaman Menjadi Tenaga Kerja Indonesia (TKI)
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Tidak Pernah (Pemohon)
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
                    &nbsp; Jam 17:00
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Pendamping
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Jam 17:00
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Sumber Informasi Masyarakat
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Info Pak Bambang
                </td>
            </tr>

            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a. SLIK (Sumber Informasi SID Bank Indonesia)
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Lancar
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
                    &nbsp; Karyawan Tetap
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Proses Pengolahan
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Aktifitas dilakukan setiap hari
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Market Wilayah
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Subang
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Sistem Pembayaran
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Transfer
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Faktor Pendukung Usaha
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Naik Jabatan
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Faktor Pengurang Usaha
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Kesehatan Menurun
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;vertical-align: text-top;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Trade Checking
                </td>
                <td style="border-right:1px solid black;">
                    <div width="94%;" style="text-align: justify;padding-left:7px;padding-right:7px;">
                        INFO PAK BAMBANG BENAR BAHWA YBS MASIH AKTIF BEKERJA SAMPAI SAAT INI DAN TIDAK MENAJUKAN PHK
                    </div>
                </td>
            </tr>
        </table>

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
                    &nbsp; Rp. 0
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 2. Investasi
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Rp. 0
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 3. Konsumtif
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Rp. 10.000.000
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 4. Pelunasan Kredit
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Rp. 0
                </td>
            </tr>
            <tr>
                <td style="border-right:1px solid black;">
                    &nbsp; 5. Take Over
                </td>
                <td style="border-right:1px solid black;">
                    &nbsp; Rp. 0
                </td>
            </tr>
        </table>

    </div>

    {{-- Memorandum --}}
    <div class="page-break"></div>
    <div class="content" style="margin-top: -57px;">
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
                <td>&nbsp; Zulfadli Rizal</td>
            </tr>
            <tr>
                <td style="vertical-align: text-top;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Alamat</td>
                <td style="vertical-align: text-top;"> : </td>
                <td>
                    <div width="94%;" style="text-align: justify;padding-left:7px;padding-right:7px;">
                        DUSUN PABUARAN RT/RW 03/01 DESA. SINDANGSARI KECAMATAN. CIKAUM - SUBANG
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. No. Telepon</td>
                <td> : </td>
                <td>&nbsp; 082320099971 / 0823200999777</td>
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
                <td>&nbsp; >5 Tahun, Meningkat</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Riwayat Pinjaman di Bank Lain</td>
                <td> : </td>
                <td>&nbsp; Lancar</td>
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
                    <div style="width:40%;float:left">&nbsp; 3213070701980004</div>
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
                    <div style="width:40%;float:left">&nbsp; -</div>
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
                <td>&nbsp; Rp. 50.000.000</td>
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
                <td>&nbsp; Strategis</td>
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
                    <div style="width:40%;float:left">&nbsp; Cukup Baik</div>
                    <div style="width:59%;float:right">d. Analisa Agunan : Cukup Baik</div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Analisa Kemampuan</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; Cukup Baik</div>
                    <div style="width:59%;float:right">e. Analisa Kondisi : Cukup Baik</div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Analisa Modal</td>
                <td> : </td>
                <td>&nbsp; Cukup Baik</td>
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
                <td>&nbsp; Karyawan Tetap</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kelemahan
                </td>
                <td> : </td>
                <td>&nbsp; Kesehatan Menurun</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Peluang
                </td>
                <td> : </td>
                <td>&nbsp; Naik Jabatan</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Ancaman
                </td>
                <td> : </td>
                <td>&nbsp; PHK</td>
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
                <td>&nbsp; Zulfadli Rizal</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b. Plafon Maksimum yang Bisa Dibiayai</td>
                <td> : </td>
                <td>&nbsp; Rp. 138.726.000</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c. Usulan Plafon Kredit</td>
                <td> : </td>
                <th>&nbsp; Rp. 50.000.000</th>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d. Jenis Kredit</td>
                <td> : </td>
                <td>&nbsp; KRU - Kedit Umum</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; e. Tujuan Penggunaan</td>
                <td> : </td>
                <td>&nbsp; Konsumtif - Modal Nikah</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; f. Sektor Ekonomi</td>
                <td> : </td>
                <td>&nbsp; 1020. Bukan Lapangan Usaha - Lainnya</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; g. Jangka Waktu</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>36 Bulan</b></div>
                    <div style="width:59%;float:right">h. Suku Bunga &nbsp; : <b>32%</b></div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; i. Sistem Angsuran</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>EFEKTIF ANUITAS</b></div>
                    <div style="width:59%;float:right">j. Biaya Provisi : <b>1.00%</b></div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; k. Biaya Penalti</td>
                <td> : </td>
                <td>
                    <div style="width:40%;float:left">&nbsp; <b>0.10% per hari</b></div>
                    <div style="width:59%;float:right">l. Biaya Admin : <b>4.00%</b></div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; m. Pengikatan Agunan</td>
                <td> : </td>
                <th>&nbsp; Tanpa Pengikatan</th>
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
                <td>&nbsp; Tidak</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Jiwa (Wkawaktu)
                </td>
                <td> : </td>
                <td>&nbsp; Ya</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Kecelakaan Plus
                </td>
                <td> : </td>
                <td>&nbsp; Tidak</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; p. Syarat-Syarat Kredit</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Sebelum Realisasi
                </td>
                <td> : </td>
                <td>&nbsp; ATM, BUTAB, KARTU JAMSOSTEK, SK KARYAWAN</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Syarat Tambahan
                </td>
                <td> : </td>
                <td>&nbsp; IJAZAH TERAKHIR SMP, KK ASLI</td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Syarat Lainnya
                </td>
                <td> : </td>
                <td>&nbsp; USER M-BANKING</td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="70%"></td>
                <td class="text-center">
                    Pamanukan, 22 November 2023 <br>
                    Surveyor, <br>
                    <p style="margin-top:55px;"></p>


                    <b>DIDI JUNAEDI</b>
                </td>
            </tr>
        </table>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
