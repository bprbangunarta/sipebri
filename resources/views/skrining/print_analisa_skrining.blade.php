<!DOCTYPE html>
<html lang="en">

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
            text-align: center;
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
        <center>
            <h3><b>HASIL ANALISA SCREENING KEPATUHAN <br> BPR BANGUNARTA </b></h3>
        </center>

        <div class="data">
            <table style="margin-left: -2px;">
                <tr>
                    <td style="width: 50%;">NIK</td>
                    <td style="width: 5%;">:</td>
                    <td>3674073001880003</td>
                </tr>
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td>AGUNG GUNARDI</td>
                </tr>
            </table>

            <p>
                Dengan ini menyatakan bahwa calon nasabah tersebut tidak terdaftar.
            </p>

            <table style="width:97%; margin-right:50px; margin-top: 35px;">
                <tr>
                    <td class="text-left" style="float: left;">

                    </td>
                    <td class="text-right" style="float: right;">
                        <center>
                            <p style="margin-top: -1.5px;"></p>
                            {{-- <p>Pamanukan, {{ $data->tgl_usulan }}</p> --}}
                            <p>Pamanukan, 15 September 2024</p>
                            <p style="margin-top: 40px;"></p>
                            <br>
                            <br>
                            <b>
                                <p>
                                    <font style="text-transform: uppercase;"><u>YANDI ROSYANDI</u>
                                    </font>
                                </p>
                            </b>
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
