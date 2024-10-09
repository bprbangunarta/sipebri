<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Konfirmasi Tabungan</title>
    <style>
        @page {
            size: A4;
            margin: 1;
        }

        body {
            font-family: "Calibri", sans-serif;
            margin: 40px;
            line-height: 1.2;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .content {
            margin-bottom: 40px;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        .cont {
            border-collapse: collapse;
        }

        .cont th {
            background: rgb(214, 212, 212);
            text-align: center;
        }

        .cont th,
        .cont td {
            border: 1px solid black;
            padding: 10px;
        }

        .sub_table {
            padding: 10px;
        }

        ol.custom-list {
            list-style-type: none;
            counter-reset: list-counter;
            margin-left: -2em;
        }

        ol.custom-list li {
            counter-increment: list-counter;
            position: relative;
            padding-left: 1.5em;
            line-height: 1.5em;
        }

        ol.custom-list li::before {
            content: counter(list-counter, lower-alpha) ") ";
            position: absolute;
            left: 0;
        }

        .ttd {
            float: right;
            width: 30%;
            height: auto;
        }

        .ttd .sub {
            align-items: center;
            text-align: center;
        }


        @media print {
            body {
                font-size: 11pt;
            }

            .cont th {
                background: rgb(214, 212, 212);
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h4><u>LEMBAR KONFIRMASI PEMAHAMAN NASABAH TABUNGAN</u></h4>
        <p style="font-size: 9pt; text-align:justify;">
            <i>Lembar konfirmasi ini merupakan bentuk pemenuhan kewajiban PT BPR Bangunarta, sebagaimana diatur dalam
                Peraturan Otoritas Jasa Keuangan Nomor 22 Tahun 2023 tentang Pelindungan Konsumen dan Masyarakat di
                Sektor
                Jasa Keuangan.</i>
        </p>
    </div>
    <div class="content">
        <table class="info-table">
            <tr>
                <td style="width: 25%;">Nama</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="text-align:justify;">
                    {{ ucwords($data->alamat_ktp) }}
                </td>
            </tr>
            <tr>
                <td>Nomor Rekening</td>
                <td>:</td>
                <td>{{ $noacc }}</td>
            </tr>
        </table>

        <p style="text-align:justify;">
            Mohon memberikan tanda centang ( √ ) pada kolom yang tersedia di sebelah pernyataan masing-masing nomor:
        </p>

        <table class="cont">
            <tr>
                <th>No</th>
                <th>Konfirmasi Nasabah</th>
                <th width='8%'>Ya</th>
                <th width='8%'>Tidak</th>
            </tr>
            <tr>
                <td style="text-align: center">1</td>
                <td>Nasabah telah membaca dan memahami sepenuhnya rincian biaya yang tercantum dalam Ketentuan Produk
                    Tabungan</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center">2</td>
                <td>Nasabah telah membaca dan memahami sepenuhnya manfaat dari Produk tabungan yang akan didapatkannya.
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center">3</td>
                <td>Nasabah telah membaca, menimbang, dan memahami sepenuhnya risiko atas Produk Tabungan yang akan
                    didapatkannya.</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center">4</td>
                <td>Nasabah telah membaca dan memahami sepenuhnya setiap dan seluruh hak dan kewajiban masing-masing
                    pihak dalam Perjanjian Kredit termasuk diantaranya: <br>
                    <ol class="custom-list">
                        <li> Mekanisme layanan pengaduan</li>
                        <li> Hak Nasabah untuk mengakhiri perjanjian tanpa dikenakan pinalty atau biaya sanksi
                            bila dalam masa jeda</li>
                        <li> Mekanisme layanan pengaduan</li>
                        <li> Mekanisme layanan pengaduan</li>
                    </ol>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center">5</td>
                <td>PT. BPR Bangunarta telah memberikan waktu yang cukup kepada Nasabah untuk memahami klausula
                    perjanjian yang disampaikan.</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <p>Kesimpulan atas konfirmasi pemahaman Nasabah terkait Ketentuan Produk Tabungan :</p>
        <p>
        <div style="display: inline-block; border: 1px solid black; width: 10px; height: 10px;"></div> &nbsp; Nasabah
        telah memahami isi dari Ketentuan Produk Tabungan
        </p>

        <p>
        <div style="display: inline-block; border: 1px solid black; width: 10px; height: 10px;"></div> &nbsp; Nasabah
        tidak memahami isi dari Ketentuan Produk Tabungan
        </p>

        <div class="ttd">
            <div class="sub">
                Subang, {{ $data->tgl }}
                <br>
                Nasabah,
                <br><br><br><br><br><br><br>
                <hr style="border: 1px solid black; width:80%;">
            </div>
        </div>

    </div>

    <script>
        // window.print();
        window.onload = function() {

            window.print();
        };
    </script>
</body>

</html>
