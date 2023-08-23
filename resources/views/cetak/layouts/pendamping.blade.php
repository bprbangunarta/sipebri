<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Persetujuan Pendamping</title>
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

        input,
        textarea {
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
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;">SURAT PERSETUJUAN PENDAMPING</h4>

        <P>Yang bertanda tangan dibawah ini :</P>

        <table>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 23%;">No. KTP</td>
                <td class="text-center"> : </td>
                <td>3213070701980004</td>

            </tr>
            <tr>
                <td></td>
                <td>Nama Pendamping</td>
                <td class="text-center"> : </td>
                <td>PUADAH</td>
            </tr>
            <tr>
                <td></td>
                <td>Tempat, Tanggal Lahir</td>
                <td class="text-center"> : </td>
                <td>Subang, 7 Januari 1985</td>
            </tr>
            <tr>
                <td></td>
                <td>Pekerjaan</td>
                <td class="text-center"> : </td>
                <td>Guru SD</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td class="text-center"> : </td>
                <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN KABUPATEN SUBANG, PROVINSI JAWA BARAT, INDONESIA
                </td>
            </tr>
        </table>

        <p>
            Adalah merupakan (Suami/ Istri/ Orang Tua/ Kakak/ Adik/ Anak) yang selanjutnya disebut sebagai Pendamping
            dari Debitur :
        </p>

        <table>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 23%;">No. KTP</td>
                <td class="text-center"> : </td>
                <td>3213070701980004</td>

            </tr>
            <tr>
                <td></td>
                <td>Nama Pendamping</td>
                <td class="text-center"> : </td>
                <td>ZULFADLI RIZAL</td>
            </tr>
            <tr>
                <td></td>
                <td>Tempat, Tanggal Lahir</td>
                <td class="text-center"> : </td>
                <td>Subang, 7 Januari 1998</td>
            </tr>
            <tr>
                <td></td>
                <td>Pekerjaan</td>
                <td class="text-center"> : </td>
                <td>Karyawan Swasta</td>
            </tr>
            <tr>
                <td></td>
                <td style="position: absolute;">Alamat</td>
                <td class="text-center"> : </td>
                <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN KABUPATEN SUBANG, PROVINSI JAWA BARAT, INDONESIA
                </td>
            </tr>
        </table>

        <p>
            Dengan ini mengetahui dan memberi persetujuan penuh kepada (Suami/ Istri/ Orang Tua/ Kakak/ Adik/ Anak) Saya
            tersebut untuk membuat dan menandatangani perjanjian kredit dengan PT. BPR Bangunarta dan menandatangani
            surat-surat yang diperlukan berkenaan dengan hal tersebut.
        </p>

        <p>
            Saya telah mengetahui terkait syarat dan ketentuan premi asuransi jiwa yang didaftarkan oleh (Suami/ Istri/
            Orang Tua/ Kakak/ Adik/ Anak) Saya dan apabila dikemudian hari (Suami/ Istri/ Orang Tua/ Kakak/ Adik/ Anak)
            Saya meninggal dunia sebelum 45 (Empat Puluh Lima) Hari setelah kredit direalisasikan atau setelah lewat
            jatuh tempo pinjaman maka seluruh kewajiban menjadi tanggungjawab ahli waris.
        </p>

        <p>
            Apabila dikemudian hari (Suami/ Istri/ Orang Tua/ Kakak/ Adik/ Anak) Saya melakukan tindakan yang merugikan
            PT. BPR Bangunarta dalam hal pembayaran angsuran, Saya bertanggungjawab untuk membayar angsuran tersebut
            sampai dinyatakan lunas oleh PT. BPR Bangunarta.
        </p>

        <table>
            <tr>
                <td style="width: 15%;">
                    Subang, Jumat 29 Agustus 2023<br>
                    Yang Memberi Persetujuan,<br><br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;">
                        ZULFADLI RIZAL
                    </font>
                </td>
            </tr>
        </table>
    </div>

    {{-- <script>
        window.print();
    </script> --}}
</body>

</html>