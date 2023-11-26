<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Kredit</title>
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
        }
    </style>
</head>

<body>
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        {{-- <h4 style="text-align: center;font-size: 12pt;">NOTIFIKASI KREDIT</h4> --}}

        <table>
            <tr>
                <td width="14%">Nomor</td>
                <td class="text-center" width="1%"> : </td>
                <td>184/NK/PBA/XI/2023</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-center" width="1%"> : </td>
                <td>13 November 2023</td>
            </tr>
            <tr>
                <td>Kode Pengajuan</td>
                <td class="text-center" width="1%"> : </td>
                <td>00339950</td>
            </tr>
        </table>

        <p></p>

        Kepada Yth. <br>
        Bapak/Ibu AKSAN / LILIS MUMINAH <br>
        Di <br>
        KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG No. Telp/HP : 082320099971

        <p></p>

        <table>
            <tr>
                <td width="14%">Perihal</td>
                <td class="text-center" width="1%"> : </td>
                <td><b><u>Notifikasi Kredit</u></b></td>
            </tr>
        </table>

        <p></p>

        Menunjuk permohonan fasilitas kredit Konsumsi Lainnya atas nama Bapak/Ibu, dengan ini kami beritahukan bahwa PT BPR BANGUNARTA dapat menyetujui permohonan kredit Bapak/Ibu tersebut, dengan ketentuan-ketentuan dan syarat-syarat sebagai berikut :

        <p></p>

        <table>
            <tr>
                <td class="text-center" width="2%"> 1. </td>
                <td width="27%">Limit Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">Rp 52.000.000 ( lima puluh dua juta rupiah )</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 2. </td>
                <td width="27%">Jenis Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">Konsumtif Lainnya</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 3. </td>
                <td width="27%" style="vertical-align: text-top;">Tujuan Penggunaan Kredit</td>
                <td class="text-center" width="1%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    KONSUMTIF. Sepanjang tidak bertentangan dengan ketentuan hukum yang berlaku, norma kesusilaan dan ketertiban umum.
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 4. </td>
                <td width="27%">Sektor Ekonomi</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">1020. Bukan Lapangan Usaha - Lainnya</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 5. </td>
                <td width="27%">Jangka Waktu Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">36 ( Tiga Puluh Enam ) bulan terhitung sejak akad kredit.</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 6. </td>
                <td width="27%" style="vertical-align: text-top;">Pembayaran Kembali</td>
                <td class="text-center" width="1%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    Wajib dilakukan dalam 36 (Tiga Puluh Enam) kali angsuran, yang dibayarkan setiap bulan selambat-lambatnya pada tanggal yang sama dengan akad kredit untuk yang pertama kalinya, angsuran dibayarkan satu bulan setelah tanggal akad kredit.
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 7. </td>
                <td width="27%">Suku Bunga</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">13 % EFEKTIF ANUITAS / Tahun</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 8. </td>
                <td width="27%">Denda</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">0.10 % per hari dari angsuran pokok dan atau bunga yang tertunggak</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 9. </td>
                <td width="27%">Biaya Kredit</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">0.50 % dari plafon kredit</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">a. Provisi Sebesar</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">0.50 % Rp. 260.000</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">b. Administrasi Bank Sebesar</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">2.50 % Rp. 750.000</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">c. APHT</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">0.00 % Rp. 0</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 10. </td>
                <td width="27%">Jumlah Biaya Asuransi</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Ekawaktu / - / -</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">a. Kendaraan (TLO)</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">2. Jiwa (Ekawaktu)</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">c. Kecelakaan Plus</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 11. </td>
                <td style="text-align: justify;">
                    Debitur tidak diperbolehkan memberikan suatu imbalan dalam bentuk apapun kapada pejabat dan atau karyawan BPR BANGUNARTA berkanaan dengan persetujuan pemberian kredit, kecuali Biaya jasa survey yang dibayar oleh debitur pada saat survey yang disertakan bukti penerimaan uang jasa surveY. Apabila dikemudian hari diketahui bahwa Debitur melanggar larangan tersebut, maka kepada Debitur dan Pejabat atau petugas BPR BANGUNARTA dapat dikenakan sanksi sesuai ketentuan BPR BANGUNARTA.
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 12. </td>
                <td style="text-align: justify;">
                    Notifikasi Kredit ini merupakan bagian yang tidak dapat terpisahkan dari Perjanjian Kredit berikut syarat-syarat umum Perjanjian Kredit BPR BANGUNARTA.
                </td>
            </tr>
        </table>

        Sebagai tanda persetujuan Bapak/Ibu, harap Notifikasi ini ditandatangani disertai dengan nama jelas. Demikian agar Bapak / Ibu maklum.

        <p></p>

        <table>
            <tr>
                <td width="40%"></td>
                <td></td>
                <td style="text-align: justify" width="40%">
                    Pamanukan, 13 November 2023
                </td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: text-top;" width="40%">
                    PT. BPR BANGUNARTA
                </td>
                <td></td>
                <td style="text-align: justify" width="42%">
                    Dengan ini saya menyatakan telah membaca mengetahui dan menyetujui isi Notifikasi Kredit <br>
                    No. 184/NK/PBA/XI/2023
                </td>
            </tr>
            <tr>
                <td class="text-center" width="40%">
                    <center>
                        <img src="https://firebase.google.com/static/docs/ml-kit/images/examples/qrcode.png" alt="" style="width: 100px;hight:100px;margin-top:-32px;">
                    </center>
                    <u>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Surveyor
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </u>
                    <br>
                    MUHIDIN PRASETYO
                </td>
                <td></td>
                <td class="text-center" width="40%">
                    <p style="margin-top:70px;"></p>
                    <u>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Debitur
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </u>
                    <br>
                    ZULFADLI RIZAL
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
