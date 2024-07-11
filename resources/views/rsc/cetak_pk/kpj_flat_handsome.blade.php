<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Kredit RSC</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.5cm;
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

        .text-hg {
            background-color: #f4ff81;
            color: #000;
            display: inline;
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
                font-size: 11pt;
                line-height: 1.5;
            }

            .header {
                width: 80%;
                margin-top: 0cm;
                margin-left: 2cm;
            }

            hr {
                position: relative;
                z-index: 5;
                border: 1px solid #161717;
                border-style: double;
                width: 100%;
                margin-left: 0cm;
            }

            .content {
                margin-top: -2cm;
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <td>
                    <div class="header">
                        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
                        <hr>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="content">
                        <h4 style="text-align: right;font-size: 12pt;">
                            <font class="text-hg">{{ 'No. ' . $data->spk_rsc }}</font>
                        </h4>

                        <h4 style="text-align: center;font-size: 12pt;">
                            <u>PERJANJIAN KREDIT DALAM RANGKA RESTRUKTURISASI KREDIT</u> <br>
                        </h4>

                        Yang bertanda tangan di bawah ini :

                        <table>
                            <tr>
                                <td width="2%" style="position: absolute;">1. </td>
                                <td colspan="3" style="text-align: justify;">
                                    MOHAMAD MUKSIN yang karena jabatannya bertindak dan atas nama PT. BPR
                                    BANGUNARTA, yang selanjutnya disebut "BANK".
                                </td>
                                <td class="text-center" width="1%"></td>
                                <td class="text-hg"></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="2%">2. </td>
                                <td width="15%">Nama</td>
                                <td class="text-center" width="1%"> : </td>
                                <td class="text-hg">{{ $data->nama_nasabah }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>No. KTP</td>
                                <td class="text-center" width="1%"> : </td>
                                <td>{{ $data->no_identitas }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Pekerjaan</td>
                                <td class="text-center"> : </td>
                                <td>
                                    <font class="text-hg">{{ $data->nama_pekerjaan }}</font>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="vertical-align: text-top;">Alamat</td>
                                <td class="text-center" style="vertical-align: text-top;"> : </td>
                                <td class="text-hg">{{ $data->alamat_ktp }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3" style="text-align: justify;">
                                    Dalam hal ini bertindak untuk dan atas namanya sendiri, selanjutnya disebut
                                    "Pengambil Kredit". Kedua belah pihak yaitu Bank dan Pengambil Kredit secara
                                    bersama-sama selanjutnya disebut para pihak.

                                </td>
                            </tr>
                        </table>
                        <br>
                        Para pihak menerangkan terlebih dahulu :
                        <table>
                            <tr>
                                <td width="2%" style="position: absolute;">- </td>
                                <td colspan="3" style="text-align: justify;">
                                    Pengambil Kredit telah memperoleh fasilitas kredit dari BANK sebesar
                                    <font class="text-hg">Rp. {{ number_format($data->plafon, '0', ',', '.') }}
                                        ( <font style="text-transform: capitalize;">
                                            {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->plafon) }}</font>
                                        Rupiah
                                        )</font> dengan syarat-syarat/ketentuan-ketentuan dan sebagaimana tercantum
                                    dalam Surat
                                    Perjanjian Kredit nomor <font class="text-hg">{{ $data->no_spk }}</font> tanggal
                                    <font class="text-hg">{{ $data->tgl_create_pk }}</font> yang
                                    telah dibuat oleh para pihak dan atau perjanjian-perjanjian lainnya yang
                                    bersangkutan.
                                </td>
                                <td class="text-center" width="1%"></td>
                                <td class="text-hg"></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="2%">- </td>
                                <td colspan="3" style="text-align: justify;">Bahwa kredit tersebut berakhir pada
                                    tanggal <font class="text-hg">{{ $data->tgl_akhir_pk }}</font>.</td>
                                <td class="text-center" width="1%"></td>
                                <td class="text-hg"></td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">- </td>
                                <td colspan="3" style="text-align: justify;">Bahwa berdasarkan perhitungan kewajiban
                                    kredit tersebut diatas terdapat kewajiban yang belum
                                    diselesaikan oleh pengambil kredit kepada Bank sebesar Rp.
                                    {{ number_format($data->penentuan_plafon, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->penentuan_plafon) }}
                                    </font> Rupiah
                                    ) yang terdiri dari kewajiban pokok sebesar Rp.
                                    {{ number_format($data->baki_debet, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->baki_debet) }}
                                    </font> Rupiah
                                    )
                                    kewajiban Bunga sebesar Rp.
                                    {{ number_format($data->tunggakan_bunga, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->tunggakan_bunga) }}
                                    </font> Rupiah
                                    ) kewajiban denda sebesar
                                    Rp. {{ number_format($data->tunggakan_denda, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->tunggakan_denda) }}
                                    </font> Rupiah
                                    ).</td>
                                <td class="text-center" width="1%"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">- </td>
                                <td colspan="3" style="text-align: justify;">
                                    Bahwa atas permohonan pengambil kredit, Bank menyetujui untuk merubah perjanjian
                                    kredit sehingga :
                                </td>
                            </tr>
                        </table>

                        <table style="margin-left: 12px; width: 97%;">
                            <tr>
                                <td style="position: absolute;">a. </td>
                                <td style="text-align: justify;">Plafon kredit menjadi sebesar Rp.
                                    {{ number_format($data->penentuan_plafon, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->penentuan_plafon) }}
                                    </font> Rupiah
                                    );
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">b. </td>
                                <td style="text-align: justify;">Jangka waktu kredit menjadi {{ $data->jw_rsc }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jw_rsc) }}
                                    </font>) bulan
                                    dengan suku bunga sebesar {{ $data->suku_bunga_rsc }}%
                                    pertahun secara {{ $data->metode_rps_rsc }};
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">c. </td>
                                <td style="text-align: justify;">Kesanggupan bayar pokok sebesar Rp.
                                    {{ number_format($data->angsuran_pokok, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran_pokok) }}
                                    </font> Rupiah) dimulai pada tanggal {{ $data->tgl_bayar_rsc }} selama
                                    {{ $data->jw_rsc }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jw_rsc) }}
                                    </font>
                                    ) bulan;
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>d. </td>
                                <td>
                                    Pembayaran bunga sebesar Rp.
                                    {{ number_format($data->angsuran_bunga, '0', ',', '.') }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran_bunga) }}
                                    </font> Rupiah
                                    ) setiap bulan dimuali tanggal {{ $data->tgl_bayar_rsc }} selama
                                    {{ $data->jw_rsc }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jw_rsc) }}
                                    </font>
                                    ) bulan;
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>e. </td>
                                <td>
                                    Jatuh tempo pembayaran kredit sampai dengan tanggal {{ $data->tgl_akhir_rsc }};
                                </td>
                                <td></td>
                            </tr>
                        </table>
                        <br>
                        Sehubungan dengan hal-hal yang telah diuraikan di atas, para pihak setuju untuk dan dengan ini
                        membuat suatu perubahan dari perjanjian kredit nomor {{ $data->no_spk }} tanggal
                        {{ $data->tgl_create_pk }} sebagai berikut :
                        <table>
                            <tr>
                                <td>1. </td>
                                <td>
                                    <b>
                                        Mengubah ketentuan pasal 1, sehingga untuk selanjutnya berbunyi sebagai berikut
                                        :
                                    </b>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="text-align: justify;">
                                    Bahwa pengambil kredit mengakui telah meminjam uang dari Bank sejumlah Rp.
                                    {{ number_format($data->penentuan_plafon, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->penentuan_plafon) }}
                                    </font> Rupiah
                                    ).
                                </td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">2. </td>
                                <td style="text-align: justify;">
                                    <b>Mengubah ketentuan pasal 3 ayat 1, sehingga untuk selanjutnya berbunyi sebagai
                                        berikut :</b>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="text-align: justify;">
                                    Dengan mengindahkan syarat-syarat dan ketentuan perjanjian Kredit, batas waktu
                                    penggunaan pinjaman ditentukan dalam jangka waktu {{ $data->jw_rsc }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jw_rsc) }}
                                    </font>) bulan atau
                                    harus sudah lunas paling lambat tanggal {{ $data->tgl_akhir_rsc }}.
                                </td>
                            </tr>
                            <tr>
                                <td style="position: absolute;">3. </td>
                                <td style="text-align: justify;">
                                    <b>
                                        Mengubah ketentuan pasal 3 Ayat 2, sehingga untuk selanjutnya berbunyi sebagai
                                        berikut :
                                    </b>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    - &nbsp;&nbsp;Angsuran Pokok Rp.
                                    {{ number_format($data->angsuran_pokok, '0', ',', '.') }} ( <font
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran_pokok) }}
                                    </font> Rupiah
                                    ) setiap {{ $data->jp_rsc }} bulan selama @if (is_null($data->jw_rsc_musiman))
                                        {{ $data->jw_rsc }} bulan.
                                    @else
                                        {{ $data->jw_rsc }} musim.
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    - &nbsp;&nbsp;Angsuran Bunga Rp.
                                    {{ number_format($data->angsuran_bunga, '0', ',', '.') }} (
                                    <font style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->angsuran_bunga) }}
                                    </font> Rupiah
                                    ) setiap {{ $data->jp_rsc }} bulan selama @if (is_null($data->jw_rsc_musiman))
                                        {{ $data->jw_rsc }} bulan.
                                    @else
                                        {{ $data->jw_rsc }} musim.
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Pembayaran kewajiban-kewajiban tersebut diatas tidak diperkenankan untuk menunggak.
                                </td>
                            </tr>
                        </table>

                        <p>Dengan disepakatinya Perubahan Perjanjian Kredit ini, semua syarat-syarat dan
                            ketentuan-ketentuan yang berlaku dalam Surat Perjanjian Kredit (SPK) Nomor
                            {{ $data->no_spk }} tanggal {{ $data->tgl_create_pk }} maupun dalam perjanjian-perjanjian
                            lainnya yang bersangkutan dengan pemberian kredit tersebut dengan ini dinyatakan tetap
                            berlaku, kecuali ketentuan-ketentuan yang telah mengalami perubahan tersebut diatas.</p>

                        <p>
                            Demikian Perjanjian Perpanjangan Kredit ini dibuat rangkap dua lembar dalam aslinya dan
                            ditandatangani di Pamanukan, pada hari {{ $data->hari_mulai_rsc }} tanggal
                            {{ $data->tgl_mulai_rsc }}.
                        </p>

                        <br>
                        <br>

                        <table>
                            <tr>
                                <td style="width:30%;">
                                    <center>
                                        Pengambil Kredit,
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <b><u>{{ $data->nama_nasabah }}</u></b>
                                    </center>
                                </td>
                                <td style="width:40%;">
                                    &nbsp;
                                </td>
                                <td style="width:30%;">
                                    <center>
                                        PT. BPR Bangunarta
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <b><u>MOHAMAD MUKSIN</u></b>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <center>
                                        Turut Bertanggungjawab
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <b><u>{{ $data->nama_pendamping }}</u></b>
                                    </center>
                                </td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>

</html>
