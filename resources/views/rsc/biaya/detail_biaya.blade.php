<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rincian Biaya RSC</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
            margin-left: 1.5cm;
            margin-right: 1cm;
        }

        table {
            border: 1px solid black;
            width: 82%;
            font-size: 12px;
            border-collapse: collapse;
        }

        td {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        .row {
            display: flex;
            gap: 1px;
        }

        .col {
            flex: 1;
            box-sizing: border-box;
        }

        .col-1 {
            flex: 0 0 7%;
        }

        .col-2 {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="content">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">
        <center>
            <h4>RINCIAN PERHITUNGAN BIAYA ADMINISTRASI PENYELAMATAN KREDIT</h4>
            <h4 style="margin-top: -15px;">RESCHEDULING/RECONDITIONING/RESTRUCTURING</h4>


            <table>
                <tr>
                    <td style="width: 35%;"><b><u>Jenis Penyelamatan Kredit</u></b></td>
                    <td style="width: 2%;">:</td>
                    <td colspan="2"><b>{{ $data->jenis_persetujuan }}</b></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b><u>Identitas Debitur</u></b></td>
                    <td>:</td>
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Nama Debitur</td>
                    <td>:</td>
                    <td colspan="2"><b>{{ $data->nama_nasabah }}</b></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Nn Loan</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->no_loan }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>No ACC Dropping</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->no_acc_dropping }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>JW Baru</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->jangka_waktu }} Bulan</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Rate & RPS</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->suku_bunga }}% {{ $data->metode_rps }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Tgl Eff Penyelamatan Kredit</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->tgl_mulai_rsc }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Tgl Jt Tempo Baru</td>
                    <td>:</td>
                    <td colspan="2">{{ $data->tgl_akhir_rsc }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b><u>Plafond Penyelamatan Kredit</u></b></td>
                    <td>:</td>
                    <td>a. </td>
                    <td style="width: 40%;">- Sisa Outstanding/Baki Debt.</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->baki_debet, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;">- Tung. Bunga</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">
                        {{ number_format($data->tunggakan_bunga, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>b. </td>
                    <td style="width: 40%;">Konversi/Kapitalisasi Tung. Bunga</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; border-bottom:1px solid black; text-align:right;">
                        {{ number_format($data->kapitalisasi, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>c. </td>
                    <td style="width: 40%;">Plafond RSC</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">
                        <b>{{ number_format($data->penentuan_plafon, '0', ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2;"><b><u>Administrasi dibayar</u></b></td>
                    <td>:</td>
                    <td>a. </td>
                    <td style="width: 40%;">By. Administrasi/Provisi RSC</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">
                        {{ number_format($data->administrasi_nominal, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;"><b><u>KYD Provisi [117200]</u></b></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;"></td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td>:</td>
                    <td>b. </td>
                    <td style="width: 40%;">Tag. Pokok</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->poko_dibayar, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td>:</td>
                    <td>c. </td>
                    <td style="width: 40%;">Tag. Bunga</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->bunga_dibayar, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2; font-size:10px;">KEW LAIN BNG KRD KEBAITULLAH</td>
                    <td>:</td>
                    <td>d. </td>
                    <td style="width: 40%;">Tag. Denda</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->denda_dibayar, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2;">GL : 222910</td>
                    <td>:</td>
                    <td>e. </td>
                    <td style="width: 40%;">UJROH KIH</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->ujroh, '0', ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td>:</td>
                    <td>f. </td>
                    <td style="width: 40%;">Asuransi TLO</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->asuransi_tlo, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;"><b>KS. TTPN ASS TLO [211203]</b></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;"></td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td>:</td>
                    <td>g. </td>
                    <td style="width: 40%;">Polis & Meterai TLO</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">-</td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;"><b>KS. TTPN POLMAT [211206]</b></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;"></td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td>:</td>
                    <td>h. </td>
                    <td style="width: 40%;">Asuransi Jiwa Kematian</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">{{ number_format($data->asuransi_jiwa, '0', ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;"><b>BUMIDA 1967 M UP 100 (211211)</b></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right; border-bottom: 1px solid black;"></td>
                </tr>
                <tr>
                    <td style="line-height: 2;"></td>
                    <td></td>
                    <td></td>
                    <td style="width: 40%;"><b></b></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%; text-align:right;">
                        <b>{{ number_format($data->total, '0', ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="border: 1px solid black;">
                        <div class="row">
                            <div class="col col-1">
                                <p><b>Note :</b></p>
                            </div>
                            <div class="col col-2">
                                <p>
                                    <i>***wajib melengkapi kelengkapan dokumen yang sudah lengkap di isi & di
                                        tandatangan
                                        (lembar pinbuk, adjustmen denda apabila mendapatkan kebijakan keringanan
                                        pembayaran
                                        denda)</i>
                                </p>
                            </div>
                        </div>

                    </td>
                </tr>
            </table>
        </center>
    </div>

    <script>
        window.print()
    </script>
</body>

</html>
