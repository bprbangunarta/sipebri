<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penolakan Kredit</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
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
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }
        }
    </style>
</head>

<body>
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        {{-- <h4 style="text-align: center;font-size: 12pt;">SURAT KUASA PENDAFTARAN PENJAMINAN FIDUSIA</h4> --}}

        <table>
            <tr>
                <td width="14%">Nomor</td>
                <td class="text-center" width="1%"> : </td>
                <td>0971/03/KABAG.ANALIS/PBA/XII/2023</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-center" width="1%"> : </td>
                <td>01 Desember 2023</td>
            </tr>
            <tr>
                <td>Kode Pengajuan</td>
                <td class="text-center" width="1%"> : </td>
                <td>00339931</td>
            </tr>
        </table>

        <p></p>

        Kepada Yth. <br>
        Bapak/Ibu IMAS HARYATI <br>
        Di <br>
        DUSUN RAJAPOLAH RT/RW 01/04 CIASEM BARU CIASEM SUBANG	 No. Telp/HP : 082320099971

        <p></p>

        <table>
            <tr>
                <td width="14%">Perihal</td>
                <td class="text-center" width="1%"> : </td>
                <td><b><u>Pemberitahuan Permohonan Kredit</u></b></td>
            </tr>
        </table>

        <p></p>

        Dengan Hormar, 

        <p style="text-align: justify;">
            Menindaklanjuti permohonan kredit yang Bapak/Ibu ajukan kepada kami (PT. BPR Bangunarta), maka dengan ini kami sampaikan bahwa berdasarkan Keputusan Komite Kredit tanggal 03 Desember 2023, permohonan kredit Bapak/Ibu untuk saat ini belum bisa kami kabulkan karena hasil analisa kurang memenuhi standar di Bank kami, semoga Bapak/Ibu berlapang dada menerima keputusan kami ini.
        </p>

        <p style="text-align: justify;">
            Demikian surat ini kami sampaikan sebagai pemberitahuan. Atas kepercayaan dan pengertian Bapak/Ibu, kami ucapkan terimakasih.
        </p>

        <p style="margin-top:30px;"></p>

        <table>
            <tr>
                <td class="text-center" width="40%">
                    Hormat Kami,<br>
                    <b>PT. BPR BANGUNARTA</b>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" width="40%">
                    <center>
                        <p style="margin-top:80px;"></p>
                        {{-- <img src="{{ asset('storage/image/qr_code/' . $qr) }}" width="100" height="100"
                            style="margin-top:-30px;"> --}}
                    </center>
                    <u>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Komite Kredit
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </u>
                    <br>
                    <font style="text-transform: uppercase;">SOPYAN ASROR</font>
                </td>
                <td></td>
                <td width="40%"></td>
            </tr>
        </table>

        <p style="margin-top:400px;"></p>

        Catatan : <br>
        <ul style="margin-top:-1px;margin-left:-25px;">
            <li>Surat ini merupakan surat resmi PT. BPR Bangunarta.</li>
            <li>Untuk informasi lengkap silahkan hubungi Call Center Kami (0260) 550888</li>
        </ul>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
