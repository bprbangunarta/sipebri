<!DOCTYPE html>
<html lang="en">
@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Skrining</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.5cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        body {
            font-family: 'Calibri', 'Trebuchet MS', sans-serif;
        }

        .content .data {
            margin-left: 2cm;
            margin-right: 2cm;
        }

        .data_table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        .data_table tr,
        .data_table td,
        .data_table th {
            border: 1px solid black;
            padding: 7px;
        }

        @media print {
            .header {
                width: 80%;
                margin-top: 0cm;
                margin-left: 2cm;
            }

            hr {
                position: relative;
                z-index: 5;
                border: 1px solid #161717;
                border-style: double;
                width: 100%;
                margin-left: 0cm;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr>
    </div>
    <div class="content">
        <div class="data">
            <br>
            <table style="margin-left: -2px;">
                <tr>
                    <td style="width: 50%;">NIK</td>
                    <td style="width: 5%;">:</td>
                    <td>{{ $data->nik }}</td>
                </tr>
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td>{{ $data->nama }}</td>
                </tr>
            </table>
            <table class="data_table">
                <thead>
                    <tr>
                        <th style="width: 3%; text-align:center;">NO</th>
                        <th style="width: 40%; text-align:center;">NAMA</th>
                        <th style="text-align:center;">KETERANGAN</th>
                    </tr>
                </thead>
                <br>
                <tbody>
                    <tr>
                        <td style="text-align:center;">1</td>
                        <td style="padding-left:45px;">DTTOT</td>
                        <td style="text-align:center;">
                            {{ $data->dttot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">2</td>
                        <td style="padding-left:45px;">DPPSPM</td>
                        <td style="text-align:center;">
                            {{ $data->dppspm }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">3</td>
                        <td style="padding-left:45px;">JUDI ONLINE</td>
                        <td style="text-align:center;">
                            {{ $data->judi }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">4</td>
                        <td style="padding-left:45px;">PEP</td>
                        <td style="text-align:center;">
                            {{ $data->pep }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">5</td>
                        <td style="padding-left:45px;">NEGATIVE NEWS</td>
                        <td style="text-align:center;">
                            {{ $data->negative }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">6</td>
                        <td style="padding-left:45px;">WATCH LIST</td>
                        <td style="text-align:center;">
                            {{ $data->watch }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <br>
    <br>
    <br>
    <br>

    <div class="header">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr>
    </div>

    <div class="content">
        <center>
            <h3><b>HASIL ANALISA SCREENING KEPATUHAN <br> BPR BANGUNARTA </b></h3>
        </center>

        <div class="data">
            <table style="margin-left: -2px;">
                <tr>
                    <td style="width: 50%;">NIK</td>
                    <td style="width: 5%;">:</td>
                    <td>{{ $data->nik }}</td>
                </tr>
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td>{{ $data->nama }}</td>
                </tr>
            </table>

            <p>
                {{ $data->catatan }}
            </p>

            <table style="width:97%; margin-right:50px; margin-top: 15px;">
                <tr>
                    <td colspan="3">
                        <center>
                            <p>Pamanukan, {{ $tgl }}</p>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td class="text-left" style="float: left;">
                        <center>
                            <p style="margin-top: -1.5px;"></p>
                            <p>Menyetujui, </p>
                            <p style="margin-top: 40px;"></p>

                            @if (!empty($qr_kabag))
                                <img style="width: 80px;" src="data:image/png;base64,{{ $qr_kabag }}"
                                    alt="QR Code">
                            @else
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                            @endif

                            <p>
                                @if (!empty($data->kabag))
                                    <font style="text-transform: uppercase;">{{ $data->kabag }}
                                    </font>
                                    <hr style="width: 100%; border:1px solid black; margin-top: -10px;">
                                    Kabag Kepatuhan
                                @else
                                    <font style="text-transform: uppercase;">{{ $data->kabag }}
                                    </font>
                                    <hr style="width: 100%; border:1px solid black; margin-top: 40px;">
                                    Kabag Kepatuhan
                                @endif
                            </p>

                        </center>
                    </td>
                    <td class="text-right" style="float: right;">
                        <center>
                            <p style="margin-top: -1.5px;"></p>
                            <p>Petugas Analisa,</p>
                            <p style="margin-top: 40px;"></p>

                            @if (!empty($qr_staff))
                                <img style="width: 80px;" src="data:image/png;base64,{{ $qr_staff }}"
                                    alt="QR Code">
                            @else
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                            @endif

                            <p>
                                <font style="text-transform: uppercase;">{{ $data->staff }}
                                </font>
                            </p>
                            <p>
                                <hr style="width: 100%; border:1px solid black; margin-top: -10px;">
                                Staff Kepatuhan
                            </p>


                        </center>
                    </td>
                </tr>
            </table>

        </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
