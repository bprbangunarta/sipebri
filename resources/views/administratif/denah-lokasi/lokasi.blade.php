<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <title>Cetak Denah Lokasi</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1px;
            text-align: left;
        }

        th:last-child,
        td:last-child {
            border-right: none;
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
            margin-top: -10px;
            text-align: justify;
        }

        #map img {
            display: none;
        }


        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.5cm;
            }

            hr {
                border: 0.8px solid black;
            }

            #map,
            #map * {
                visibility: visible;
            }

            #map {
                position: relative;
                left: 50%;
                transform: translateX(-50%);
                width: 100%;
                height: 400px;
            }

            .qr-background {
                display: flex;
                background-color: white;
                width: 100px;
                height: 100px;
                position: absolute;
                bottom: 10px;
                left: 10px;
                border-radius: 3px;
                justify-content: center;
                align-items: center;
            }

            #map img {
                display: block;
                position: absolute;
                bottom: 10px;
                left: 10px;
                /* Ubah sesuai kebutuhan */
                width: 80px;
                z-index: 2000;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 2px solid #034871;">
        <h4 style="text-align: center;">DENAH LOKASI CALON DEBITUR</h4>

        <table style="font-size: 13px;">
            <tr>
                <td><input type="hidden" value="{{ $data->latitude }}" id="latitude"></td>
                <td style="width: 20%;">Nama Calon Debitur</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td><input type="hidden" value="{{ $data->longitude }}" id="longitude"></td>
                <td style="width: 14%;">Alamat</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="width: 14%;">Petugas</td>
                <td style="width: 2%;">:</td>
                <td>{{ $data->nama_user }}</td>
            </tr>
        </table>
        <br><br>
        <div class="row">
            <label for=""><b>PETA LOKASI RUMAH</b></label>
            <div id="map" style="border:2px solid black; height: 350px; margin-top:5px;">
                <div class="qr-background">
                    <img style="width: 80px;" src="data:image/png;base64,{{ $qr_lokasi_rumah }}">
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <label for=""><b>DENAH LOKASI USAHA</b></label>
            <div id="map" style="border:2px solid black; height: 350px; margin-top:5px;">

            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var lat = {{ $data->latitude }};
        var lng = {{ $data->longitude }};

        // URL untuk peta
        var url = 'https://www.google.com/maps?q=' + lat + ',' + lng + '&z=18&t=k&output=embed';

        // Membuat elemen iframe
        var iframe = document.createElement('iframe');
        iframe.width = "100%";
        iframe.height = "100%";
        iframe.src = url;
        iframe.frameBorder = "0";
        iframe.style.border = "0";
        iframe.allowFullscreen = true;

        document.getElementById('map').appendChild(iframe);

        iframe.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
