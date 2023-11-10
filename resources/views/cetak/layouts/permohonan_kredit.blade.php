<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permohonan Kredit</title>
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

        <table>
            <tr>
                <td style="width:200px;">
                    <img src="{{ asset('assets/img/pba.png') }}" style="width: 200px;">
                </td>
                <td style="float:right;">
                    <table>
                        <tr>
                            <td style="width:65px;">Nama CGC</td>
                            <td><center> : </center></td>
                            <td style="text-align: right;">ZULFADLI RIZAL</td>
                        </tr>
                        <tr>
                            <td style="width:65px;">No. Rek</td>
                            <td><center> : </center></td>
                            <td style="text-align: right;">-</td>
                        </tr>
                        <tr>
                            <td style="width:65px;">Surveyor</td>
                            <td><center> : </center></td>
                            <td style="text-align: right;">MUHIDIN SEPTI PRESETIO</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        

     

        <h4 style="text-align: center;font-size: 12pt;margin-top:10px;">FORMULIR PERMOHONAN KREDIT</h4>
        <div style="margin-top:-10px;">
            <font>Dengan ini saya mengajukan Permohonan Kredit :</font><br>
            <font>
                Jenis kredit yang dimohon: <b>KRU</b> Jumlah kredit yang dimohon : <b>Rp. 20.000.000</b> Jangka Waktu <b>36 Bulan</b> Sistem angsuran : <b>Efektif Musiman</b>. Rencana Penggunaan dana untuk : <b>Modal Usaha</b>
            </font>
        </div>

        <h4 style="text-align: center;font-size: 12pt;">DATA PRIBADI</h4>
        <div style="margin-top:-10px;">
            <table>
                <tr>
                    <td>Nama Lengkap</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        ZULFADLI RIZAL
                    </td>
                </tr>

                <tr>
                    <td>Nama Panggilan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        ZULFAME
                    </td>
                </tr>

                <tr>
                    <td>Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        KTP - 3213070701980004
                    </td>
                </tr>

                <tr>
                    <td>Jth. Tempo ID</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>07/01/2099</td>
                </tr>


                <tr>
                    <td>Jenis Kelamin</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        Laki-Laki
                    </td>
                </tr>

                <tr>
                    <td>Tempat, Tgl. Lahir</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>SUBANG, 07/01/1998</td>
                </tr>

                <tr>
                    <td style="vertical-align: text-top;">Alamat KTP</td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td>
                        <b>Dsn. </b> Sukagalih <b>RT/RW</b> 30/08 <b>Desa. </b> Sukamulya <b>Kec. </b> Pagaden <b>Kab. </b> Subang <b>Kode Pos. </b> 41252
                    </td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>082320099971</td>
                </tr>

                <tr>
                    <td>Pekerjaan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        Wiraswasta
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align: text-top;">Pendidikan</td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td>
                        1. SD &nbsp;&nbsp; 2. SLTP &nbsp;&nbsp; 3. SLTA &nbsp;&nbsp; 4. D1 &nbsp;&nbsp; 5. D2 &nbsp;&nbsp; 6. D3 &nbsp;&nbsp; 7. S1 &nbsp;&nbsp; 8. S2 &nbsp;&nbsp; 9. S3
                    </td>
                </tr>

                <tr>
                    <td>Agama</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>Islam</td>
                </tr>

                <tr>
                    <td>Status Perkawinan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>Menikah</td>
                </tr>

                <tr>
                    <td>Jumlah Anak</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>.....&nbsp; Orang</td>
                </tr>

                <tr>
                    <td style="vertical-align: text-top;">Kepemilikan Rumah</td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td>
                        1. Milik Sendiri &nbsp;&nbsp; 2. Sewa/kontrak &nbsp;&nbsp; 3. Kredit &nbsp;&nbsp; 4. Milik orang tua &nbsp;&nbsp; 5. Rumah dinas &nbsp;&nbsp; <br> 6. Lainnya ....................
                    </td>
                </tr>

                <tr>
                    <td>Nama Gadih Ibu Kandung</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>PUADAH</td>
                </tr>
            </table>
        </div>

        <div style="width:49%;float:left;clear: left;argin-bottom:10px;">
            <table>
                <tr>
                    <td style="width:145px;vertical-align: text-top;">
                        Nama Pendamping <br>
                        (istri/suami)
                    </td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td style="vertical-align: text-top;">MUTIA WAHIDA RAHMI</td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>082320099971</td>
                </tr>

                <tr>
                    <td>Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>KTP</td>
                </tr>

                <tr>
                    <td>No. Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>3213070701980004</td>
                </tr>

                <tr>
                    <td>Tempat, Tgl. Lahir</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>SUBANG, 07/01/1998</td>
                </tr>

                <tr>
                    <td>Jth. Tempo ID</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>07/07/2099</td>
                </tr>
            </table>
        </div>

        <div style="width:49%;float:right;clear: right;margin-bottom:10px;">
            <table>
                <tr>
                    <td colspan="3"><i>Untuk keperluan mendesak (keluarga yang tidak serumah)</i></td>
                </tr>
                <tr>
                    <td style="width:120px;">Nama Lengkap</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>YANDI ROSYANDI</td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>082320099971</td>
                </tr>

                <tr>
                    <td>Jenis Kelamin</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>Laki-laki</td>
                </tr>

                <tr>
                    <td style="vertical-align: text-top;">Hubungan Keluarga</td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td>
                        1. Anak &nbsp;&nbsp; 2. Orang Tua &nbsp;&nbsp; 3. Anak Saudara Kandung dari Orang Tua &nbsp;&nbsp; 4. Saudara Kandung &nbsp;&nbsp; 5. Lainnya
                    </td>
                </tr>
            </table>
        </div>

        <h4 style="text-align: center;font-size: 12pt;clear:both;">DATA AGUNAN</h4>
        <div style="width:49%;border:1px solid black;float:left;margin-top:-10px;">
            <table>
                <tr>
                    <th colspan="3" style="border-bottom: 1px solid black;">
                        <center>Bukti Kepemilikan Tanah dan atau Bangunan</center>
                    </th>
                </tr>
                <tr>
                    <td>
                        SHM/AJB Nomor
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Keadaan Tanah
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Luas
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td style="width: 125px;">
                        Letak Objek Desa/Kec
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Atas Nama
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Alamat
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>
            </table>

            <table style="border-top:1px solid black;">
                <tr>
                    <td>
                        SHM/AJB Nomor
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Keadaan Tanah
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Luas
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td style="width: 125px;">
                        Letak Objek Desa/Kec
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Atas Nama
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Alamat
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>......................................................</center></td>
                </tr>
            </table>
        </div>

        <div style="width:49%;border:1px solid black;float:right;margin-top:-10px;">
            <table>
                <tr>
                    <th colspan="3" style="border-bottom: 1px solid black;">
                        <center>Bukti Kepemilikan Kendaraan Bermotor</center>
                    </th>
                </tr>
                <tr>
                    <td>
                        No. Polisi
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Merek/Tahun
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        No. Mesin
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td style="width: 80px;">
                        No. Rangka
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Atas Nama
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Alamat
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>
            </table>

            <table style="border-top: 1px solid black;">
                <tr>
                    <td>
                        No. Polisi
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Merek/Tahun
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        No. Mesin
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td style="width: 80px;">
                        No. Rangka
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Atas Nama
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>

                <tr>
                    <td>
                        Alamat
                    </td>
                    <td style="width:10px;border-left:1px solid black;border-right:1px solid black;"><center> : </center></td>
                    <td><center>.....................................................................</center></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="content">
        <div style="width:49%;float:left;font-size: 7pt;clear:left;margin-top:5px;">
            <font>
                Sehubungan dengan data/informasi serta dokumen-dokumen yang saya berikan tersebut diatas, dengan ini saya selaku pemohon kredit menyatakan sebagai berikut:
            </font> <br>
            <ol style="margin:0;margin-left:-30px;">
                <li>
                    Bahwa semua informasi dalam formulir aplikasi ini telah saya isi dengan lengkap dan sebenar-benarnya.
                </li>
                <li>
                    Dengan ini saya memberikan persetujuan dan kuasa kepada PT. BPR Bangunarta untuk memperoleh referensi dari sumber manapun dengan cara yang dianggap layak oleh PT. BPR Bangunarta.
                </li>
                <li>
                    Apabila permohonan saya disetujui, saya akan tunduk dan terikat pada ketentuan dan syarat-syarat yang dikeluarkan oleh PT. BPR Bangunarta.
                </li>
                <li>
                    PT. BPR Bangunarta berhak untuk menolak permohonan saya apabila data dan informasi diri saya tidak sesuai dengan ketentuan yang berlaku di PT. BPR Bangunarta dengan menunjukan alasan penolakan tersebut.
                </li>
            </ol>
        </div>

        <div style="width:49%;float:right;font-size: 7pt;clear:right;margin-top:5px;">
            <table>
                <tr>
                    <td style="width:50%">
                        <center>
                            Tanda tanggan istri/suami <br>
                            pemohon
                        </center>
                    </td>
                    <td>
                        <center>
                            Tanda tangan pemohon <br>
                            &nbsp;
                        </center>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%">
                        <p style="margin-top:70px;"></p>
                        <center>
                            <u>...............................................................</u> <br>
                            Tanggal :................................................
                        </center>
                    </td>
                    <td>
                        <p style="margin-top:70px;"></p>
                        <center>
                            <u>...............................................................</u> <br>
                            Tanggal :................................................
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
