<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penjaminan Fiducia</title>
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

        <h4 style="text-align: center;font-size: 12pt;">SURAT KUASA PENDAFTARAN PENJAMINAN FIDUSIA</h4>

        Yang bertanda tangan di bawah ini :
        <table style="margin-left:20px;">
            <tr>
                <td width="14%">Nama</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->nama_pekerjaan }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">{{ $data->alamat_ktp }}</td>
            </tr>
        </table>

        <p></p>

        Dengan ini secara bersama - sama memberi kuasa kepada :
        <table style="margin-left:20px;">
            <tr>
                <td width="14%">Nama</td>
                <td class="text-center" width="1%"> : </td>
                <td>MOHAMAD MUKSIN</td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td class="text-center" width="1%"> : </td>
                <td>DIREKTUR UTAMA</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">JL. H. IKHSAN NO. 89 PAMANUKAN - SUBANG JAWA BARAT</td>
            </tr>
        </table>

        <p></p>

        Untuk mewakili Pemberi Kuasa melakukan tindakan hukum tersebut di bawah ini :
        <table>
            <tr>
                <td width="3%" style="vertical-align: text-top;">1.</td>
                <td colspan="3" style="text-align: justify;">Menghadap kepada Notaris, JOICE HAPSARI FENDRINI SH,
                    M.Kn. dan menandatangani akta jaminan Fidusia atas nama Pemberi Kuasa atas sebuah kendaraan : </td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    Merk / Jenis
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->merek) }} / {{ Str::upper($data->nama_jenis_jaminan) }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    Tahun / Warna
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ $data->tahun }} / {{ $data->warna }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    No. Rangka
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->no_rangka) }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    No. Mesin
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->no_mesin) }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    BPKB Atas Nama
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->nama_pemilik_bpkb) }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    No. Polisi
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->no_polisi) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="text-align: justify;">
                    Jaminan tersebut diberikan oleh Pemberi Kuasa kepada PT. BPR PAMANUKAN BANGUNARTA
                </td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    Tanggal
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ $data->hari_ini }}</td>
            </tr>
            <tr>
                <td></td>
                <td width="20%">
                    Nomor SPK
                </td>
                <td class="text-center" width="3%"> : </td>
                <td>{{ Str::upper($data->no_spk) }}</td>
            </tr>

            <tr>
                <td width="3%" style="vertical-align: text-top;">2.</td>
                <td colspan="3" style="text-align: justify;">Memberikan keterangan - keterangan dan dokumen - dokumen
                    seperlunya kepada Notaris tersebut atas semua hal yang berkaitan dengan Pembuatan Penandatanganan
                    Akta Jaminan Fidusia dan Pendaftarannya.</td>
            </tr>

            <tr>
                <td width="3%" style="vertical-align: text-top;">3.</td>
                <td colspan="3" style="text-align: justify;">Menerima Sertifikat Fidusia dari Instansi yang
                    berwenang.</td>
            </tr>

            <tr>
                <td width="3%" style="vertical-align: text-top;"></td>
                <td colspan="3" style="text-align: justify;">Kuasa ini merupakan bagian yang sangat penting dan
                    Perjanjian Pembiayaan Konsumen tersebut diatas, oleh karenanya kuasa ini tidak dapat dicabut kembali
                    dan tidak akan berakhir oleh sebab - sebab yang sebagaimana diatur dalam pasal 1813,1814 dan 1818
                    Kitab Undang - undang Hukum Perdata Indonesia.</td>
            </tr>

            <tr>
                <td width="3%" style="vertical-align: text-top;"></td>
                <td colspan="3" style="text-align: justify;">Demikian Surat Kuasa ini dibuat untuk dipergunakan
                    sesuai keperluannya. </td>
            </tr>
        </table>

        <p style="margin-top:50px;"></p>

        <table>
            <tr>
                <td width="30%"></td>
                <td width="40%"></td>
                <td class="text-center" width="30%">
                    Subang, {{ $data->hari_ini }}
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    Penerima Kuasa
                    <p style="margin-top:100px;"></p>
                    <b>( <u>MOHAMAD MUKSIN</u> )</b>
                </td>
                <td></td>
                <td class="text-center">
                    Pemberi Kuasa
                    <p style="margin-top:100px;"></p>
                    <b>( <u>{{ Str::upper($data->nama_nasabah) }}</u> )</b>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
