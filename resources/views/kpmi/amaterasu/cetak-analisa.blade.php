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

        .content img {
            display: none;
        }

        .content hr {
            display: none;
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

            .content img {
                display: block;
            }

            .content hr {
                display: block;
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
                <td style="text-align: justify;">{{ \Carbon\Carbon::parse($cetak->tgl_pengajuan)->format('Y-m-d') }}
                </td>
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
                    {{ \Carbon\Carbon::parse($cetak->tgl_pengajuan)->format('Y-m-d') }}
                </td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_cs }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 2. </td>
                <td style="border:1px solid black;">&nbsp; Terima Berkas</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->tgl_nasabah)->format('Y-m-d') }}
                </td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_input_nasabah }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 3. </td>
                <td style="border:1px solid black;">&nbsp; Proses Survey</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->tgl_survei)->format('Y-m-d') }}
                </td>
                <td class="text-center" style="border:1px solid black;">{{ $cetak->nama_input_survei }}</td>
                <td style="border:1px solid black;"></td>
            </tr>
            <tr style="border:1px solid black;">
                <td class="text-center" width="2%" style="border:1px solid black;"> 4. </td>
                <td style="border:1px solid black;">&nbsp; Proses Analisa</td>
                <td class="text-center" style="border:1px solid black;">
                    {{ \Carbon\Carbon::parse($cetak->analisa_kredit)->format('Y-m-d') }}
                </td>
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


    {{-- Analisa Kredit KPMI --}}
   <div class="page-break"></div>
   <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">ANALISA KREDIT KPMI</h4>
        @include('kpmi.amaterasu._cetak_analisa_kpmi', $kpmi)
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
        
        <h4 style="text-align: center;font-size: 12pt;">KEBUTUHAN DANA</h4>
        <table style="border:1px solid black;">
            <tr>
                <th class="text-center" width="25%">Kebutuhan</th>
                <th class="text-center" width="50%">Keterangan</th>
                <th class="text-center" width="25%">Nominal</th>
            </tr>

            <tr>
                <td style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; 1. Modal Kerja
                </td>
                <td style="border-top:1px solid black;border-right:1px solid black;">
                    &nbsp; {{ $kebutuhan_dana->ket_modal_kerja }}
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
                    &nbsp; {{ $kebutuhan_dana->ket_investasi }}
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
                    &nbsp; {{ $kebutuhan_dana->ket_konsumtif }}
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
                    &nbsp; {{ $kebutuhan_dana->ket_pelunasan_kredit }}
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
                    &nbsp; {{ $kebutuhan_dana->ket_take_over }}
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
                <td>&nbsp; @if (!empty($cetak->temp_plafon))
                    {{ 'Rp. ' . ' ' . number_format($cetak->temp_plafon, 0, ',', '.') }}
                    @else
                    {{ 'Rp. ' . ' ' . number_format($memorandum->plafon_usulan, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Obyek yang Dibiayai
                </td>
                <td> : </td>
                <td>&nbsp; {{ $memorandum->penggunaan_debitur }}</td>
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
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; k. Biaya Denda</td>
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
                    APHT dan Fiducia
                    @endif
                    <div style="width:59%; float:right; font-weight: normal;">n. Biaya Penalti :
                        <b>{{ $cetak->b_penalti }}%</b>
                    </div>
                </th>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; o. Kepesertaan Asuransi</td>
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
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Jiwa (Ekawaktu)
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
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; p. Syarat-Syarat Kredit</td>
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
                <td>
                    <div style="margin-left: 5px; margin-right:5px;">
                        {{ $memorandum->syarat_lainnya }}
                    </div>
                </td>
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
                        <img src="{{ asset('storage/image/qr_code/' . $qr) }}" width="100" height="100"
                            style="margin-top:-25px;">
                    </center>
                    <b>
                        <font style="text-transform: uppercase; margin-top: 5px; display: block;">
                            {{ $memorandum->nama_surveyor }}
                        </font>
                    </b>
                </td>
            </tr>

        </table>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>