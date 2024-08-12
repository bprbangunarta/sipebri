<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detail Angsuran RSC</title>

    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.5cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif
        }

        .content {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            text-align: justify;
        }

        .col .table {
            width: 100%;
        }

        .col .table td {
            padding: 3px;
        }

        .tabel_setoran {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
            font-size: 10pt;
        }

        .tabel_setoran th {
            padding: 3px;
            border: 1px solid black;
            text-align: center;
        }

        .tabel_setoran td {
            padding: 5px;
            border: 1px solid black;
        }



        @media print {
            .content {
                margin-top: -1cm;
                padding-top: 1cm;
                padding-bottom: 1.5cm;
                /* padding-left: 1.2cm;
                padding-right: 1.2cm; */
            }

            /* .col .table {
                padding-left: 1.0cm;
                padding-right: 1.0cm;
            } */

            .col .tabel_setoran {
                table-layout: auto;
                /* padding-left: 1.0cm;
                padding-right: 1.0cm; */
            }

        }
    </style>
</head>

<body>

    <center>
        <h5>PT. BPR PAMANUKAN BANGUNARTA</h5>
        <h3 style="margin-top: -20px;">TABEL SETORAN PINJAMAN ({{ $data->metode_rps }})</h3>
    </center>

    <div class="content">

        <div class="col">

            <table class="tabel" style="font-size: 9pt;">
                <tr>
                    <td style="width: 14%;">Nama Debitur</td>
                    <td style="width: 1%;">:</td>
                    <td colspan="4" style="width: 60%;">{{ $data->nama_nasabah }}</td>
                    <td style="width: 9%;">No. SPK</td>
                    <td>:</td>
                    <td style="width: 15%;">{{ $data->no_spk }}</td>
                </tr>
                <tr>
                    <td style="width: 14%;">Alamat</td>
                    <td>:</td>
                    <td colspan="4" style="width: 60%;" style="text-align: justify; vertical-align: text-top;">
                        {{ $data->alamat_ktp }}
                    </td>
                    <td style="width: 9%;">No. Rek</td>
                    <td>:</td>
                    <td style="width: 15%;">{{ $data->no_tab }}</td>
                </tr>
                <tr>
                    <td>Suku Bunga</td>
                    <td>:</td>
                    <td style="width: 15%;">{{ $data->suku_bunga }}% p.a</td>
                    <td style="width: 17%;">Pokok Pinjaman</td>
                    <td style="width: 1%;">:</td>
                    <td style="width: 27%;">{{ number_format($data->penentuan_plafon, '0', ',', '.') }}</td>
                    <td style="width: 9%;">No. Loan</td>
                    <td>:</td>
                    <td style="width: 15%;">{{ $data->no_kredit }}</td>
                </tr>
                <tr>
                    <td>Jk Wkt Kredit</td>
                    <td>:</td>
                    <td style="width: 15%;">{{ $data->jangka_waktu }}</td>
                    <td style="width: 17%;">Tanggal Realisasi</td>
                    <td style="width: 1%;">:</td>
                    <td colspan="4" style="width: 27%;">{{ $data->tgl_realisasi }}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Denda
                        Terlambat Setor per-hari
                        = Jml
                        Setoran X 0,1 %</td>
                </tr>
            </table>
            <br>
            <table class="tabel_setoran">
                <tr>
                    <th colspan="6">JADWAL SETORAN PINJAMAN</th>
                    <th colspan="6">TRANSAKSI SETORAN</th>
                </tr>
                <tr>
                    <th>Bln Ke-</th>
                    <th width='5%'>Tanggal</th>
                    <th>Setoran Pokok</th>
                    <th>Setoran Bunga</th>
                    <th>Jumlah Setoran</th>
                    <th>Saldo Pokok</th>
                    <th width='5%'>Tgl. Setor</th>
                    <th>Setoran Pokok</th>
                    <th>Setoran Bunga</th>
                    <th>Setoran Denda</th>
                    <th>Saldo Pokok</th>
                    <th width='5%'>Ket</th>
                </tr>

                @forelse ($angsuran as $item)
                    <tr>
                        <td style="text-align: center;">{{ $item['bulan_ke'] }}</td>
                        <td style="text-align: center;">{{ $item['tanggal_setoran'] }}</td>
                        <td style="text-align: center;">{{ number_format($item['setoran_pokok'], '0', ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($item['setoran_bunga'], '0', ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($item['jumlah_setoran'], '0', ',', '.') }}
                        </td>
                        <td style="text-align: center;">{{ number_format($item['sisa_plafon'], '0', ',', '.') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @empty
                @endforelse

            </table>
        </div>

    </div>

    <script>
        window.print()
    </script>
</body>

</html>
