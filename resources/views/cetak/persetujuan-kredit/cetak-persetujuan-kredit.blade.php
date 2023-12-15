<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catak Persetujuan Kredit</title>
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

        <h4 style="text-align: center;font-size: 12pt;">LEMBAR PERSETUJUAN DAN KONTROL KREDIT</h4>

        <table>
            <tr>
                <td style="width: 3%;">1. </td>
                <td style="width: 22%;">Identitas Calon Debitur</td>
                <td style="width: 2%;"></td>
                <td style="width: 73%;"></td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Kode Pengajuan</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->kode_pengajuan }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Nama Nasabah</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ Str::upper($data->nama_nasabah) }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;vertical-align: text-top;">Alamat Lengkap</td>
                <td style="width: 2%;vertical-align: text-top;"><center> : </center></td>
                <td>{{ Str::upper($data->alamat_ktp) }}</td>
            </tr>

            <tr>
                <td style="width: 3%;">2. </td>
                <td style="width: 22%;">Pengajuan Kredit</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Plafon</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Jangka Waktu</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Penggunaan</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->penggunaan }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Nilai Taksasi Agunan</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->total_taksasi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Repayment Capacity</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->rc_akhir }} %</td>
            </tr>

            <tr>
                <td style="width: 3%;">3. </td>
                <td style="width: 22%;">Usulan Kredit</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Plafon</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->plafon_usulan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Jangka Waktu</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Suku Bunga</td>
                <td style="width: 2%;"><center> : </center></td>
                <td>{{ $data->sb_usulan }} %</td>
            </tr>

            <tr>
                <td style="width: 3%;">4. </td>
                <td style="width: 22%;">Persetujuan Kredit</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>

            @forelse ($usulan as $item)
            <tr>
                <td></td>
                <td class="text-center" style="width: 22%;" style="vertical-align: text-top;">
                    <img src="{{ asset('storage/image/qr_code/' . $item->qr) }}" width="100" height="100">
                </td>
                <td style="width: 2%;"></td>
                <td>
                    <b>{{ $item->role_name }}</b> <br>
                    {{ $item->nama_user }}
                    <p></p>

                    <b>Komentar</b> <br>
                    {{ ucwords($item->catatan) }} <br>
                    Layak untuk diberikan pinjaman sebesar {{ 'Rp. ' . ' ' . number_format($item->usulan_plafon, 0, ',', '.') }}
                    <p></p>

                    Dengan suku bunga {{ $data->suku_bunga }} % / bulan {{ $item->metode_rps }} untuk jangka waktu {{ $data->jangka_waktu }} bulan
                    <br>
                    Biaya ADM {{ 'Rp. ' . ' ' . number_format($data->biaya_admin, 0, ',', '.') ?? 'Rp. ' . ' ' . '0' }}
                    <p></p>
                    <p></p>
                </td>
            </tr>
            <tr>
                <td colspan="4"><hr></td>
            </tr>
            @empty
            @endforelse


            {{-- @forelse ($usulan as $item)
            <tr>
                <td class="text-center" colspan="2">
                    <p style="margin-top:30px;"></p>
                    <img src="{{ asset('storage/image/qr_code/' . $item->qr) }}" width="100" height="100" style="margin-top:-30px;">

                    <br>
                    {{ $item->nama_user }}
                    <hr>
                    <font style="text-transform: uppercase;">{{ $item->role_name }}</font>
                </td>
                <td></td>
                <td style="text-align: justify;vertical-align: text-top;">
                    Komentar: <br>
                    {{ ucwords($item->catatan) }}
                    <p>
                    Dengan suku bunga {{ $data->suku_bunga }} % / bulan {{ $item->metode_rps }} untuk jangka waktu {{ $data->jangka_waktu }} bulan
                    <br>
                    Biaya ADM {{ 'Rp. ' . ' ' . number_format($data->biaya_admin, 0, ',', '.') ?? 'Rp. ' . ' ' . '0' }}
                    </p>
                </td>
            </tr>
            @empty
            @endforelse --}}
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
