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
                <td style="width: 15%;">{{ $data->nasabah_kode }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Nama</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%; text-align:left;">{{ Str::upper($data->nama_nasabah) }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Alamat</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%; text-align:left;">
                    {{ Str::upper($data->alamat_ktp) }}
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
                <td style="width: 15%;">{{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Jangka Waktu</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Penggunaan</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ $data->penggunaan }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Nilai Taksasi Agunan</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ 'Rp. ' . ' ' . number_format($data->total_taksasi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Repayment Capacity</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ $data->rc_akhir }} %</td>
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
                <td style="width: 15%;">{{ 'Rp. ' . ' ' . number_format($data->plafon_usulan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Jangka Waktu</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 0.3%;"></td>
                <td style="width: 16%;">Suku Bunga</td>
                <td style="width: 0.3%;">:</td>
                <td style="width: 15%;">{{ $data->sb_usulan }} %</td>
            </tr>
            <tr>
                <td style="width: 0.3%;">4.</td>
                <td style="width: 16%;">Persetujuan</td>
                <td style="width: 0.3%;"></td>
                <td style="width: 15%;"></td>
            </tr>
            @forelse ($usulan as $item)
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
                    <td colspan="2">{{ $loop->iteration }}. {{ ucwords($item->catatan) }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">Layak untuk diberikan pinjaman sebesar
                        {{ 'Rp. ' . ' ' . number_format($item->usulan_plafon, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="align-content: center; text-align:center;">
                        <center>
                            {{-- <p style="margin-top:10px;"></p> --}}
                            <img src="{{ asset('storage/image/qr_code/' . $item->qr) }}" width="100" height="100"
                                style="margin-top:-30px;">
                        </center>
                        <div style="margin-bottom: -15px;">{{ $item->nama_user }}</div>
                        <div
                            style="width: 70%; border-bottom: 1px solid black;margin-bottom: -10px; display: inline-block;">
                        </div>
                        <div style="width: 70%; border-bottom: 1px solid black; display: inline-block;"></div>
                        <div style="margin-bottom: -15px;">{{ $item->role_name }}</div>
                    </td>
                    <td colspan="2" style="text-align:justify;">Dengan suku bunga {{ $data->suku_bunga }} % / bulan
                        {{ $item->metode_rps }} untuk jangka
                        waktu {{ $data->jangka_waktu }} bulan
                        Biaya ADM
                        {{ 'Rp. ' . ' ' . number_format($data->biaya_admin, 0, ',', '.') ?? 'Rp. ' . ' ' . '0' }}
                    </td>
                </tr>
            @empty
            @endforelse
            {{-- <tr>
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
            </tr> --}}
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
