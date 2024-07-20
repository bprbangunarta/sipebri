<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Persetujuan Kredit RSC</title>
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

        /* Style headers item */
        .headers {
            margin-top: 10px;
        }

        .item {
            margin-top: -10px;
        }

        .headers p {
            margin-left: 23px;
        }

        .headers table {
            margin-left: 23px;
            margin-top: -10px;
        }

        .headers table tr td {
            padding-bottom: 5px;
        }

        .ttd p {
            margin-left: -3px;
        }

        .headers .plafon_rsc {
            box-sizing: border-box;
            margin-top: 20px;
            width: 35%;
            margin-bottom: 30px;
        }

        .headers .plafon_rsc th {
            font-weight: bold;
            text-align: center;
        }

        .plafon_rsc th,
        .plafon_rsc tr,
        .plafon_rsc td {
            /* padding-left: 7px; */
            padding-left: 0px;
            /* border: 1px solid black; */
        }

        /* Style headers item */

        @media print {
            body {
                font-size: 10pt;
            }

            .content {
                margin-top: -57px;
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
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <div class="headers">
            <h2 style="text-align: center;font-size: 12pt;"><u>PENGUSULAN {{ $data->jenis_persetujuan }}
                    KREDIT</u>
            </h2>
            {{-- <label for="" style="font-weight:bold;">A. &nbsp;&nbsp;PENGUSULAN</label>
            <div class="item">
                <p>Atas dasar penilaian kembali kondisi usaha debitur, dengan ini kami mengusulkan agar permohonan
                    {{ $data->jenis_persetujuan }} atas sistem angsuran kredit yang bersangkutan dapat dipertimbangkan
                    untuk disetujui dengan ketentuan sebagai berikut :</p>

                <table>
                    <tr>
                        <td width='20px'>-</td>
                        <td>Plafon Rp. {{ number_format($data->penentuan_plafon, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>-</td>
                        <td>Jangka Waktu : {{ $data->jangka_waktu }} Bulan</td>
                    </tr>
                    <tr>
                        <td>-</td>
                        <td>Suku Bunga {{ $data->suku_bunga }} % p.a</td>
                    </tr>
                    <tr>
                        <td>-</td>
                        @if (is_null($data->jangka_musim))
                            <td>Pembayaran angsuran pokok setiap {{ $data->jangka_pokok }} bulan selama
                                {{ $data->jangka_waktu }} bulan</td>
                        @else
                            <td>Pembayaran angsuran pokok setiap {{ $data->jangka_pokok }} bulan selama
                                {{ $data->jangka_musim }} musim</td>
                        @endif
                    </tr>
                    <tr>
                        <td>-</td>
                        <td>Pembayaran angsuran bunga dilakukan pada setiap {{ $data->jangka_bunga }} bulan
                            {{ $data->metode_rps }}</td>
                    </tr>
                    <tr>
                        <td>-</td>
                        <td>Sistem angsuran {{ $data->metode_rps }}</td>
                    </tr>
                </table>

                <p>Demikian usulan ini semoga dapat dijadikan bahan pertimbangan dalam pengambilan keputusan lebih
                    lanjut.
                </p>

                <table style="width:97%; margin-right:50px; margin-top: -10px;">
                    <tr>
                        <td class="text-left" style="float: left;">
                            <div class="ttd">
                                <center>
                                    <p style="margin-top: -1.5px;"></p>
                                    <p>Kasi Analis</p>
                                    <p style="margin-top: 40px;"></p>
                                    <img src="{{ asset('storage/image/qr_code/' . $qr['Kasi Analis']) }}"
                                        width="100" height="100" style="margin-top:-30px;">
                                    <b>
                                        <p>
                                            <font style="text-transform: uppercase;">
                                                <u>{{ $petugas['Kasi Analis'] }}</u>
                                            </font>
                                        </p>
                                    </b>
                                </center>
                            </div>
                        </td>
                        <td class="text-right" style="float: right;">
                            <center>
                                <p style="margin-top: -1.5px;"></p>
                                <p>Pamanukan, {{ $data->tgl_usulan }}</p>
                                <p style="margin-top: 40px;"></p>
                                <img src="{{ asset('storage/image/qr_code/' . $qr['Staff Analis']) }}" width="100"
                                    height="100" style="margin-top:-30px; margin-left:20px;">
                                <b>
                                    <p>
                                        <font style="text-transform: uppercase;"><u>{{ $petugas['Staff Analis'] }}</u>
                                        </font>
                                    </p>
                                </b>
                            </center>
                        </td>
                    </tr>
                </table>
            </div> --}}
        </div>

        {{-- <div class="headers">
            <label for="" style="font-weight:bold;">B. &nbsp;&nbsp;PERSETUJUAN</label>
            <div class="item">
                <p>
                    Setelah dilakukan berbagai pertimbangan dan analisa sesuai dengan informasi debitur yang diperoleh
                    maka dengan ini di setujui usulan ( {{ $data->jenis_persetujuan }} ) kredit tersebut di
                    atas,dengan ketentuan :
                </p>

                <table>
                    <tr>
                        <td width='120px'>Plafond</td>
                        <td width='5px'>:</td>
                        <td>Rp. {{ number_format($data->penentuan_plafon, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Suku Bunga</td>
                        <td>:</td>
                        <td>{{ $data->suku_bunga }}%</td>
                    </tr>
                </table>

                <p style="margin-top: -1px;">
                    Jangka Waktu: @if (is_null($data->jangka_musim))
                        {{ $data->jangka_waktu }} bulan
                    @else
                        {{ $data->jangka_musim }} musim
                    @endif, Pembayaran angsuran bunga di lakukan dari bulan ke-
                    {{ $data->jangka_bunga }}
                    sampai dengan
                    bulan ke- @if (is_null($data->jangka_musim))
                        {{ $data->jangka_waktu }}
                    @else
                        {{ $data->jangka_musim }}
                    @endif , Pembayaran angsuran pokok + bunga dilakukan dari bulan ke-
                    {{ $data->jangka_pokok }} sampai
                    dengan
                    ke-@if (is_null($data->jangka_musim))
                        {{ $data->jangka_waktu }}
                    @else
                        {{ $data->jangka_musim }}
                    @endif
                </p>

                <table>
                    <tr>
                        <td width='120px'>Angsuran Pokok</td>
                        <td width='5px'>:</td>
                        <td>Rp. {{ number_format($data->angsuran_pokok, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Angsuran Bunga</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->angsuran_bunga, '0', ',', '.') }}</td>
                    </tr>
                </table>

                <table style="margin-top: 10px;">
                    <tr>
                        <td width='120px'>Biaya</td>
                        <td width='5px'></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Administrasi {{ $data->administrasi }}%</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->administrasi_nominal, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Asuransi Jiwa</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->asuransi_jiwa, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Asuransi TLO</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->asuransi_tlo, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Denda</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->denda_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Bunga</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->bunga_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pokok</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($data->poko_dibayar, '0', ',', '.') }}</td>
                    </tr>
                </table>

                <table style="width:97%; margin-right:50px; margin-top: 10px;">
                    <tr>
                        <td class="text-left" style="float: left;">
                            <div class="ttd">
                                <center>
                                    <p style="margin-top: -1.5px;"></p>
                                    <p>Direktur,</p>
                                    <p style="margin-top: 55px;"></p>
                                    <img src="{{ asset('storage/image/qr_code/' . $qr['Direksi']) }}" width="100"
                                        height="100" style="margin-top:-30px;">
                                    <b>
                                        <p>
                                            <font style="text-transform: uppercase;"><u>{{ $petugas['Direksi'] }}</u>
                                            </font>
                                        </p>
                                    </b>
                                </center>
                            </div>
                        </td>
                        <td class="text-right" style="float: right;">
                            <center>
                                <p style="margin-top: -1.5px;"></p>
                                <p>Pamanukan, {{ $data->tgl_usulan }}</p>
                                <p style="margin-top: -10px;">Kepala Bagian Analis</p>
                                <p style="margin-top: 40px;"></p>
                                <img src="{{ asset('storage/image/qr_code/' . $qr['Kabag Analis']) }}" width="100"
                                    height="100" style="margin-top:-35px; margin-left:20px;">
                                <b>
                                    <p>
                                        <font style="text-transform: uppercase;"><u>{{ $petugas['Kabag Analis'] }}</u>
                                        </font>
                                    </p>
                                </b>
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
        </div> --}}

        <table>
            <tr>
                <td style="width: 3%;">1. </td>
                <td style="width: 22%;">Data Debitur</td>
                <td style="width: 2%;"></td>
                <td style="width: 73%;"></td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Kode Pengajuan</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->pengajuan_kode }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Nama Nasabah</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ Str::upper($data->nama_nasabah) }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;vertical-align: text-top;">Alamat Lengkap</td>
                <td style="width: 2%;vertical-align: text-top;">
                    <center> : </center>
                </td>
                <td>{{ Str::upper($data->alamat_ktp) }}</td>
            </tr>

            <tr>
                <td style="width: 3%;">2. </td>
                <td style="width: 22%;">Data Kredit</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Plafon</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Jangka Waktu</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Penggunaan</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->penggunaan }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Nilai Taksasi Agunan</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->total_taksasi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Repayment Capacity</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->rc }} %</td>
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
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ 'Rp. ' . ' ' . number_format($data->penentuan_plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Jangka Waktu</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->jangka_waktu }} Bulan</td>
            </tr>
            <tr>
                <td style="width: 3%;"></td>
                <td style="width: 22%;">Suku Bunga</td>
                <td style="width: 2%;">
                    <center> : </center>
                </td>
                <td>{{ $data->suku_bunga }} %</td>
            </tr>

            <tr>
                <td style="width: 3%;">4. </td>
                <td style="width: 22%;">Persetujuan Kredit</td>
                <td style="width: 2%;"></td>
                <td></td>
            </tr>

            @forelse ($qr as $item)
                <tr>
                    <td></td>
                    <td class="text-center" style="width: 22%;" style="vertical-align: text-top;">
                        <img src="{{ asset('storage/image/qr_code/' . $item->data_qr_usulan) }}" width="100"
                            height="100">
                    </td>
                    <td style="width: 2%;"></td>
                    <td>
                        <b>{{ $item->role_name }}</b> <br>
                        {{ $item->nama_user }}
                        <p></p>

                        <b>Komentar</b> <br>
                        {{ ucwords($item->catatan) }} <br>
                        Layak untuk diberikan pinjaman sebesar
                        {{ 'Rp. ' . ' ' . number_format($item->usulan_plafon, 0, ',', '.') }}
                        <p></p>

                        Dengan suku bunga {{ $data->suku_bunga }} % / bulan {{ $item->metode_rps }} untuk jangka waktu
                        {{ $data->jangka_waktu }} bulan
                        <br>
                        Biaya ADM
                        {{ 'Rp. ' . ' ' . number_format($data->administrasi_nominal, 0, ',', '.') ?? 'Rp. ' . ' ' . '0' }},
                        <p></p>
                        <p></p>
                    </td>
                </tr>
                {{-- <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr> --}}
            @empty
            @endforelse
        </table>


        <div class="page-break"></div>

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <div class="headers">
            <label for="" style="font-weight:bold;">1. &nbsp;&nbsp;&nbsp;PLAFON RSC</label>
            <div class="item">
                <table class="plafon_rsc">
                    <tr>
                        <td style="width: 55%;">Baki Debet</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->baki_debet, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Tunggakan Bunga</td>
                        <td style="width: 2%;">:</td>
                        <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                            {{ number_format($data->tunggakan_bunga, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%; font-weight:bold; text-align:center;">TOTAL</td>
                        <td style="width: 2%; font-weight:bold;">:</td>
                        @php
                            $total_baki = $data->tunggakan_bunga + $data->baki_debet;
                        @endphp
                        <td style="font-weight:bold;"> &nbsp;Rp.
                            {{ number_format($total_baki, '0', ',', '.') }}</td>
                    </tr>
                </table>

                <table class="plafon_rsc">
                    <tr>
                        <td style="width: 55%;">Bunga Dibayar</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->bunga_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Pokok Dibayar</td>
                        <td style="width: 2%;">:</td>
                        <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                            {{ number_format($data->poko_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%; font-weight:bold; text-align:center;">PLAFON</td>
                        <td style="width: 2%; font-weight:bold;">:</td>
                        <td style="font-weight:bold;"> &nbsp;Rp.
                            {{ number_format($data->penentuan_plafon, '0', ',', '.') }}</td>
                    </tr>
                </table>

            </div>
        </div>

        <div class="headers">
            <label for="" style="font-weight:bold;">2. &nbsp;&nbsp;&nbsp;BIAYA RSC</label>
            <div class="item">
                <table class="plafon_rsc">
                    <tr>
                        <td style="width: 55%;">Administrasi {{ $data->administrasi }}%</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->administrasi_nominal, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Asuransi Jiwa</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->asuransi_jiwa, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Asuransi TLO</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->asuransi_tlo, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Pokok Dibayar</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->poko_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Bunga Dibayar</td>
                        <td style="width: 2%;">:</td>
                        <td> &nbsp;Rp. {{ number_format($data->bunga_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%;">Denda</td>
                        <td style="width: 2%;">:</td>
                        <td style="border-bottom: 1px solid black;"> &nbsp;Rp.
                            {{ number_format($data->denda_dibayar, '0', ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 55%; font-weight:bold; text-align:center;">TOTAL BIAYA</td>
                        <td style="width: 2%; font-weight:bold;">:</td>
                        <td style="font-weight:bold;"> &nbsp;Rp. {{ number_format($data->total, '0', ',', '.') }}</td>
                    </tr>
                </table>

                <p>SIMULASI ANGSURAN PLAFON Rp. {{ number_format($data->penentuan_plafon, '0', ',', '.') }},- RATE
                    {{ $data->suku_bunga }} %
                    {{ $data->metode_rps }} JANGKA WAKTU {{ $data->jangka_waktu }} BULAN</p>
                <p>
                    Total Angsuran = Pokok (Rp. {{ number_format($data->angsuran_pokok, '0', ',', '.') }}) + Bunga (Rp.
                    {{ number_format($data->angsuran_bunga, '0', ',', '.') }}) = Rp.
                    {{ number_format($data->angsuran_pokok + $data->angsuran_bunga, '0', ',', '.') }}
                </p>

                <table style="width:97%; margin-right:50px; margin-top: 35px;">
                    <tr>
                        <td class="text-left" style="float: left;">

                        </td>
                        <td class="text-right" style="float: right;">
                            <center>
                                <p style="margin-top: -1.5px;"></p>
                                <p>Pamanukan, {{ $data->tgl_usulan }}</p>
                                <p style="margin-top: 40px;"></p>
                                <br>
                                <br>
                                <b>
                                    <p>
                                        <font style="text-transform: uppercase;"><u>{{ $data->nama_nasabah }}</u>
                                        </font>
                                    </p>
                                </b>
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
