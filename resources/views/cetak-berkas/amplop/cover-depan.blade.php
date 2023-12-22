<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cover Amplop</title>
    <style>
        @page {
            size: A4;
            margin-top: 0cm;
            margin-bottom: 1.0cm;
            margin-left: 0cm;
            margin-right: 0cm;
            size: landscape;
        }

        body {
            width: 275mm;
            height: 180mm;
            margin: 0;
            font-family: "Times New Roman", Times, serif;
            border: 1px solid brown;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* border: 1px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th,
        td {
            padding: 1px;
            text-align: left;
            /* Menambahkan border pada sisi kanan sel */
        }

        .br-1 {
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
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
            text-align: justify;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .content {
                padding-top: 0cm;
                padding-bottom: 1.5cm;
                padding-left: 1cm;
                padding-right: 1cm;
            }
        }
    </style>
</head>

<body>
    <div class="content" style="margin-top: 30px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <table>
            <tr>
                <td width="14%">Nomor</td>
                <td class="text-center" width="1%"> : </td>
                <td>0973/03/KABAG.ANALIS/PBA/XII/2023	</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td class="text-center" width="1%"> : </td>
                <td>1 (satu) Bundle</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td class="text-center" width="1%"> : </td>
                <td><b><u>Pemberitahuan Permohonan Kredit</u></b></td>
            </tr>
        </table>

        <p style="margin-top: 250px;"></p>
        <table>
            <tr>
                <td width="65%"></td>
                <td width="35%">
                    Kepata Yth. <br>
                    WARMAH RIO <br>
                    di <br>
                    DUSUN CIPACAR RT/RW 07/02 PADAMULYA CIPUNAGARA SUBANG <br>
                    082320099971
                </td>
            </tr>
        </table>

        <p style="margin-top: 40px;"></p>
        <table>
            <tr>
                <td width="14%">Kantor Pusat</td>
                <td class="text-center" width="1%"> : </td>
                <td> Jl. H. Iksan No. 89 Pamanukan 41254 Telp. (0260) 550500 – 550888</td>
            </tr>

            <tr>
                <td width="14%">Kantor Kas</td>
                <td class="text-center" width="1%"> : </td>
                <td>Jl. Otto Iskandardinata No. 73 Telp. (0260) 412449</td>
            </tr>
            <tr>
                <td width="14%"></td>
                <td class="text-center" width="1%"></td>
                <td>Jl. Raya Jalancagak No. 58 Telp. (0260) 472660</td>
            </tr>
            <tr>
                <td width="14%"></td>
                <td class="text-center" width="1%"></td>
                <td>Jl. Raya Kamarung, Pagaden Telp. (0260) 450400</td>
            </tr>
            <tr>
                <td width="14%"></td>
                <td class="text-center" width="1%"></td>
                <td>Jl. Kalijati-Purwadadi No. 9 Telp. (0260) 4641126</td>
            </tr>
            <tr>
                <td width="14%"></td>
                <td class="text-center" width="1%"></td>
                <td>Jl. Ahmad Yani No. 7, Ciasem Telp. (0260) 523170</td>
            </tr>
            <tr>
                <td width="14%"></td>
                <td class="text-center" width="1%"></td>
                <td>Jl. Raya Pantura Pusakanagara No. 13 Telp. (0260) 550500</td>
            </tr>
        </table>

    </div>


    <script>
        window.print();
    </script>
</body>

</html>