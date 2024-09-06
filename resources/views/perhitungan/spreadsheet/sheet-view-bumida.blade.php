<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        use Carbon\Carbon;
    @endphp
    <style>
        .content {
            border: 1px solid black;
            font-family: 'Calibri', sans-serif
        }

        .container {
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-left: 0.3cm;
        }

        .containers {
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-top: 15px;
            gap: 20px;
            margin-left: 0.3cm;
            margin-bottom: 0.5cm;
        }

        .name {
            display: flex;
            justify-content: left;
            align-items: center;
            padding-left: 5px;
            border: 1px solid black;
            /* width: 692px; */
            width: 64.5%;
            height: 30px;
        }

        .names {
            display: block;
            justify-content: left;
            align-items: center;
            padding: 7px;
            border: 1px solid black;
            width: 30%;
            height: 180px;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .debitur {
            margin-right: 10px;
        }

        .colon {
            position: relative;
            left: 70px;
        }

        .name-value {
            margin-left: 75px;
        }

        img {
            position: relative;
            width: 25%;
            max-width: 350px;
            height: auto;
            left: 80px;
        }

        .ttd {
            margin-top: 15px;
            float: right;
            width: 40%;
            height: 100px;
        }

        @media print {
            @page {
                size: landscape;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-left: 1cm;
                margin-left: 1cm;
            }

            hr {
                border: 0.2px solid black
            }
        }
    </style>
    <title>Cetak Asuransi Jiwa Kematian</title>
</head>

<body>
    <div class="content">
        <center>
            <h3><b>PT BPR BANGUNARTA <br> SIMULASI PERHITUNGAN PREMI ASURANSI JIWA KREDIT</b></h3>
        </center>
        <div class="container">
            <div class="name">
                <span class="debitur"><b>Nama Debitur</b></span>
                <span class="colon"><b>:</b></span>
                <span class="name-value"><b>{{ $data[5][4] }}</b></span>
            </div>
            <img src="{{ asset('assets/img/pba.png') }}" class="image">
        </div>

        <div class="containers">
            <div class="names">
                <table style="width: 100%;">
                    <tr>
                        <td style="display:flex; width: 80%;">Plafond Kredit</td>
                        <td>:</td>
                        <td>{{ 'Rp ' . $data[8][4] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">J.W (Bulan)</td>
                        <td>:</td>
                        <td>{{ 'Rp ' . $data[8][4] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Sistem Angsuran (RPS)</td>
                        <td>:</td>
                        <td>{{ $data[10][4] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Produk Kredit</td>
                        <td>:</td>
                        <td>{{ $data[11][4] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        @if (is_null($data[14][2]))
                            <td colspan="3" style="border:1px solid black; height: 70px"></td>
                        @else
                            <td colspan="3" style="border:1px solid black; height: 70px; color: red;">
                                &nbsp;&nbsp;<b>{{ $data[14][2] }}</b>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
            <div class="names">
                <table style="width: 100%;">
                    <tr>
                        <td style="display:flex; width: 80%;">Tgl. Lahir</td>
                        <td>:</td>
                        <td>{{ $data[8][8] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Tgl. Realisasi</td>
                        <td>:</td>
                        <td>{{ $data[9][8] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Usia Realisasi</td>
                        <td>:</td>
                        <td>{{ $data[10][8] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Usia Jt Tempo</td>
                        <td>:</td>
                        <td>{{ $data[11][8] }}</td>
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">Jasa Asuransi</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan=""
                            style="border:1px solid black; height: 70px; font-weight:bold; font-size:11pt; max-width:30%; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                            @if (!is_null($data[14][6]))
                                &nbsp;&nbsp;{{ $data[14][6] }}
                            @else
                            @endif
                        </td>
                        <td colspan="2" style="height: 70px; color: red; font-weight:bold; font-size:14pt;">
                            @if ($data[12][8] == 'Ditolak usia <20')
                                &nbsp;&nbsp; {{ $data[12][8] }}
                            @else
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="names">
                <table style="width: 100%;">
                    <tr>
                        <td style="display:flex; width: 80%;"><b>Maks. Pertanggungan</b></td>
                        <td><b>:</b></td>
                        @if (trim($data[8][12]) == 'Err')
                            <td style="color: red; font-weight:bold;">
                                <b>{{ 'Rp. ' . ' ' . $data[8][12] }}</b>
                            </td>
                        @else
                            <td><b>{{ 'Rp. ' . ' ' . $data[8][12] }}</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;"><b>Rate</b></td>
                        <td><b>:</b></td>
                        @if (trim($data[9][12]) == 'Err')
                            <td style="color: red; font-weight:bold;"><b>{{ $data[9][12] }}</b></td>
                        @else
                            <td><b>{{ $data[9][12] }}</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;"><b>Keterangan Medis</b></td>
                        <td><b>:</b></td>
                        @if (trim($data[10][12]) == 'Err')
                            <td style="color: red; font-weight:bold;"><b>{{ $data[10][12] }}</b></td>
                        @else
                            <td><b>{{ $data[10][12] }}</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="display:flex; width: 80%;">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="height: 70px; border: 1px solid black; border-right: 0; font-size:16pt;">
                            &nbsp;&nbsp;<b>Premi</b></td>
                        <td
                            style=" height: 70px; border: 1px solid black; border-right: 0; border-left: 0; font-size:16pt;">
                            <b>:</b>
                        </td>
                        @if ($data[14][12] == ' Err ')
                            <td
                                style=" height: 70px; border: 1px solid black; border-left: 0; color:red; font-size:16pt;">
                                &nbsp;&nbsp; <b>{{ 'Rp. ' . $data[14][12] }}</b>
                            </td>
                        @else
                            <td style=" height: 70px; border: 1px solid black; border-left: 0; font-size:16pt;">
                                &nbsp;&nbsp; <b>{{ 'Rp. ' . $data[14][12] }}</b>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
        <hr style="border: 1px solid black; width:100;">
        </hr>

        <table style="margin-top: 1px; border: none; margin-bottom: 5px;">
            <tr style="font-size: 12px">
                <td style="width: 11%; display: flexbox; text-align: center;">
                    <i><b>Note * :</b></i>
                </td>
                <td style="width: 2%;"><i>*</i></td>
                <td>
                    <i>Asuransi Jiwa Kredit hanya berlaku dari usia <b>Minimal 20</b> s/d <b>Maksimal 65 Tahun</b> pada
                        saat jatuh tempo kredit. Harap sesuaikan sisa masa pemberian kredit
                        apabila usia debitur sudah memasuki <b>62 Tahun</b></i>
                </td>
            </tr>
            <tr style="font-size: 12px">
                <td><i><b></b></i>
                </td>
                <td><i>*</i></td>
                <td><i>Maksimal pertanggungan masing-masing fasilitas asuransi :
                    </i></td>
            </tr>
            <tr style="font-size: 12px">
                <td><i><b></b></i>
                </td>
                <td><i></i></td>
                <td><i>** BUMIDA 1967 Menurun UP 100
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        1 Milyar dengan usia maksimal <b>65 Tahun</b> pada saat
                        jatuh tempo kredit </i></td>
            </tr>
            <tr style="font-size: 12px">
                <td><i><b></b></i>
                </td>
                <td><i>*</i></td>
                <td>
                    <i>
                        Apabila <b>Plafond Kredit > Maks. Pertanggungan</b>, maka klaim dihitung berdasarkan <b>sisa
                            hutang pokok dari nilai Maks. Pertanggungan</b> pada saat meninggal dunia dengan sistem
                        angsuran <b>Menurun</b>, sesuai dengan suku bunga kredit yang berlaku</i>
                </td>
            </tr>
        </table>
    </div>

    <div class="ttd">
        <center>
            Pamanukan, {{ \Carbon\Carbon::parse('31-07-2024')->translatedFormat('d F Y') }}
            <br>
            <table width='100%' style="text-align: center;">
                <tr>
                    <td>Mengetahui, </td>
                    <td>Mengetahui, </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <hr style="border: 0.2px solid black; width:80%;">
                    </td>
                    <td>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <hr style="border: 0.2px solid black; width:80%;">
                    </td>
                </tr>
                <tr>
                    <td>Pendamping/Ahli Waris</td>
                    <td>Debitur/Peserta Asuransi</td>
                </tr>
            </table>
        </center>
    </div>

    <script>
        window.print()
    </script>
</body>

</html>
