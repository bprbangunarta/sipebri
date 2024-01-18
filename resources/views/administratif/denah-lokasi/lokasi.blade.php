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

        .content {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            margin-top: -10px;
            text-align: justify;
        }

        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.5cm;
            }
        }

        .penghasilan {
            margin-left: 40px;
            margin-top: -14px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
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
        <br>
        <div id="map" style="height: 200px;"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        window.print();

        var lat = document.getElementById('latitude').value;
        var long = document.getElementById('longitude').value;

        var initialLocation = {
            lat: lat,
            lng: long
        };

        var map = L.map('map').setView([lat, long], 20);
        var redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            tooltipAnchor: [16, -28],
            shadowSize: [41, 41]
        });

        L.marker(initialLocation, {
            icon: redIcon
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        // var qrCode = new QRCode(document.getElementById("qrcode"), {
        //     width: 128,
        //     height: 128
        // });
        var mapLink = window.location.href.split('?')[0] + `?lat=${initialLocation.lat}&lng=${initialLocation.lng}&zoom=16`;
        console.log(mapLink)
        // qrCode.makeCode(mapLink);
    </script>
</body>

</html>
