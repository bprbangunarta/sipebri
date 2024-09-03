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
            margin: 0;
            orientation: landscape;
        }

        body {
            display: flex;
            justify-content: space-around;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
        }

        .container {
            border: 1px solid black;
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 10px;
        }

        .box {
            width: 6.35cm;
            height: 9.67cm;
            border: 1px solid black;
            box-sizing: border-box;
        }

        @media print {
            @page {
                size: landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="box"></div>
        <div class="box"></div>
        <div class="box"></div>
        <div class="box"></div>
        <div class="box"></div>
        <div class="box"></div>
    </div>
    <script>
        window.print()
    </script>
</body>

</html>
