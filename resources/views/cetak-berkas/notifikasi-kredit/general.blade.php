<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Kredit</title>
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

        {{-- <h4 style="text-align: center;font-size: 12pt;">NOTIFIKASI KREDIT</h4> --}}

        <table>
            <tr>
                <td width="14%">Nomor</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->no_notifikasi }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->tgl_notifikasi_hari_ini }}</td>
            </tr>
            <tr>
                <td>Kode Pengajuan</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->kode_pengajuan }}</td>
            </tr>
        </table>

        <p></p>

        Kepada Yth. <br>
        Bapak/Ibu {{ $data->nama_nasabah }} <br>
        Di <br>
        {{ $data->alamat_ktp }} No. Telp/HP : {{ $data->no_telp }}

        <p></p>

        <table>
            <tr>
                <td width="14%">Perihal</td>
                <td class="text-center" width="1%"> : </td>
                <td><b><u>Notifikasi Kredit</u></b></td>
            </tr>
        </table>

        <p></p>

        Menunjuk permohonan fasilitas kredit Konsumsi Lainnya atas nama Bapak/Ibu, dengan ini kami beritahukan bahwa PT
        BPR BANGUNARTA dapat menyetujui permohonan kredit Bapak/Ibu tersebut, dengan ketentuan-ketentuan dan
        syarat-syarat sebagai berikut :

        <p></p>

        <table>
            <tr>
                <td class="text-center" width="2%"> 1. </td>
                <td width="27%">Limit Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">
                    {{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }}
                    ( <font style="text-transform: capitalize;">
                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->plafon) }}</font> Rupiah )
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 2. </td>
                <td width="27%">Jenis Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">{{ Str::upper($data->produk_kode) }} -
                    {{ Str::upper($data->nama_produk) }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 3. </td>
                <td width="27%" style="vertical-align: text-top;">Tujuan Penggunaan Kredit</td>
                <td class="text-center" width="1%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    {{ Str::upper($data->penggunaan) }}. Sepanjang tidak bertentangan dengan ketentuan hukum yang
                    berlaku, norma kesusilaan dan
                    ketertiban umum.
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 4. </td>
                <td width="27%">Sektor Ekonomi</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">{{ $data->sandi_sektor_ekonomi }}.
                    {{ $data->keterangan_sektor_ekonomi }}</td>
            </tr>

            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 5. </td>
                <td width="27%" style="vertical-align: text-top;">Agunan</td>
                <td class="text-center" width="1%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    {{ $data->count_jaminan }} ( <font style="text-transform: capitalize;">
                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->count_jaminan) }}</font> ) <br>
                    @forelse ($agunan as $item)
                        <ol style="margin-left: -40px;text-align: justify; list-style-type:none;">
                            @if ($item->jenis_jaminan == 'Tanah')
                                <li>
                                    {{ $loop->iteration }}.
                                    {{ Str::upper($item->nama_jenis_dokumen) . ',' . ' ' . Str::upper($item->jenis_jaminan) . ',' . ' ' . 'NO' . ' ' . $item->no_dokumen . ',' . ' ' . 'LUAS' . ' ' . number_format($item->luas, 0, ',', '.') . ' ' . 'M2' . ',' . ' ' . 'ATAS NAMA' . ' ' . strtoupper($item->atas_nama) . ',' . ' ' . 'ALAMAT' . ' ' . $item->lokasi }}
                                </li>
                            @elseif ($item->jenis_jaminan == 'Kendaraan')
                                <li>
                                    {{ $loop->iteration }}.
                                    {{ $item->catatan }}
                                </li>
                            @elseif ($item->jenis_jaminan == 'Lainnya')
                                <li>
                                    {{ $loop->iteration }}.
                                    {{ Str::upper($item->nama_jenis_dokumen) . ',' . ' ' . Str::upper($item->jenis_jaminan) . ',' . ' ' . 'ATAS NAMA' . ' ' . strtoupper($item->atas_nama) . ',' . ' ' . 'NO' . ' ' . $item->no_dokumen }}
                                </li>
                            @endif
                        </ol>
                    @empty
                    @endforelse
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 6. </td>
                <td width="27%">Jangka Waktu Kredit</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">{{ $data->jangka_waktu }} bulan terhitung sejak akad kredit.</td>
            </tr>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 7. </td>
                <td width="27%" style="vertical-align: text-top;">Pembayaran Kembali</td>
                <td class="text-center" width="1%" style="vertical-align: text-top;"> : </td>
                <td style="text-align: justify;">
                    Wajib dilakukan dalam {{ $data->jangka_waktu }} kali angsuran, yang dibayarkan setiap bulan
                    selambat-lambatnya pada tanggal yang sama dengan akad kredit untuk yang pertama kalinya, angsuran
                    dibayarkan satu bulan setelah tanggal akad kredit.
                </td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 8. </td>
                <td width="27%">Suku Bunga</td>
                <td class="text-center" width="1%"> : </td>
                <td style="text-align: justify;">{{ $data->suku_bunga }} % {{ $data->metode_rps }} / Tahun</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 9. </td>
                <td width="27%">Denda</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->b_denda }} % per hari dari angsuran pokok dan atau bunga
                    yang tertunggak</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"> 10. </td>
                <td width="27%">Biaya Kredit</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->biaya_kredit }} % dari plafon kredit</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">a. Provisi Sebesar</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->b_provisi ?? 0 }} %
                    {{ 'Rp. ' . '' . number_format($data->provisi, 0, ',', '.') ?? 0 }}</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">b. Administrasi Bank Sebesar</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">{{ $data->b_admin }} %
                    {{ 'Rp. ' . '' . number_format($data->administrasi, 0, ',', '.') ?? 0 }}</td>
            </tr>
            @if ($data->persen_apht != 0.0)
                <tr>
                    <td class="text-center" width="2%"></td>
                    <td width="27%">c. APHT</td>
                    <td class="text-center" width="3%"> : </td>
                    <td style="text-align: justify;">{{ $data->persen_apht }} %
                        {{ 'Rp. ' . '' . number_format($data->proses_apht, 0, ',', '.') ?? 0 }}</td>
                </tr>
            @elseif ($data->persen_apht != 0.0 && $data->persen_fiducia != 0.0)
                <tr>
                    <td class="text-center" width="2%"></td>
                    <td width="27%">c. APHT</td>
                    <td class="text-center" width="3%"> : </td>
                    <td style="text-align: justify;">{{ $data->persen_apht }} %
                        {{ 'Rp. ' . '' . number_format($data->proses_apht, 0, ',', '.') ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="text-center" width="2%"></td>
                    <td width="27%">c. FIDUCIA</td>
                    <td class="text-center" width="3%"> : </td>
                    <td style="text-align: justify;">{{ $data->persen_fiducia }} %
                        {{ 'Rp. ' . '' . number_format($data->by_fiducia, 0, ',', '.') ?? 0 }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-center" width="2%"> 11. </td>
                <td width="27%">Jumlah Biaya Asuransi</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Ekawaktu / - / -</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">a. Kendaraan (TLO)</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">2. Jiwa (Ekawaktu)</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
            <tr>
                <td class="text-center" width="2%"></td>
                <td width="27%">c. Kecelakaan Plus</td>
                <td class="text-center" width="3%"> : </td>
                <td style="text-align: justify;">Rp. 0 (dengan nilai pertanggungan Rp. 0)</td>
            </tr>
        </table>

        @forelse ($jaminan as $item)
            @if ($item->jenis_jaminan == 'Kendaraan')
                <table>
                    <tr>
                        <td class="text-center" width="2%"> {{ $loop->iteration + 11 }} </td>
                        <td width="27%">Untuk Jaminan Kendaraan</td>
                        <td class="text-center" width="3%" colspan="2"> : </td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td class="text-center" width="2%"></td>
                        <td style="text-align: justify;">
                            <ol type='a' style="margin-left: -20px;margin-top:-1px;tetxt-align:justify;">
                                <li>
                                    Debitur tidak akan menjaminkan atau menggunakannnya sebagai jaminan pinjaman,
                                    menjual atau
                                    memindahtangankan dengan cara apapun kepada pihak manapun dan menggunakan kendaraan
                                    tersebut
                                    hanya untuk pemakaian pribadi sampai seluruh Hutang dinyatakan lunas oleh pihak
                                    Bank.
                                </li>
                                <li>
                                    Apabila debitur lalai dari kewajiban atau tidak dapat mengangsur bunga dan atau
                                    pokok
                                    pinjaman selama 3 (tiga) bulan, maka Bank berhak melelang atau menjual dibawah
                                    tangan,
                                    barang jaminan kendaraan tersebut diatas kepada pihak ketiga guna mengangsur atau
                                    melunasi
                                    Hutang Debitur kepada Bank.
                                </li>
                                <li>
                                    Jika batas maksimal pinjaman berikut bunga telah melebihi jaminan yang dimiliki
                                    debitur,
                                    maka dengan Debitur menyatakan pula bahwa kepemilikan barang-barang jaminan
                                    kendaraan
                                    tersebut telah dialihkan kepada Bank dengan kewajiban Debitur untuk meyerahkan
                                    barang-barang
                                    jaminan kendaraan tersebut kepada Bank.
                                </li>
                                <li>
                                    Dalam hal kejadian diatas ( sesuai nomor b dan c), Debitur tidak dapat memenuhi
                                    kewajibannya
                                    untuk menyerahkan barang jaminan kendaraann, maka Debitur bersedia untuk diajukan ke
                                    proses
                                    peradilan pidana karena telah melakukan penggelapan atas barang yang sudah bukan
                                    lagi
                                    miliknya, sesuai dengan yang tercantum dalam pasal 372 KUHP.
                                </li>
                            </ol>
                        </td>
                    </tr>
                </table>
            @elseif ($item->jenis_jaminan == 'Tanah')
                <table>
                    <tr>
                        <td class="text-center" width="2%"> {{ $loop->iteration + 11 }} </td>
                        <td width="27%">Untuk Jaminan Tanah</td>
                        <td class="text-center" width="3%" colspan="2"> : </td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td class="text-center" width="2%"></td>
                        <td style="text-align: justify;">
                            <ol type='a' style="margin-left: -20px;margin-top:-1px;tetxt-align:justify;">
                                <li>
                                    Debitur tidak akan menjaminkan atau menggunakannya sebagai jaminan pinjaman, menjual
                                    atau
                                    memindahtangankan dengan cara apapun kepada pihak manapun sampai seluruh Hutang
                                    dinyatakan
                                    lunas oleh pihak Bank.
                                </li>
                                <li>
                                    Apabila debitur lalai dari kewajiban atau tidak dapat mengangsur bunga dan atau
                                    pokok
                                    pinjaman selama 3 (tiga) bulan atau lebih, maka Bank berhak melakukan
                                    tindakan-tindakan yang
                                    mendorong untuk penyelesaian tunggakan termasuk dalam hal ini BPR BANGUNARTA
                                    melakkukan
                                    tindakan pemasangan plakat "TANAH INI SEDANG DIJAMINKAN KE BPR BANGUNARTA" sampai
                                    tunggakan
                                    tersebut dapat terselesaikan atau Bank melakukan upaya-upaya yang mengarah pada
                                    penguasaan
                                    agunan untuk penyelesaian kredit tersebut dan dalam hal ini Debitur tidak berhak
                                    untuk
                                    menolak, mempersulit, menghalang-halangi, maupun menghambat.
                                </li>
                            </ol>
                        </td>
                    </tr>
                </table>
            @elseif ($item->jenis_jaminan == 'Lainnya')
                <table>
                    <tr>
                        <td class="text-center" width="2%"> {{ $loop->iteration + 11 }} </td>
                        <td width="27%">Untuk Jaminan Lainnya</td>
                        <td class="text-center" width="3%" colspan="2"> : </td>
                        <td></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td class="text-center" width="2%"></td>
                        <td style="text-align: justify;">
                            <ol type='a' style="margin-left: -20px;margin-top:-1px;tetxt-align:justify;">
                                <li>
                                    Debitur tidak akan menjaminkan atau menggunakannnya sebagai jaminan pinjaman,
                                    menjual atau
                                    memindahtangankan dengan cara apapun kepada pihak manapun dan menggunakan kendaraan
                                    tersebut
                                    hanya untuk pemakaian pribadi sampai seluruh Hutang dinyatakan lunas oleh pihak
                                    Bank.
                                </li>
                                <li>
                                    Apabila debitur lalai dari kewajiban atau tidak dapat mengangsur bunga dan atau
                                    pokok
                                    pinjaman selama 3 (tiga) bulan, maka Bank berhak melelang atau menjual dibawah
                                    tangan,
                                    barang jaminan kendaraan tersebut diatas kepada pihak ketiga guna mengangsur atau
                                    melunasi
                                    Hutang Debitur kepada Bank.
                                </li>
                                <li>
                                    Jika batas maksimal pinjaman berikut bunga telah melebihi jaminan yang dimiliki
                                    debitur,
                                    maka dengan Debitur menyatakan pula bahwa kepemilikan barang-barang jaminan
                                    kendaraan
                                    tersebut telah dialihkan kepada Bank dengan kewajiban Debitur untuk meyerahkan
                                    barang-barang
                                    jaminan kendaraan tersebut kepada Bank.
                                </li>
                                <li>
                                    Dalam hal kejadian diatas ( sesuai nomor b dan c), Debitur tidak dapat memenuhi
                                    kewajibannya
                                    untuk menyerahkan barang jaminan kendaraann, maka Debitur bersedia untuk diajukan ke
                                    proses
                                    peradilan pidana karena telah melakukan penggelapan atas barang yang sudah bukan
                                    lagi
                                    miliknya, sesuai dengan yang tercantum dalam pasal 372 KUHP.
                                </li>
                            </ol>
                        </td>
                    </tr>
                </table>
            @endif
        @empty
        @endforelse

        <table style="margin-top:-15px;">
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 14. </td>
                <td style="text-align: justify;">
                    Debitur tidak diperbolehkan memberikan suatu imbalan dalam bentuk apapun kapada pejabat dan atau
                    karyawan BPR BANGUNARTA berkanaan dengan persetujuan pemberian kredit, kecuali Biaya jasa survey
                    yang dibayar oleh debitur pada saat survey yang disertakan bukti penerimaan uang jasa surveY.
                    Apabila dikemudian hari diketahui bahwa Debitur melanggar larangan tersebut, maka kepada Debitur dan
                    Pejabat atau petugas BPR BANGUNARTA dapat dikenakan sanksi sesuai ketentuan BPR BANGUNARTA.
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="text-center" width="2%" style="vertical-align: text-top;"> 15. </td>
                <td style="text-align: justify;">
                    Notifikasi Kredit ini merupakan bagian yang tidak dapat terpisahkan dari Perjanjian Kredit berikut
                    syarat-syarat umum Perjanjian Kredit BPR BANGUNARTA.
                </td>
            </tr>
        </table>

        Sebagai tanda persetujuan Bapak/Ibu, harap Notifikasi ini ditandatangani disertai dengan nama jelas. Demikian
        agar Bapak / Ibu maklum.

        <p></p>

        <table>
            <tr>
                <td width="40%"></td>
                <td></td>
                <td style="text-align: justify" width="40%">
                    Pamanukan, {{ $data->tgl_notifikasi_hari_ini }}
                </td>
            </tr>
            <tr>
                <td class="text-center" style="vertical-align: text-top;" width="40%">
                    PT. BPR BANGUNARTA
                </td>
                <td></td>
                <td style="text-align: justify" width="42%">
                    Dengan ini saya menyatakan telah membaca mengetahui dan menyetujui isi Notifikasi Kredit <br>
                    No. {{ $data->no_notifikasi }}
                </td>
            </tr>
            <tr>
                <td class="text-center" width="40%">
                    <center>
                        <p style="margin-top:70px;"></p>
                        {{-- <img src="https://images.tokopedia.net/img/cache/700/product-1/2018/11/8/39617213/39617213_91fd0f0c-03c2-43b4-861a-64e9f04e8f24_700_700.jpeg" width="100" height="100" style="margin-top:-30px;"> --}}
                    </center>
                    <u>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Surveyor
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </u>
                    <br>
                    <font style="text-transform: uppercase;">{{ $data->nama_user }}</font>
                </td>
                <td></td>
                <td class="text-center" width="40%">
                    <p style="margin-top:70px;"></p>
                    <u>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Debitur
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </u>
                    <br>
                    <font style="text-transform: uppercase;">{{ $data->nama_nasabah }}</font>
                </td>
            </tr>
        </table>

        Catatan : <br>
        Adapun kelengkapan dokumen yang harus diserahkan saat realisasi kredit adalah : <br>

        <ol>
            <li>{{ Str::upper($data->sebelum_realisasi) }}</li>
            <li>{{ Str::upper($data->syarat_tambahan) }}</li>
            <li>{{ Str::upper($data->syarat_lainnya) }}</li>
        </ol>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
