<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorandum Usulan Kredit</title>
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
            background-color: #ffffff;
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

        .taksasi {
            border: 1px solid black;
            padding-left: 15px;
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: 11px;
        }

        .sub td {
            position: relative;
            margin-right: 120px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <p style="font-weight: bold; text-align: center;"><u>MEMORANDUM USULAN KREDIT (MUK)</u></p>
        <div>
            <div class="taksasi">
                <table>
                    <tr>
                        <td style="width: 18px;"><b>I.</b></td>
                        <td><b>Identitas Pemohon dan Usulan</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">1. Nama Lengkap Pemohon</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Sudarwani wibowo Binti Agus</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">2. Alamat</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">3 (Sedang)</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;a. Rumah tinggal (individu)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">KP Peutag RT 24 RW 09 Desa Pringkasap Kec. Pabuaran Kab. Subang</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;b. Kantor (individu)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;c. Usaha</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">KP Peutag RT 24 RW 09 Desa Pringkasap Kec. Pabuaran Kab. Subang</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;d. No Telepon</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">3. Bentuk Usaha</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Perorang</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">4. Pengalaman Usaha</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Buka lapangan usaha lainnya</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">5. Susunan Pengurus & Pemegang Saham</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Perorang</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">6. Riwayat Usaha dan Grup</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">> 5 Tahun, Meningkat</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">7. Riwayat Pinjaman di Bank Lain</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Lancar</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">8. Legalitas dan Ijin Usaha</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Lancar</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;a. KTP</td>
                        <td style="width: 2px;">:</td>
                        <td>3213051208950007</td>
                        <td style="width: 17%; margin-right: 20px;">&ensp;&ensp;e. SIUP</td>
                        <td style="width: 60%;">:</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;b. Akta Pendirian</td>
                        <td style="width: 2px;">:</td>
                        <td>3213051208950007</td>
                        <td style="width: 17%; margin-right: 20px;">&ensp;&ensp;f. TDP</td>
                        <td style="width: 60%;">:</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;c. NPWP</td>
                        <td style="width: 2px;">:</td>
                        <td>3213051208950007</td>
                        <td style="width: 17%; margin-right: 20px;">&ensp;&ensp;g. TDPP</td>
                        <td style="width: 60%;">:</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;d. SITU</td>
                        <td style="width: 2px;">:</td>
                        <td>3213051208950007</td>
                        <td style="width: 17%; margin-right: 20px;">&ensp;&ensp;h. Ijin lainnya</td>
                        <td style="width: 60%;">:</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">9. Pemohon Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;a. Besar Permohonan</td>
                        <td style="width: 2px;">:</td>
                        <td>Rp. 100.000.000</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;b. Obyek uang dibiayai</td>
                        <td style="width: 2px;">:</td>
                        <td>Konsumtif</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">10. Lokasi Usaha dan atau Lokasi Kantor</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="2">Strategis</td>
                    </tr>
                    <tr>
                        <td style="width: 18px;"><b>II.</b></td>
                        <td><b>Analisa dan Evaluasi Kredit</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">1. Analisa Watak (character)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">2. Analisa Kemampuan (capacity)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">3. Analisa Modal (capital)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">4. Analisa Agunan (collateral)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">5. Analisa Kondisi & Prospek Usaha (condition)</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr>
                    {{-- <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">6. Analisa SWOT</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Cuukup Baik</td>
                    </tr> --}}
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">6. CRR</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">4 (Tinggi)</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;"><b>Kesimpulan</b></td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Usaha Layak dibiayai</td>
                    </tr>
                    <tr>
                        <td style="width: 18px;"><b>III.</b></td>
                        <td><b>Rekomendasi</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">1. Nama Peminjam</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Sudarwani </td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">2. Plafon masksimum yang bisa dibiayai</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Rp. 200.000.000 </td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">3. Jumlah / USulan Plafon Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Rp. 200.000.000 </td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">4. Jenis Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">39. Konsumsi Lainnya</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">5. Tujuan Penggunaan Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Kredit Konsumsi Lainnya</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">6. Sektor Ekonomi</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">1020. Bukan Lapangan Usaha - Lainnya</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">7. Jangka Waktu Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">60 Bulan</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">8. Suku Bunga Kredit per tahun</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">15%</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">9. Sistem Angsuran</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">Flat</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">10. Provisi Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">1.00%</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">11. Biaya Administrasi</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">2.50%</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">12. Pinalty</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">0.10% per hari</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">13. Pengikatan Agunan</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3">APHT</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">14. Kepesertaan Asuransi :</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;a. Kendaraan (TLO)</td>
                        <td style="width: 2px;">:</td>
                        <td>Tidak</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;b. Jiwa (Ekawaktu)</td>
                        <td style="width: 2px;">:</td>
                        <td>Ya</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;c. Kecelakaan PLus</td>
                        <td style="width: 2px;">:</td>
                        <td>Tidak</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%;">15. Syarat-syarat Kredit</td>
                        <td style="width: 2px;">:</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;a. Sebelum Kredit direalisasi</td>
                        <td style="width: 2px;">:</td>
                        <td>KTP asli, BUKTAB, SPPT Terbaru Asli</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;b. Syarat-syarat Tambahan</td>
                        <td style="width: 2px;">:</td>
                        <td>POT Pelunasan, TTD Pendamping du Berkas</td>
                    </tr>
                    <tr>
                        <td style="width: 2px;"></td>
                        <td style="width: 40%; margin-right: 20px;">&ensp;&ensp;c. Syarat Tambahan Lainnya</td>
                        <td style="width: 2px;">:</td>
                        <td>FC SHM (Minta Ke Legal)</td>
                    </tr>
                </table>
            </div>
        </div>

        <p style="text-align:
                            center; position:relative; float: right;">Pamanukan,
            25 September 2023 <br>
            Surveyor <br>
            <br>
            <br><br>
            <b>Muhidin
                Herlambang</b>
        </p><span style="position:absolute; left: 55px; margin-top: 105px;">Cetak : 20 September 2023 09:20:25</span>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
