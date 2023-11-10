<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BA Pemeriksaan Motor</title>
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
            margin-top: -10px;
            text-align: justify;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .content {
                padding: 1.5cm;
            }
        }
    </style>
</head>

<body>
    <div class="content" style="margin-top: -10px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">BERITA ACARA PEMERIKSAAN KENDARAAN BERMOTOR</h4>

        <table>
            Pada hari ini ............. tanggal, ........................... telah dilakukan pemeriksaan kondisi
            jaminan oleh PT BPR Bangunarta dengan Debitur :
            <tr>
                <td style="width: 5%;"></td>
                <td>Nama</td>
                <td class="text-center"> : </td>
                <td>{{ $data->nama_user }}</td>

            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width:5%;">Jabatan</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->role_name }}</td>
            </tr>
        </table>

        <table>
            Yang akan dijadikan agunan oleh :
            <tr>
                <td style="width: 5%;"></td>
                <td>Nama</td>
                <td class="text-center"> : </td>
                <td>{{ $data->nama_nasabah }}</td>

            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width:5%;">Alamat</td>
                <td class="text-center" style="width: 3%;"> : </td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
        </table>

        <table style="border: 1px solid #000000;border-bottom:none;">
            Dengan kondisi jaminan sebagai berikut :
            <tr>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>Merk/Type</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>No. Rangka</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>Warna</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>No. BPKB</b></td>
            </tr>
            <tr>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>No. Polisi</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>No. Mesin</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>Tahun</b></td>
                <td class="text-center br-1" colspan="2" style="width: 25%"><b>Atas Nama</b></td>
            </tr>
            <tr>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
                <td class="br-1" colspan="2" style="width: 25%">&nbsp;</td>
            </tr>
        </table>
        <table style="border: 1px solid #000000;border-top:none;">
            <tr>
                <td class="text-center br-1" rowspan="2" style="width: 25%"><b>Perlengkapan</b></td>
                <td class="text-center br-1" colspan="3" style="width: 25%"><b>Check List Petugas</b></td>
                <td class="text-center br-1" rowspan="2" style="width: 25%"><b>Perlengkapan</b></td>
                <td class="text-center br-1" colspan="3" style="width: 25%"><b>Check List Petugas</b></td>
            </tr>
            <tr>
                <td class="text-center br-1" style="width: 8%"><b>Ada</b></td>
                <td class="text-center br-1" style="width: 9%"><b>TA</b></td>
                <td class="text-center br-1" style="width: 8.1%"><b>KET</b></td>
                <td class="text-center br-1" style="width: 8%"><b>Ada</b></td>
                <td class="text-center br-1" style="width: 9%"><b>TA</b></td>
                <td class="text-center br-1" style="width: 8%"><b>KET</b></td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; STNK</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Tutup Oli Samping</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Slebor Depan</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Tutup Oli</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Velg Depan</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Filter Udara</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Kepala Babi</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Tool Kit</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Kabel Rem</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Mesin</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Shock Depan</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; CDI</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Lampu Depan</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Karburator</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Stang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Accu</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Holder Ka/Ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Kabel Busi</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Spion Ka/ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Rantai</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Riting Depan Ka/Ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Tutup Rantai</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Kunci Kontak</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Foot Step Depan Ka/Ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Rem Depan</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Foot Step Belakang Ka/Ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Rem Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Kick Starter</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Cakram (rem)</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Pijakan Rem</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Panel Instrument</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Pijakan Perseneling</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Klakson</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Standar</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Jok</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Gear Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Body</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Slebor Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Strip Body</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Velg Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Front Guard Sayap Body</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Shock Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Swing Arm</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Lampu Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Tangki Bensin</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Riting Belakang Ka/Ki</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Tutup Tangki Bensin</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Pegangan Belakang</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
            <tr>
                <td class="br-1" style="width: 25%">&nbsp; Tangki Oli Samping</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 25%">&nbsp; Knalpot</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
                <td class="br-1" style="width: 8%">&nbsp;</td>
            </tr>
        </table>

        <font>
            Demikian Berita Acara Pemeriksaan ini agar dapat digunakan sesuai dengan kepentingannya.
        </font><br>

        <table>
            <font style="float: right;">Pamanukan, ......................... {{ $data->thn }}</font>
            <tr>
                <td class="text-center">
                    Calon Debitur
                    ,<br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                        {{ $data->nama_nasabah }}</font>
                </td>
                <td class="text-center" style="width: 48%">
                    <div style="border: 1px solid black;padding:5px;">
                        <div style="border-bottom: 1px solid black;"><b>NILAI TAKSASI JAMINAN</b><br></div>
                        <div>Rp. ...........................................</div>
                    </div>
                </td>
                <td class="text-center">
                    Petugas Survei,<br><br><br><br>

                    <font style="font-weight: bold;text-decoration: underline;text-transform:uppercase;">
                        {{ $data->nama_user }}</font>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
