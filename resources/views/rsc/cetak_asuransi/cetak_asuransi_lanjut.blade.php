<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Asuransi RSC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.5cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
        }

        .float-right-custom {
            text-align: right;
            align-items: right;
            float: right;
        }

        .content .data {
            margin-left: 2cm;
            margin-right: 2cm;
            margin-top: 30px;
        }

        .content .materai {
            margin-left: 2cm;
            margin-right: 2cm;
        }

        hr {
            position: relative;
            z-index: 5;
            border: 1px solid #000000;
            /* border-style: double; */
            width: 100%;
            margin-left: 0cm;
            margin-bottom: 5px;
        }

        .data table {
            margin-bottom: 15px;
        }

        @media print {
            .content {
                margin-top: 1.0cm;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <center>
            <h5><u><b>SURAT PERNYATAAN ASURANSI JIWA</b></u></h5>
        </center>
        <div class="data">
            <table>
                <tr>
                    <td colspan="3">Yang bertanda tangan dibawah ini :</td>
                </tr>
                <tr>
                    <td width='15%' style="vertical-align: text-top;">Nama</td>
                    <td>:</td>
                    <td style="text-align: justify; padding-left: 7px;"><b>{{ $data->nama_nasabah }}</b></td>
                </tr>
                <tr>
                    <td width='15%' style="vertical-align: text-top;">Alamat</td>
                    <td style="vertical-align: text-top;">:</td>
                    <td style="text-align: justify; padding-left: 7px;"><b>{{ $data->alamat_ktp }}</b>
                    </td>
                </tr>
            </table>

            <p style="margin-left: 2px;">Menyatakan dengan ini bahwa :</p>
            <ol style="margin-left: -16px; margin-top: -13px; line-height: 1.5em;">
                <li style="text-align: justify;">
                    Saya mengakui telah menerima pencairan restrukturisasi kredit dari PT. BPR Bangunarta sebesar
                    <b>{{ 'Rp. ' . number_format($data->penentuan_plafon, 0, ',', '.') }}</b> dengan suku bunga
                    <b>{{ $data->suku_bunga }}% {{ $data->metode_rps }}</b> per tahun dengan jangka waktu kredit
                    selama <b>{{ $data->jangka_waktu }}</b> bulan sesuai dengan surat perjanjian kredit restrukturisasi
                    nomor <b>{{ $data->no_spk }}</b>, yang telah ditandatangani bersama pada tanggal
                    <b>{{ $data->tgl_rsc }}</b>.
                </li>
                <li style="text-align: justify;">
                    Saya mengakui bahwa PT. BPR BANGUNARTA telah menjelaskan mengenai penawaran asuransi jiwa, dengan
                    manfaat apabila karena sesuatu hal yang terjadi mengakibatkan saya meninggal dunia. Maka kewajiban
                    saya kepada PT. BPR BANGUNARTA akan ditanggung oleh pihak asuransi jiwa sesuai dengan jumlah klaim
                    asuransi jiwa dan sepanjang jangka waktu kepesertaan asuransi jiwa saya masih berlaku, serta akan
                    mendapatkan pengembalian jika jumlah klaim asuransi jiwa lebih besar dari pada seluruh kewajiban.
                </li>
                <li style="text-align: justify;">
                    Saya mengetahui dan mengerti dengan baik mengenai batas maksimal nilai pertanggungan asuransi
                    jiwa saya yang dapat ditanggung oleh pihak asuransi jiwa yaitu sebesar
                    <b>
                        @if (is_null($data->nilai_pertanggungan))
                            NILAI PERTANGGUNGAN TIDAK ADA
                        @else
                            {{ 'Rp. ' . number_format($data->nilai_pertanggungan, 0, ',', '.') }}
                        @endif
                    </b> Dan
                    jangka waktu pertanggungan asuransi jiwa saya mulai dari tanggal <b>
                        @if (is_null($data->tgl_mulai))
                            TANGGAL MULAI TIDAK ADA
                        @else
                            {{ $data->tgl_mulai }}
                        @endif
                    </b>
                    sampai dengan tanggal <b>
                        @if (is_null($data->tgl_akhir))
                            TANGGAL AKHIR TIDAK ADA
                        @else
                            {{ $data->tgl_akhir }}
                        @endif
                    </b>.
                </li>
                <li style="text-align: justify;">
                    Sehubungan dengan jangka waktu kredit saya yang melebihi dari ketentuan maksimal nilai
                    pertanggungan kepesertaan asuransi jiwa, dan apabila di kemudian hari terjadi sesuatu hal yang
                    mengakibatkan saya meninggal dunia, maka seluruh kewajiban saya kepada PT. BPR BANGUNARTA yang tidak
                    dapat di tanggung oleh pihak asuransi jiwa menjadi tanggung jawab ahli waris sepenuhnya.
                </li>
            </ol>
            <p style="text-align: justify;">Demikian surat pernyataan ini saya buat dengan sebenarnya dalam keadaan
                sehat jasmani dan rohani tidak
                ada paksaan dari pihak manapun</p>

            <div class="container-lg px-4">
                <div class="row">
                    <div class="col text-center">
                        Pamanukan, {{ $data->tgl_now }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-4 text-center">
                        Mengetahui,
                    </div>
                    <div class="col">

                    </div>
                    <div class="col float-right-custom">
                        Yang Membuat Penyataan,
                        <br>
                        <center>
                            <label for="" style="font-size: 10px; margin-top: 40px;">Meterai Rp. 10.000</label>
                        </center>
                    </div>
                </div>
            </div>
            <br>
            <div class="container-lg" style="margin-top: 40px;">
                <div class="row">
                    <div class="col-4 text-center">
                        <center>
                            <hr>
                            Ahli Waris
                        </center>
                    </div>
                    <div class="col">

                    </div>
                    <div class="col float-right-custom">
                        <center>
                            <hr>
                            Debitur
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

<script>
    window.print();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

</html>
