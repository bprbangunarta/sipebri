<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standing Instruction</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
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
            margin-left: 1cm;
            margin-right: 2.0cm;
        }

        .signature-section {
            margin-top: 60px;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature div {
            text-align: center;
        }

        .info-table {
            border-collapse: collapse;
            /* margin-top: -15px; */
        }

        .info-table td {
            padding: 2px;
            vertical-align: top;
        }

        .page-break {
            page-break-before: always;
        }

        .page-dua {
            font-family: "Calibri", sans-serif;
            margin-left: 0cm;
            margin-right: 0cm;
            line-height: 1.2;
        }

        @media print {
            body {
                font-size: 12pt;
            }
        }

        .penghasilan {
            margin-left: 40px;
            margin-top: -14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3><u>STANDING INSTRUCTION</u></h3>
    </div>
    <div class="content">
        <br>
        <p>Kepada :</p>
        <p>
            PT. Bank Mandiri (Persero) Tbk<br>
            Cabang Purwakarta Wanayasa<br>
            Kp. Krajan Rt. 06 Rw. 03 Ds. Wanayasa <br>
            Purwakarta – 41174
        </p>

        <table class="info-table">
            <tr>
                <td colspan="2">Yang bertanda tangan di bawah ini :</td>
            </tr>
            <tr>
                <td style="width: 25%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td class="nowrap">Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $data->tempat_lahir }}, {{ $data->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Nomor Rekening</td>
                <td>:</td>
                <td>{{ $data->no_rekening }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="text-align:justify;">
                    {{ ucwords($data->alamat_ktp) }}
                </td>
            </tr>
            <tr>
                <td>Nomor KTP</td>
                <td>:</td>
                <td style="vertical-align: middle;">{{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>:</td>
                <td>{{ $data->no_telp }}</td>
            </tr>
        </table>

        <p>Mohon agar dilakukan pemindahbukuan dana dari rekening saya tersebut di atas dengan penjelasan sebagai
            berikut:</p>

        <table class="info-table">
            <tr>
                <td style="width: 145px;">Waktu Pelaksanaan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="width: 2%;">:</td>
                <td>Setiap tanggal 10 (Sepuluh)</td>
            </tr>
            <tr>
                <td>Jangka Waktu</td>
                <td>:</td>
                <td>{{ $data->jangka_waktu }}</td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>:</td>
                <td>{{ 'Rp. ' . number_format($data->angsuran, '0', ',', '.') }}</td>
            </tr>
            <tr>
                <td>Terbilang</td>
                <td>:</td>
                <td>
                    <font class="text-hg" style="text-transform: capitalize;">
                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran) }}</font>
                </td>
            </tr>
        </table>

        <br>

        <table class="info-table">
            <tr>
                <td style="width: 145px;">Nomor Rekening</td>
                <td style="width: 2%;">:</td>
                <td>173-00-0808089-8</td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td>:</td>
                <td>Pamanukan Bangunarta</td>
            </tr>
            <tr>
                <td>Bank Penerima</td>
                <td>:</td>
                <td>PT. Bank Mandiri (Persero) Tbk Cab. Purwakarta Wanayasa</td>
            </tr>
        </table>

        <p style="text-align: justify;">Biaya yang timbul dalam pelaksanaan standing instruction ini dapat dibebankan
            kepada rekening saya di atas.
            Demikian surat ini saya buat untuk dipergunakan sebagaimana mestinya dan akan berakhir sesuai dengan jangka
            waktu atau setelah ada pemberitahuan tertulis dari saya.</p>

        <table style="width:100%;">
            <tr>
                <td style="float: left;">
                    <center>
                        <p>Subang, {{ $data->tanggal_spk }}</p>
                        <br>
                        <p><i>Meterai 10.000</i></p>
                        <br>
                        <label
                            style='margin-top: 10px; font-size:12px; display: block;'>{{ $data->nama_nasabah }}</label>
                        <p style="margin-top: -9px;"> (..............................................)</p>
                    </center>
                </td>
                <td style="float: right; width:38%; margin-bottom:0; margin-right:0;">
                    <center>
                        <p>Mengetahui & Menyetujui <br>
                            PT. Bank Mandiri (Persero) Tbk
                            Cab. Purwakarta Wanayasa
                        </p>
                        <br>
                        <br>

                        <p style="margin-top: 30px;">(..............................................)</p>
                    </center>
                </td>
            </tr>
        </table>

        <div class="page-break"></div>

        <div class="header" style='font-family: "Calibri", sans-serif; margin-top: -30px;'>
            <br>
            <br>
            <br>
            <h3><b><u>PERMOHONAN PEMBLOKIRAN <br> DAN <br> PENDEBETAN REKENING</u></b>
            </h3>
        </div>

        <p>Kepada :</p>
        <p>
            PT. Bank Mandiri (Persero) Tbk<br>
            Cabang Purwakarta Wanayasa<br>
            Kp. Krajan Rt. 06 Rw. 03 Ds. Wanayasa <br>
            Purwakarta – 41174
        </p>

        <table class="info-table">
            <tr>
                <td colspan="2">Yang bertanda tangan di bawah ini :</td>
            </tr>
            <tr>
                <td style="width: 25%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td class="nowrap">Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $data->tempat_lahir }}, {{ $data->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Nomor Rekening</td>
                <td>:</td>
                <td>{{ $data->no_rekening }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="text-align:justify;">
                    {{ ucwords($data->alamat_ktp) }}
                </td>
            </tr>
            <tr>
                <td>Nomor KTP</td>
                <td>:</td>
                <td style="vertical-align: middle;">{{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>:</td>
                <td>{{ $data->no_telp }}</td>
            </tr>
        </table>

        <p>Mohon agar dilakukan pemindahbukuan dana dari rekening saya tersebut di atas dengan penjelasan sebagai
            berikut:</p>

        <table class="info-table">
            <tr>
                <td style="width: 145px;">Waktu Pelaksanaan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="width: 2%;">:</td>
                <td>Setiap tanggal 10 (Sepuluh)</td>
            </tr>
            <tr>
                <td>Jangka Waktu</td>
                <td>:</td>
                <td>{{ $data->jangka_waktu }}</td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>:</td>
                <td>{{ 'Rp. ' . number_format($data->angsuran, '0', ',', '.') }}</td>
            </tr>
            <tr>
                <td>Terbilang</td>
                <td>:</td>
                <td>
                    <font class="text-hg" style="text-transform: capitalize;">
                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran) }}</font>
                </td>
            </tr>
        </table>

        <br>

        <table class="info-table">
            <tr>
                <td style="width: 145px;">Nomor Rekening</td>
                <td style="width: 2%;">:</td>
                <td>173-00-0808089-8</td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td>:</td>
                <td>Pamanukan Bangunarta</td>
            </tr>
            <tr>
                <td>Bank Penerima</td>
                <td>:</td>
                <td>PT. Bank Mandiri (Persero) Tbk Cab. Purwakarta Wanayasa</td>
            </tr>
        </table>

        <p style="text-align: justify;">Biaya yang timbul dalam pelaksanaan standing instruction ini dapat dibebankan
            kepada rekening saya di atas.
            Demikian surat ini saya buat untuk dipergunakan sebagaimana mestinya dan akan berakhir sesuai dengan jangka
            waktu atau setelah ada pemberitahuan tertulis dari saya.</p>

        <table style="width:100%;">
            <tr>
                <td style="float: left;">
                    <center>
                        <p>Subang, {{ $data->tanggal_spk }}</p>
                        <br>
                        <p><i>Meterai 10.000</i></p>
                        <br>
                        <label
                            style='margin-top: 10px; font-size:12px; display: block;'>{{ $data->nama_nasabah }}</label>
                        <p style="margin-top: -9px;"> (..............................................)</p>
                    </center>
                </td>
                <td style="float: right; width:38%; margin-bottom:0; margin-right:0;">
                    <center>
                        <p>Mengetahui & Menyetujui <br>
                            PT. Bank Mandiri (Persero) Tbk
                            Cab. Purwakarta Wanayasa
                        </p>
                        <br>
                        <br>

                        <p style="margin-top: 30px;">(..............................................)</p>
                    </center>
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
