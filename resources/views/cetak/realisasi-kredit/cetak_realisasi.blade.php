<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Photo Realisasi</title>
    <style>
        @page {
            size: A4;
            margin: 0.51cm 0.31cm;
            orientation: landscape;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 10px;
            padding: 10px;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }

        .box {
            width: 6.35cm;
            height: 9.67cm;
            border: 1px solid black;
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            page-break-inside: avoid;
        }

        .img {
            top: 0.4cm;
            height: 8.60cm;
            width: 4.84cm;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border: 1px solid black;
        }

        .img img {
            width: 100%;
            height: 100%;
            object-fit: fill;
            object-position: center;
        }

        .footer {
            display: flex;
            position: absolute;
            background: white;
            bottom: 0;
            padding-left: 5px;
            padding-right: 5px;
            width: 100%;
            height: 1.3cm;
            border-top: 1px solid black;
            box-sizing: border-box;
            justify-content: center;
            align-items: center;
            text-align: center;
            line-height: 0.5cm;
            overflow: hidden;
        }

        @media print {
            @page {
                size: landscape;
                margin: 0.51cm 0.31cm;
                font-family: Arial, Helvetica, sans-serif;
            }

            .footer {
                display: flex;
                font-size: 12pt;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @forelse ($data as $value)
            <div class="box">
                @if (!empty($value->foto_pemohon))
                    <div class="img">
                        <img src="{{ asset('storage/image/photo_realisasi/' . $value->foto_pemohon) }}" alt="">
                    </div>
                @else
                    <div class="img">
                        <img src="{{ asset('assets/img/default.png') }}" alt="">
                    </div>
                @endif
                <div class="footer">{{ $value->nama_nasabah }} <br> {{ $value->no_spk }}</div>
            </div>

            <div class="box">
                @if (!empty($value->foto_pemohon))
                    <div class="img">
                        <img src="{{ asset('storage/image/photo_realisasi/' . $value->foto_pendamping) }}"
                            alt="">
                    </div>
                @else
                    <div class="img">
                        <img src="{{ asset('assets/img/default.png') }}" alt="">
                    </div>
                @endif
                <div class="footer">{{ $value->nama_pendamping }}</div>
            </div>
        @empty
            <h2 style="display: flex; justify-content:center; align-items:center; margin-left:50%;">Data Tidak Ditemukan
            </h2>
        @endforelse
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
