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
            position: absolute;
            bottom: 10px;
            left: 10px;
            width: 120px;
            z-index: 1000;
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
                position: static;
                left: 0;
                top: 0;
                width: 100%;
                height: 100vh;
            }

        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 2px solid 034871;">
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
        <br>
        <div class="row">
            <label for=""><b>PETA LOKASI RUMAH</b></label>
            <div id="map" style="border:2px solid black; height: 400px; margin-top:5px;">

                <img style="width: 80px; position: absolute; bottom: 10px; left: 10px;"
                    src="data:image/png;base64,{{ $qr_lokasi_rumah }}">

            </div>
            {{-- <div class="col-sm-5 mt-3 img">
                <div class="card">
                    @if (!is_null($data->latitude) || !is_null($data->longitude))
                        <img class="card-img-top" src="{{ asset('storage/image/qr_code/' . $qr_lokasi_rumah) }}">
                    @else
                        <img class="card-img-top" src="{{ asset('assets/img/lokasi.png') }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title"><b>LOKASI RUMAH</b></h5>
                        {{ $data->alamat_ktp }}
                    </div>
                </div>
            </div> --}}
            {{-- @forelse ($lokasi_usaha as $item)
                <div class="col-sm-5 mt-3">
                    <div class="card">
                        @if ($data->alamat_ktp === $item->lokasi_usaha)
                            @if (!is_null($data->latitude) || !is_null($data->longitude))
                                <img class="card-img-top"
                                    src="{{ asset('storage/image/qr_code/' . $qr_lokasi_rumah) }}">
                            @else
                                <img class="card-img-top" src="{{ asset('assets/img/lokasi.png') }}">
                            @endif
                        @else
                            <img class="card-img-top" src="{{ asset('assets/img/lokasi.png') }}">
                        @endif
                        <div class="card-body">
                            <h6 class="card-title"><b>{{ $item->nama_usaha }}</b></h6>
                            {{ $data->alamat_ktp }}
                        </div>
                    </div>
                </div>
            @empty
            @endforelse --}}
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var lat = {{ $data->latitude }};
        var lng = {{ $data->longitude }};

        var map = L.map('map').setView([lat, lng], 15);

        var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            maxZoom: 17,
        }).addTo(map);

        L.marker([lat, lng]).addTo(map);

        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
</body>

</html>
