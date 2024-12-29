<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .qr-container {
            text-align: center;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .qr-container img {
            width: 120px;
        }

        .qr-container b {
            display: block;
            margin-top: 10px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="qr-container" id="qrContainer">
        <img width="80" id="qrCodeImage" src="data:image/png;base64,{{ $qr }}" alt="QR Code">
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        window.onload = function() {
            const qrContainer = document.getElementById('qrContainer');

            html2canvas(qrContainer, {
                scale: 5,
                logging: false,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#fff',
            }).then(function(canvas) {

                const imgData = canvas.toDataURL('image/png');

                const downloadLink = document.createElement('a');
                downloadLink.href = imgData;
                downloadLink.download = 'qrcode_tracking.png';

                downloadLink.click();
            });
        }
    </script>
</body>

</html>
