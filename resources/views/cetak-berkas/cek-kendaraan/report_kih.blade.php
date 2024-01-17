<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report KIH</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
            margin-left: -1.2cm;
            margin-right: 0cm;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 106%;
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
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <h4 style="text-align: center; font-size: 12pt;">PT BPR BANGUNARTA <br>
            CHECK LIST KELENGKAPAN DOKUMEN SURAT PERNJANJIAN KREDIT</h4>
        <table style="border:1px solid black;">
            <thead>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="5%" rowspan="2" style="border:1px solid black;">No</th>
                    <th class="text-center" width="48%" rowspan="2" style="border:1px solid black;">Daftar Dokumen
                        Persyaratan Kredit
                    </th>
                    <th class="text-center" colspan="5" width="30%" style="border:1px solid black;">QC Petugas
                    </th>
                    <th class="text-center" rowspan="2" width="17%" style="border:1px solid black;">Catatan &
                        Kelengkapan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="5%" style="border:1px solid black;">SAA</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">KAA</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">P.E</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">Real</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">Legal</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">1.</td>
                    <td style="border:1px solid black;">&nbsp; Perjanjian Kredit</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">2.</td>
                    <td style="border:1px solid black;">&nbsp; Bukti Droping Kredit</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">3.</td>
                    <td style="border:1px solid black;">&nbsp; Pas Foto Pemohon dan Pendamping</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">4.</td>
                    <td style="border:1px solid black;">&nbsp; Fotocopy e-KTP Pemohon dan Pendamping</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">5.</td>
                    <td style="border:1px solid black;">&nbsp; Foto Proses Realisasi Pemohon & Pendamping</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">6.</td>
                    <td style="border:1px solid black;">&nbsp; Formulir Pendaftaran Kredit</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">7.</td>
                    <td style="border:1px solid black;">&nbsp; History Kredit yang Lalu / Kartu Angsuran (Debitur
                        Ulangan)
                    </td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">8.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Keterangan Domisili</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">9.</td>
                    <td style="border:1px solid black;">&nbsp; Fotocopy Kartu Keluarga / Surat Nikah / Surat Cerai</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">10.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Keterangan Beda Tanggal Lahir / Nama</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">11.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Pernyataan Asuransi Jiwa</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">12.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Pernyataan Lainnya</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">13.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Pernyataan Pengecekan NIK</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">14.</td>
                    <td style="border:1px solid black;">&nbsp; Formulir Permohonan Informasi Debitur</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">15.</td>
                    <td style="border:1px solid black;">&nbsp; SLIK / Informasi Debitur</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">16.</td>
                    <td style="border:1px solid black;">&nbsp; Rekap SLIK</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">17.</td>
                    <td style="border:1px solid black;">&nbsp; Surat Izin Usaha / Nota / Bukti Garap / Slip Gaji</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">18.</td>
                    <td style="border:1px solid black;">&nbsp; Form Analisa Kredit Usaha</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">19.</td>
                    <td style="border:1px solid black;">&nbsp; Form Perhitungan Kemampuan Keuangan</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">20.</td>
                    <td style="border:1px solid black;">&nbsp; Form Analisa 5C</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">21.</td>
                    <td style="border:1px solid black;">&nbsp; Form Penilaian Credit Risk Rating ( CRR )</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">22.</td>
                    <td style="border:1px solid black;">&nbsp; Form Analisa Kualitatif</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">23.</td>
                    <td style="border:1px solid black;">&nbsp; Simulasi Perhitungan Asuransi Jiwa</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">24.</td>
                    <td style="border:1px solid black;">&nbsp; Form Rekomendasi Kebitullah</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">25.</td>
                    <td style="border:1px solid black;">&nbsp; Form Lembar Persetujuan dan Kontrol Kredit</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">26.</td>
                    <td style="border:1px solid black;">&nbsp; Notifikasi Kredit</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">27.</td>
                    <td style="border:1px solid black;">&nbsp; Checklist Kelengkapan Dokumen</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td class="text-center" style="border:1px solid black;">28.</td>
                    <td style="border:1px solid black;">&nbsp; Denah Lokasi</td>
                    <td style="border:1px solid black;text-align:right;">
                        &nbsp;
                    </td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                    <td style="border:1px solid black;text-align:right;"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="height: 40px;">
                    <th class="text-center" colspan="2" style="border:1px solid black;"><b>Paraf Petugas QC</b>
                    </th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
