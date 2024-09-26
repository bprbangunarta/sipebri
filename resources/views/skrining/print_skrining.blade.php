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
            padding: 3px;
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
            <h3><b>SCREENING CALON NASABAH BPR BANGUNARTA </b></h3>
        </center>

        <div class="data">
            <p>Menyatakan dengan ini bahwa :</p>
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
                        <th style="width: 3%;">NO</th>
                        <th>NAMA</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>
                <br>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td style="text-align: left; padding-left:20px;">DTTOT</td>
                        <td>{{ strtoupper($data->dttot) }}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td style="text-align: left; padding-left:20px;">PPSPM</td>
                        <td>{{ strtoupper($data->dppspm) }}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td style="text-align: left; padding-left:20px;">JUDI ONLINE</td>
                        <td>{{ strtoupper($data->judi_online) }}</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td style="text-align: left; padding-left:20px;">PEP</td>
                        <td>{{ strtoupper($data->pep) }}</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td style="text-align: left; padding-left:20px;">NEGATIVE NEWS</td>
                        <td>{{ strtoupper($data->berita_negatif) }}</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td style="text-align: left; padding-left:20px;">WATCH LIST</td>
                        <td>{{ strtoupper($data->watch_list) }}</td>
                    </tr>
                </tbody>
            </table>

            <table style="margin-left: -2px; margin-top: 20px;">
                <tr>
                    <td style="width: 50%;">Petugas Pemeriksa</td>
                    <td style="width: 5%;">:</td>
                    <td>{{ $data->pemeriksa }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $data->tgl }}</td>
                </tr>
            </table>

        </div>

    </div>


    <div class="header" style="margin-top: 50px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr>
    </div>

    <div class="content">
        <center>
            <h3><b>SCREENING CALON NASABAH BPR BANGUNARTA </b></h3>
        </center>

        <div class="data">
            <p>Menyatakan dengan ini bahwa :</p>
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
                        <th style="width: 3%;">NO</th>
                        <th>NAMA</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>
                <br>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td style="text-align: left; padding-left:20px;">DTTOT</td>
                        <td>{{ strtoupper($data->dttot) }}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td style="text-align: left; padding-left:20px;">PPSPM</td>
                        <td>{{ strtoupper($data->dppspm) }}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td style="text-align: left; padding-left:20px;">JUDI ONLINE</td>
                        <td>{{ strtoupper($data->judi_online) }}</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td style="text-align: left; padding-left:20px;">PEP</td>
                        <td>{{ strtoupper($data->pep) }}</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td style="text-align: left; padding-left:20px;">NEGATIVE NEWS</td>
                        <td>{{ strtoupper($data->berita_negatif) }}</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td style="text-align: left; padding-left:20px;">WATCH LIST</td>
                        <td>{{ strtoupper($data->watch_list) }}</td>
                    </tr>
                </tbody>
            </table>

            <table style="margin-left: -2px; margin-top: 20px;">
                <tr>
                    <td style="width: 50%;">Petugas Pemeriksa</td>
                    <td style="width: 5%;">:</td>
                    <td>{{ $data->pemeriksa }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $data->tgl }}</td>
                </tr>
            </table>

        </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
