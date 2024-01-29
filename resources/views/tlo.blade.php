<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asuransi Jiwa Kredit Simulation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .content {
            width: 100%;
            border: 2px solid black;
        }

        .table1 {

            width: 96%;
            margin-left: 2%;
            margin-right: 2%;
            border-collapse: collapse;
            box-sizing: border-box;
        }

        th,
        td {
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        @media print {
            @page {
                size: A4 landscape;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <center>
                <h4>PT BPR BANGUNARTA <br> SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i>
                </h4>
            </center>

            <table class="table1">
                <thead>
                    <tr>
                        <td
                            style="background:#f2efef; border-top: 1px solid black; border-bottom: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black;">
                            <b>Nama Debitur</b>
                        </td>
                        <td style="border-top: 1px solid black; border-bottom: 1px solid black;"><b>:</b></td>
                        <td
                            style="border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">
                            <b>WAWA WIBAWA</b>
                        </td>
                        <td style="border-top: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);"
                            width="4%"></td>
                        <td rowspan="2" style="float: right;"><img src="{{ asset('assets/img/pba.png') }}"
                                alt="" width="250px">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <p>* Asuransi Jiwa Kredit hanya berlaku dari usia Minimal 20 s/d Maksimal 65 Tahun pada saat jatuh tempo kredit.
        Harap sesuaikan sisa masa pemberian kredit apabila usia debitur sudah memasuki 62 Tahun</p>
    <p>* Maksimal pertanggungan masing-masing fasilitas asuransi : ** BUMIDA 1967 Menurun UP 100 : 1 Milyar dengan usia
        maksimal 65 Tahun pada saat jatuh tempo kredit</p>
    <p>* Apabila Plafond Kredit > Maks. Pertanggungan, maka klaim dihitung berdasarkan sisa hutang pokok dari nilai
        Maks. Pertanggungan pada saat meninggal dunia dengan sistem angsuran Menurun, sesuai dengan suku bunga kredit
        yang berlaku</p>
</body>

</html>
