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
            /* border: 1px solid #000000; */
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
            /* text-align: justify; */
        }

        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.5cm;
            }
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:300px; margin-top: -15px;">
        <br>

        <p style="font-size: 16px; text-align:center;"><b>LEMBAR PERSETUJUAN DAN KONTROL KREDIT</b></p>
        <table style="font-size: 12px; width: 100%;">
            <tr>
                <td style="width: 0.3%;">1.</td>
                <td style="width: 16%;">Identitas calon debitur</td>
                <td style="width: 0.3%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Kode Nasabah</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">00028728-004</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Nama</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%; text-align:left;">YUDHA SAKTI PRATAMA</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Alamat</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%; text-align:left;">
                    KAMPUNG KRAJAN BARAT RT. 07/02 DS. PASIRBUNGUR - PURWADADI - SUBANG
                </td>
            </tr>
            <tr>
                <td style="width: 0.3%;">2.</td>
                <td style="width: 16%;">Pengajuan Kredit</td>
                <td style="width: 0.3%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Plafon</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">Rp 55.000.000</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Jangka Waktu</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">36 Bulan</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Penggunaan</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">39. Konsumsi Lainnya</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Nilai Taksasi Agunan</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">Rp 70.323.436</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Repayment Capacity</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">41.06 %</td>
            </tr>
            <tr>
                <td style="width: 0.3%;">3.</td>
                <td style="width: 16%;">Usulan</td>
                <td style="width: 0.3%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Plafon</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">Rp 52.000.000</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Jangka Waktu</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">36 Bulan</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Suku Bunga</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">13 %</td>
            </tr>
            <tr>
                <td style="width: 0.3%;">4.</td>
                <td style="width: 16%;">Persetujuan</td>
                <td style="width: 0.3%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Komentar :</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">1.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">2.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">3.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">4.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Layak untuk diberikan pinjaman sebesar Rp.</td>
            </tr>
            <tr>
                <td colspan="2" style="align-content: center; text-align:center;">
                    <div
                        style="width: 70%; border-bottom: 1px solid black;margin-bottom: -10px; display: inline-block;">
                    </div>
                    <div style="width: 70%; border-bottom: 1px solid black; display: inline-block;"></div>
                    <div style="margin-bottom: 5px;">Staff Analis Appraisal</div>
                </td>
                <td colspan="2" style="text-align:justify;">Dengan suku bunga ............... % / bulan
                    ............................ untuk jangka
                    waktu ................. bulan
                    Biaya ADM
                    ....................................................................................................................................
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Komentar :</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">1.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">2.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">3.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">4.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Layak untuk diberikan pinjaman sebesar Rp.</td>
            </tr>
            <tr>
                <td colspan="2" style="align-content: center; text-align:center;">
                    <div
                        style="width: 70%; border-bottom: 1px solid black;margin-bottom: -10px; display: inline-block;">
                    </div>
                    <div style="width: 70%; border-bottom: 1px solid black; display: inline-block;"></div>
                    <div style="margin-bottom: 5px;">Kasi Analis Appraisal</div>
                </td>
                <td colspan="2" style="text-align:justify;">Dengan suku bunga ............... % / bulan
                    ............................ untuk jangka
                    waktu ................. bulan
                    Biaya ADM
                    ....................................................................................................................................
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Komentar :</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">1.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">2.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">3.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">4.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Layak untuk diberikan pinjaman sebesar Rp.</td>
            </tr>
            <tr>
                <td colspan="2" style="align-content: center; text-align:center;">
                    <div
                        style="width: 70%; border-bottom: 1px solid black;margin-bottom: -10px; display: inline-block;">
                    </div>
                    <div style="width: 70%; border-bottom: 1px solid black; display: inline-block;"></div>
                    <div style="margin-bottom: 5px;">Kabag Administrasi</div>
                </td>
                <td colspan="2" style="text-align:justify;">Dengan suku bunga ............... % / bulan
                    ............................ untuk jangka
                    waktu ................. bulan
                    Biaya ADM
                    ....................................................................................................................................
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Komentar :</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">1.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">2.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">3.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">4.</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">Layak untuk diberikan pinjaman sebesar Rp.</td>
            </tr>
            <tr>
                <td colspan="2" style="align-content: center; text-align:center;">
                    <div
                        style="width: 70%; border-bottom: 1px solid black;margin-bottom: -10px; display: inline-block;">
                    </div>
                    <div style="width: 70%; border-bottom: 1px solid black; display: inline-block;"></div>
                    <div style="margin-bottom: 5px;">Direksi</div>
                </td>
                <td colspan="2" style="text-align:justify;">Dengan suku bunga ............... % / bulan
                    ............................ untuk jangka
                    waktu ................. bulan
                    Biaya ADM
                    ....................................................................................................................................
                </td>
            </tr>
        </table>
    </div>

    <script>
        // window.print();
        window.onload = function() {

            window.print();
        };
    </script>
</body>

</html>
