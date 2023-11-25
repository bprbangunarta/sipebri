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
                            <td style="text-align: right;">{{ $data->fnama }}</td>
                        </tr>
                        <tr>
                            <td style="width:65px;">No. Rek</td>
                            <td><center> : </center></td>
                            <td style="text-align: right;">{{ $data->noacc }}</td>
                        </tr>
                        <tr>
                            <td style="width:65px;">Surveyor</td>
                            <td><center> : </center></td>
                            <td style="text-align: right;text-transform:uppercase;">{{ $data->surveyor }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <h4 style="text-align: center;font-size: 12pt;margin-top:10px;">FORMULIR PERMOHONAN KREDIT</h4>
        <div style="margin-top:-10px;">
            <font>Dengan ini saya mengajukan Permohonan Kredit :</font><br>
            <font>
                Jenis kredit yang dimohon: <b>{{ $data->produk_kode }}</b> Jumlah kredit yang dimohon : <b>Rp. {{ number_format($data->plafon,0,'.','.') }}</b> Jangka Waktu <b>{{ $data->jangka_waktu }} Bulan</b> Sistem angsuran : <b>{{ $data->metode_rps }}</b>. Rencana Penggunaan dana untuk : <b>{{ $data->penggunaan }}, {{ $data->penggunaan_ket }}</b>
            </font>
        </div>

        <h4 style="text-align: center;font-size: 12pt;">DATA PRIBADI</h4>
        <div style="margin-top:-10px;">
            <table>
                <tr>
                    <td>Nama Lengkap</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        {{ $data->nama_nasabah }}
                    </td>
                </tr>

                <tr>
                    <td>Nama Panggilan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        {{ $data->nama_panggilan }}
                    </td>
                </tr>

                <tr>
                    <td>Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        KTP -  {{ $data->no_identitas_n }}
                    </td>
                </tr>

                <tr>
                    <td>Jth. Tempo ID</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->masa_identitas_n }}</td>
                </tr>


                <tr>
                    <td>Jenis Kelamin</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        @if ($data->jenis_kelamin == '1')
                            LAKI-LAKI
                        @else
                            PEREMPUAN
                        @endif
                    </td>
                </tr>

                <tr>
                    <td>Tempat, Tgl. Lahir</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->kota_n }}, {{ $data->ttl_n }}</td>
                </tr>

                <tr>
                    <td style="vertical-align: text-top;">Alamat KTP</td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td>
                        {{ $data->alamat_ktp_n }}, {{ $data->kode_pos }}
                    </td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->no_telp }}</td>
                </tr>

                <tr>
                    <td>Pekerjaan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td style="text-transform: uppercase;">{{ $data->nama_pekerjaan }}</td>
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
                    <td>
                        @if ($data->agama == '1')
                            ISLAM
                        @elseif ($data->agama == '2') 
                            KATOLIK
                        @elseif ($data->agama == '3') 
                            KREISTEN
                        @elseif ($data->agama == '4') 
                            HINDU
                        @elseif ($data->agama == '5') 
                            BUDHA
                        @else
                            KONG HU CU
                        @endif
                    </td>
                </tr>

                <tr>
                    <td>Status Perkawinan</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>
                        @if ($data->status_pernikahan == 'M')
                            MENIKAH
                        @elseif ($data->status_pernikahan == 'L') 
                            LAJANG
                        @elseif ($data->status_pernikahan == 'D') 
                            DUDA
                        @else
                            JANDA
                        @endif
                    </td>
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
                        1. Milik Sendiri &nbsp;&nbsp; 2. Sewa/kontrak &nbsp;&nbsp; 3. Kredit &nbsp;&nbsp; 4. Milik orang tua &nbsp;&nbsp; 5. Rumah dinas &nbsp;&nbsp; <br> 6. Lainnya .....................................................................................................................................
                    </td>
                </tr>

                <tr>
                    <td>Nama Gadih Ibu Kandung</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->nama_ibu_kandung }}</td>
                </tr>
            </table>
        </div>

        <div style="width:49%;float:left;clear: left;argin-bottom:10px;">
            <table>
                <tr>
                    <td style="width:145px;vertical-align: text-top;">
                        Nama Pendamping <br>
                        ({{ $data->status_pendamping }})
                    </td>
                    <td style="width:10px;vertical-align: text-top;"><center> : </center></td>
                    <td style="vertical-align: text-top;">{{ $data->nama_pendamping }}</td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->no_hp }}</td>
                </tr>

                <tr>
                    <td>Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>KTP</td>
                </tr>

                <tr>
                    <td>No. Identitas</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->no_identitas_p }}</td>
                </tr>

                <tr>
                    <td>Tempat, Tgl. Lahir</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->tempat_lahir_p }}, {{ $data->ttl_p }}</td>
                </tr>

                <tr>
                    <td>Jth. Tempo ID</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->masa_identitas_p }}</td>
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
                    <td>......................................................</td>
                </tr>

                <tr>
                    <td>No. HP</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>{{ $data->no_telp_darurat }}</td>
                </tr>

                <tr>
                    <td>Jenis Kelamin</td>
                    <td style="width:10px;"><center> : </center></td>
                    <td>1. Laki-laki &nbsp;&nbsp; 2. Perempuan</td>
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
                        <p style="margin-top:65px;"></p>
                        <center>
                            {{ $data->nama_nasabah }}
                            <u>...............................................................</u> <br>
                            Tanggal :................................................
                        </center>
                    </td>
                    <td>
                        <p style="margin-top:65px;"></p>
                        <center>
                            {{ $data->nama_pendamping }}
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
