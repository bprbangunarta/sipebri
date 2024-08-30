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

        .content {
            border: 1px solid black;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-top: 1cm;
            margin-bottom: 1cm;
        }

        .item {
            border: 1px solid black;
            height: 9.67cm;
            width: 6.35cm;
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
    <div class="content">
        <table>
            <tr>
                <td>
                    <div class="item">

                    </div>
                </td>
                <td>
                    <div class="item">

                    </div>
                </td>
                <td>
                    <div class="item">

                    </div>
                </td>
                <td>
                    <div class="item">

                    </div>
                </td>
            </tr>
        </table>
    </div>
    <script>
        window.print()
    </script>
</body>

</html>
