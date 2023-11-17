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

        /* td {
            text-align: center;
        } */

        .content {
            width: 100%;
            max-width: 100%;
            padding: 10px;
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
            margin-left: 40px;
            margin-top: -14px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <br>
        <br>
        <br>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Nomor</td>
                <td style="width: 2%;">:</td>
                <td>233/NK/PBA/VIII/2023</td>
            </tr>
            <tr>
                <td style="width: 14%;">Tanggal</td>
                <td style="width: 2%;">:</td>
                <td>16 Agustus 2023</td>
            </tr>
            <tr>
                <td style="width: 14%;">Kode Nasabah</td>
                <td style="width: 2%;">:</td>
                <td>00028728-004</td>
            </tr>
        </table>

        <p style="font-size: 13px; lin">Kepada Yth. <br> Bapak/Ibu YUDHA SAKTI PRATAMA / SITI IRHAMNAH KARWIYANTI <br>
            Di
            <br> KAMPUNG KRAJAN BARAT RT. 07/02 DS. PASIRBUNGUR - PURWADADI - SUBANG No. Telp/Hp :
            085210109922
        </p>
        <p>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 14%;">Perihal</td>
                <td style="width: 2%;">:</td>
                <td><b> Notifikasi Kredit</b></td>
            </tr>
        </table>
        </p>

        <p>
            Menunjuk permohonan fasilitas kredit Konsumsi Lainnya atas nama Bapak/Ibu, dengan ini kami beritahukan bahwa
            PT BPR
            PAMANUKAN BANGUNARTA dapat menyetujui permohonan kredit Bapak/Ibu tersebut, dengan ketentuan-ketentuan dan
            syarat-syarat sebagai berikut :
        </p>
        <p>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 2%;">1.</td>
                <td style="width: 30%;">Limit Kredit</td>
                <td style="width: 2%;">:</td>
                <td>Rp 52.000.000 ( lima puluh dua juta rupiah )</td>
            </tr>
            <tr>
                <td style="width: 2%;">2.</td>
                <td style="width: 30%;">Jenis Kredit</td>
                <td style="width: 2%;">:</td>
                <td>Konsumsi Lainnya</td>
            </tr>
            <tr>
                <td style="width: 2%;">3.</td>
                <td style="width: 30%;">Tujuan Penggunaan Kredit</td>
                <td style="width: 2%;">:</td>
                <td>Kredit Konsumsi Lainnya. Sepanjang tidak bertentangan dengan ketentuan hukum
                    yang berlaku, norma kesusilaan dan ketertiban umum.</td>
            </tr>
            <tr>
                <td style="width: 2%;">4.</td>
                <td style="width: 30%;">Sektor Ekonomi</td>
                <td style="width: 2%;">:</td>
                <td>1020. Bukan Lapangan Usaha - Lainnya</td>
            </tr>
            <tr>
                <td style="width: 2%;">5.</td>
                <td style="width: 30%;">Agunan</td>
                <td style="width: 2%;">:</td>
                <td>4 ( Empat )</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;"></td>
                <td style="width: 2%;"></td>
                <td style="text-align:justify;">1 BPKB KENDARAAN RODA 2, HONDA V1J02Q32L1 A/T, 2021,
                    MH1KF7114MK018142, KF71E1018243, T2173XB, Q-07118481, HITAM,
                    YUDHA SAKTI PRATAMA, KRAJAN BARAT 07/02 PASIRBUNGUR
                    PURWADADI SUBANG
                    2 BPKB KENDARAAN RODA 2, YAMAHA RG10, 2016, MH3RG1020GK019466,
                    G401E0046643, T3391YM, M-13758144, PUTIH, YUDHA SAKTI PRATAMA,
                    KRAJAN BARAT 07/02 PASIRBUNGUR PURWADADI SUBANG
                    3 KARTU DAN SALDO JAMSOSTEK ATAS NAMA YUDHA SAKTI PRATAMA
                    NO 21035199716
                    4 SK DIREKSI NO 29/SK.DIR/PBA/V/2021</td>
            </tr>
            <tr>
                <td style="width: 2%;">6.</td>
                <td style="width: 30%;">Jangka Waktu Kredit</td>
                <td style="width: 2%;">:</td>
                <td>36 ( Tiga Puluh Enam ) bulan terhitung sejak tanggal akad kredit.</td>
            </tr>
            <tr>
                <td style="width: 2%;">7.</td>
                <td style="width: 30%;">Pembayaran Kembali</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">a. Pembayaran Pokok dan bunga</td>
                <td style="width: 2%;">:</td>
                <td>Wajib dilakukan oleh debitur sebesar Rp. ........................................... pada setiap
                    bulan,
                    selambat-lambatnya pada tanggal yang sama dengan tanggal akad kredit.</td>
            </tr>
            <tr>
                <td style="width: 2%;">8.</td>
                <td style="width: 30%;">Suku Bunga</td>
                <td style="width: 2%;">:</td>
                <td>13 % Efektif Anuitas / tahun</td>
            </tr>
            <tr>
                <td style="width: 2%;">9.</td>
                <td style="width: 30%;">Denda</td>
                <td style="width: 2%;">:</td>
                <td>0.10 % per hari dari angsuran pokok dan atau bunga yang tertunggak</td>
            </tr>
            <tr>
                <td style="width: 2%;">10.</td>
                <td style="width: 30%;">Biaya Kredit</td>
                <td style="width: 2%;">:</td>
                <td>0.10 % per hari dari angsuran pokok dan atau bunga yang tertunggak</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">a. Provisi Sebesar</td>
                <td style="width: 2%;">:</td>
                <td>0,50 % Rp 260.000</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">b. Administrasi Bank Sebesar</td>
                <td style="width: 2%;">:</td>
                <td>0,00 % Rp 0</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">c. APHT</td>
                <td style="width: 2%;">:</td>
                <td>0,00 % Rp 0</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">d. Fiducia</td>
                <td style="width: 2%;">:</td>
                <td>0,00 % Rp 0</td>
            </tr>
            <tr>
                <td style="width: 2%;">11.</td>
                <td style="width: 30%;">Jumlah Biaya Asuransi</td>
                <td style="width: 2%;">:</td>
                <td>Ekawaktu / TLO / -</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">a. Kendaraan (TLO)</td>
                <td style="width: 2%;">:</td>
                <td>Rp 0 (dengan nilai pertanggungan Rp. 0 )</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">b. Jiwa (Ekawaktu)</td>
                <td style="width: 2%;">:</td>
                <td>Rp 0 (dengan nilai pertanggungan Rp. 0 )</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;">c. Kecelakaan Plus</td>
                <td style="width: 2%;">:</td>
                <td>Rp 0 (dengan nilai pertanggungan Rp. 0 )</td>
            </tr>
            <tr style="height: 20px; background-color:blueviolet;">
                <td style="width: 2%;"></td>
                <td style="width: 30%;"></td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;">12.</td>
                <td style="width: 30%;">Untuk jaminan Kendaraan</td>
                <td style="width: 2%;">:</td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">a. Debitur tidak akan menjaminkan atau
                    menggunakannnya sebagai
                    jaminan pinjaman,
                    menjual atau
                    memindahtangankan dengan cara apapun kepada pihak manapun dan menggunakan kendaraan tersebut hanya
                    untuk pemakaian pribadi sampai seluruh Hutang dinyatakan lunas oleh pihak Bank.</td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <img src="{{ asset('assets/img/pba.png') }}" style="width:400px;">
        <br>
        <br>
        <table style="font-size: 13px;">
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%;"></td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">b. Apabila debitur lalai dari kewajiban atau
                    tidak dapat
                    mengangsur bunga dan atau pokok pinjaman selama 3 (tiga )
                    bulan , maka Bank berhak melelang atau menjual dibawah tangan , barang jaminan kendaraan tersebut
                    diatas
                    kepada pihak ketiga guna mengangsur atau melunasi Hutang Debitur kepada Bank.</td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">c. Jika batas maksimal pinjaman berikut
                    bunga
                    telah melebihi
                    jaminan yang dimiliki debitur, maka dengan ini
                    Debitur menyatakan pula bahwa kepemilikan barang-barang jaminan kendaraan tersebut telah dialihkan
                    kepada
                    Bank dengan kewajiban Debitur untuk meyerahkan barang-barang jaminan kendaraan tersebut kepada Bank.
                </td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">d. Dalam hal kejadian diatas ( sesuai nomor
                    b dan c ), Debitur
                    tidak dapat memenuhi kewajibannya untuk
                    menyerahkan barang jaminan kendaraann, maka Debitur bersedia untuk diajukan ke proses peradilan
                    pidana
                    karena telah melakukan penggelapan atas barang yang sudah bukan lagi miliknya, sesuai dengan yang
                    tercantum
                    dalam pasal 372 KUHP.
                </td>
            </tr>
            <tr style="height: 15px;">
                <td style="width: 2%;"></td>
                <td style="width: 30%;"></td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr style="height: 15px;">
                <td style="width: 2%;">13.</td>
                <td style="width: 30%;">Untuk jaminan Non Kendaraan :</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">a. Debitur tidak akan menjaminkan atau
                    menggunakannnya sebagai jaminan pinjaman, menjual atau
                    memindahtangankan dengan cara apapun kepada pihak manapun sampai seluruh Hutang dinyatakan lunas
                    oleh pihak Bank.</td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td style="width: 30%; text-align:justify;" colspan="3">b. Apabila debitur lalai dari kewajiban
                    atau tidak dapat mengangsur bunga dan atau pokok pinjaman selama 3 (tiga
                    ) bulan atau lebih, maka Bank berhak melakukan tindakan-tindakan yang mendorong untuk penyelesaian
                    tunggakan termasuk dalam hal ini BANK melakukan tindakan pemasangan plakat “TANAH INI SEDANG DI
                    JAMINKAN KE BPR BANGUNARTA" sampai tunggakan tersebut dapat terselesaikan atau Bank melakukan
                    upaya-upaya yang mengarah pada penguasaan agunan untuk penyelesaian kredit tersebut dan dalam hal
                    ini Debitur tidak berhak untuk menolak, mempersulit, menghalang-halangi maaupun menghambat,</td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 30%; text-align:justify;" colspan="4">14. Debitur tidak diperbolehkan memberikan
                    suatu imbalan dalam bentuk apapun kepada pejabat dan atau karyawan PT
                    BPR PAMANUKAN BANGUNARTA berkenaan dengan persetujuan pemberian kredit, kecuali jasa survey sebesar
                    Rp, 20.000,- yang dibayar oleh debitur pada saat survey yang disertakan bukti penerimaan uang jasa
                    survey. Apabila
                    dikemudian hari diketahui bahwa Debitur melanggar larangan tersebut, maka kepada Debitur dan Pejabat
                    atau petugas BANK dapat dikenakan sanksi sesuai ketentuan BANK.</td>
            </tr>
            <tr>
                <td style="width: 30%; text-align:justify;" colspan="4">15. Notifikasi Kredit ini merupakan bagian
                    yang tidak dapat terpisahkan dari Perjanjian Kredit berikut syarat umum
                    Perjanjian Kredit Konsumsi Lainnya PT. BPR PAMANUKAN BANGUNARTA. Sebagai tanda persetujuan
                    Bapak/Ibu, harap Notifikasi ini ditandatangani disertai dengan nama jelas.
                    Demikian agar Bapak / Ibu maklum.</td>
            </tr>
        </table>
        <br>
        <br>

        <table style="font-size: 13px;">
            <tr>
                <td style="text-align:justify;">Surat ini tidak memerlukan tanda tangan dari pejabat Bank karena
                    dicetak secara Komputerisasi</td>
                <td style="width: 30%;"></td>
                <td style="text-align: justify;">Pamanukan, 16 Agustus 2023 <br>
                    Dengan ini saya menyatakan telah membaca,
                    mengetahui dan menyetujui isi Notifikasi Kredit
                    No. 233/NK/PBA/VIII/2023</td>
            </tr>
        </table>
        <table style="font-size: 13px; margin-top: 70px;">
            <tr>
                <td style="width: 50%"></td>
                <td style="width: 50%; text-align:center; margin-top: 10px; position:relative;">
                    <div style="border-bottom: 1px solid black; display:inline-block; width: 60%;"></div><br>Debitur
                </td>
            </tr>
        </table>
        <p>Catatan : <br> Adapun kelengkapan dokumen yang harus diserahkan saat realisasi kredit adalah :</p>

        <table style="font-size: 13px;">
            <tr>
                <td>1.</td>
                <td>BPKB PCX ASLI, BPKB R25, SKPG, SP TDK IKUT TLO, KK</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>SK ASLI, KARTU JHT, PAS FOTO SUAMI ISTRI, STNK AKF </td>
            </tr>
            <tr>
                <td>3.</td>
                <td>FOTO MOTOR, GOSOKAN NO MESIN & RANGKA, SRT NIKAH </td>
            </tr>
            <tr>
                <td>4.</td>
                <td></td>
            </tr>
            <tr>
                <td>5.</td>
                <td></td>
            </tr>
        </table>
        </p>



    </div>

    <script>
        // window.print();
        window.onload = function() {

            window.print();
        };
    </script>
</body>

</html>
