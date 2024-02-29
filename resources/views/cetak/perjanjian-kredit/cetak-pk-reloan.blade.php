<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Kredit</title>
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
                font-size: 10pt;
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

                        <h4 style="text-align: center;font-size: 12pt;">
                            <u>PERJANJIAN KREDIT</u> <br>
                            No. <font class="text-hg">{{ $data->no_spk }}</font>
                        </h4>

                        Pada hari ini {{ $data->hari }}, tanggal {{ $data->tgl_bln_thn }} telah disepakati
                        Perjanjian Kredit oleh dan
                        antara :

                        <table>
                            <tr>
                                <td width="2%">I. </td>
                                <td width="15%">Nama</td>
                                <td class="text-center" width="1%"> : </td>
                                <td class="text-hg">{{ $data->nama_nasabah }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Pekerjaan</td>
                                <td class="text-center" width="1%"> : </td>
                                <td>{{ $data->nama_pekerjaan }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Tanda Pengenal</td>
                                <td class="text-center"> : </td>
                                <td>KTP Nomor : <font class="text-hg">{{ $data->no_identitas }}</font>
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
                                    Dalam melakukan tindakan hukum tersebut dibawah ini telah mendapat persetujuan
                                    dari <font class="text-hg">{{ $data->status_pendamping }}</font> bernama
                                    <font class="text-hg">{{ $data->nama_pendamping }}</font> yang ikut serta
                                    menandatangani perjanjian
                                    ini. Untuk selanjutnya
                                    disebut PEMINJAM.
                                </td>
                            </tr>

                            <tr>
                                <td style="vertical-align: text-top;">II. </td>
                                <td colspan="3" style="text-align: justify;">
                                    MOHAMAD MUKSIN dalam hal ini bertindak dalam jabatannya selaku Direktur Utama
                                    berdasarkan Anggaran
                                    Dasar Perseroan Terbatas Bank Perkreditan Rakyat Pamanukan Bangunarta beserta
                                    perubahan-perubahannya
                                    dan yang terakhir termaktub dalam Akta Penegasan Pernyataan Keputusan Rapat
                                    Perseroan Terbatas “PT.
                                    Bank Perekonomian Rakyat Bangunarta” tanggal 30-10-2023 ( tiga puluh oktober dua
                                    ribu dua puluh tiga
                                    ) nomor 58, yang dibuat dihadapan NANA SAPTUNAH ZUHRI, S.H., M.Kn, Notaris
                                    Kabupaten Subang, yang
                                    telah mendapat persetujuan Menteri Hukum dan Hak Asasi Manusia Republik
                                    Indonesia tanggal 31-10-2023
                                    ( tiga puluh satu oktober dua ribu dua puluh tiga ) nomor: AHU-AH.01.09.0179704
                                    Tahun 2023,
                                    karenanya untuk dan atas nama serta sah mewakili Perseroan Terbatas Bank
                                    Perekonomian Rakyat
                                    Bangunarta berkantor pusat di Pamanukan, dengan alamat di Jl. Haji Iksan No. 89,
                                    Desa Mulyasari,
                                    Kecamatan Pamanukan, Kabupaten Subang. Untuk selanjutnya disebut BANK.
                                </td>
                            </tr>
                        </table>

                        <p style="text-align: justify;">
                            PEMINJAM dan BANK secara bersama-sama selanjutnya disebut PARA PIHAK
                        </p>

                        <p style="text-align: justify;">
                            Bahwa guna keperluan {{ $data->penggunaan_debitur }} PEMINJAM telah mengajukan
                            permohonan pinjam uang
                            secara tertulis kepada BANK tanggal {{ $data->tgl_pengajuan }} dan BANK telah memberi
                            persetujuan secara
                            tertulis pada tanggal {{ $data->keputusan_komite }} dengan ketentuan pokok yang telah
                            disetujui PEMINJAM.
                            Ketentuan pokok tersebut akan diuraikan lebih lanjut dalam ketentuan dan syarat-syarat
                            perjanjian kredit
                            dalam pasal-pasal sebagai berikut :
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 1 <br>
                                FASILITAS PINJAMAN
                            </center>
                            BANK setuju untuk memberikan fasilitas pinjaman kepada PEMINJAM berupa pinjaman uang
                            sebesar
                            <font class="text-hg">{{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }}
                            </font> ( <font class="text-hg" style="text-transform: capitalize;">
                                {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->plafon) }}</font> Rupiah)
                            yang akan dipindah
                            bukukan kedalam Rekening Tabungan PEMINJAM yang ada di BANK.
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 2 <br>
                                BUNGA, PROVISI DAN BIAYA ADMINISTRASI
                            </center>
                            Atas pinjaman tersebut diatas, PEMINJAM wajib membayar kepada BANK :
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                <font class="text-hg">Bunga sebesar : {{ $data->suku_bunga }}%</font> per tahun
                                dihitung secara merata
                                setiap bulannya.
                            </li>
                            <li>
                                <font class="text-hg">Provisi sebesar
                                    {{ 'Rp. ' . ' ' . number_format($data->provisi, 0, ',', '.') }},-
                                </font> ( <font class="text-hg" style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->provisi) }}</font>
                                Rupiah) <font class="text-hg">
                                    dan Biaya Administrasi
                                    sebesar {{ 'Rp. ' . ' ' . number_format($data->administrasi, 0, ',', '.') }},
                                </font>- ( <font class="text-hg" style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->administrasi) }}</font>
                                Rupiah) didebetkan
                                dari
                                Rekening Tabungan PEMINJAM yang ada pada BANK.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 3 <br>
                                JANGKA WAKTU DAN ANGSURAN PINJAMAN
                            </center>
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            @if ($data->tgl_jth == '30')
                                @php
                                    $isi = ', untuk bulan Februari maka setoran dilakukan pada tanggal akhir bulan.';
                                @endphp
                            @elseif ($data->tgl_jth == '31')
                                @php
                                    $isi = ", untuk bulan yang berakhir bukan tanggal 31 maka setoran dilakukan pada tanggal
                                akhir bulan.";
                                @endphp
                            @else
                                @php
                                    $isi = '.';
                                @endphp
                            @endif
                            <li>
                                Pembayaran angsuran pokok yang terhutang oleh PEMINJAM kepada BANK wajib dilakukan oleh
                                PEMINJAM
                                secara <font class="text-hg">{{ $data->jangka_pokok }}</font> ( <font class="text-hg"
                                    style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jangka_pokok) }}</font> )
                                bulanan yang
                                dimulai pada tanggal <font class="text-hg">{{ $data->tgl_jth_pokok }}</font> sebanyak
                                <font class="text-hg"> {{ $data->jangka_pokok }}</font> ( <font class="text-hg"
                                    style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jangka_pokok) }}</font> )
                                kali angsuran,
                                selama jangka waktu kredit ( <font class="text-hg">{{ $data->jwt }}</font> bulan),
                                pembayaran bunga
                                kredit yang terhutang oleh PEMINJAM kepada BANK wajib dilakukan oleh
                                PEMINJAM secara @if ($data->jangka_bunga == '1')
                                @else
                                    <font class="text-hg"> {{ $data->jangka_bunga }}</font> ( <font class="text-hg"
                                        style="text-transform: capitalize;">
                                        {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jangka_bunga) }}</font>
                                    )
                                @endif bulanan sebanyak <font class="text-hg">
                                    {{ $data->jumlah_bulan }}</font> (
                                <font class="text-hg" style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jumlah_bulan) }}</font> )
                                kali setiap tanggal <font class="text-hg"> {{ $data->tgl_jth }}</font> ( <font
                                    class="text-hg" style="text-transform: capitalize;">
                                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->tgl_jth) }}</font> )
                                ( selanjutnya disebut tanggal angsuran ) yang dimulai pada tanggal <font
                                    class="text-hg">
                                    {{ $data->tgl_bln_thn_tempo }}</font> dan demikian
                                seterusnya hingga berakhir pada tanggal <font class="text-hg">
                                    {{ $data->tgl_jth_tmp }}</font>{{ $isi }}

                            </li>
                            <li>
                                Jumlah kewajiban angsuran dan setoran bunga diuraikan dalam Rincian Jadwal Angsuran
                                sebagaimana
                                terlampir, yang merupakan suatu kesatuan yang tidak terpisahkan dengan Perjanjian
                                Kredit ini.
                            </li>
                            <li>
                                Semua pembayaran dapat dilakukan PEMINJAM dikantor BANK dengan menunjukkan kartu
                                angsuran dan PEMINJAM
                                akan memperoleh bukti pembayaran dari BANK atau pembayaran dilakukan melalui
                                transfer ke Rekening milik
                                BANK yang tercantum dalam kartu angsuran pinjaman. Apabila PEMINJAM melakukan
                                pembayaran angsuran
                                pinjaman melalui transfer ke rekening Bank yang tercantum dalam kartu angsuran
                                pinjaman maka setelah
                                dana diterima direkening Bank baru dapat diakui sebagai pembayaran angsuran pinjaman
                                dan bukti transfer
                                yang sah dapat diakui sebagai bukti pembayaran angsuran pinjaman.
                            </li>
                        </ol>
                        </p>


                        <p style="text-align: justify;">
                        <ol start="4" style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                PEMINJAM menyetujui bahwa pembukuan BANK selalu menjadi dasar untuk menetapkan jumlah
                                hutang yang wajib
                                dibayar oleh PEMINJAM kepada BANK berdasarkan Perjanjian Kredit ini, baik jumlah pokok,
                                bunga, denda,
                                provisi, biaya teguran/peringatan akibat PEMINJAM ingkar janji, biaya penafsiran,
                                penyimpanan,
                                pemeliharaan serta biaya pemeriksaan barang-barang jaminan, dan PEMINJAM akan menerima
                                baik perhitungan
                                yang dibuat dan diberikan oleh BANK diatas dengan tanpa mengurangi hak PEMINJAM untuk
                                membuktikan
                                sebaliknya, dan apabila ada catatan BANK yang tidak benar, BANK akan melakukan
                                pembetulan.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 4 <br>
                                JAMINAN
                            </center>
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                Untuk menjamin kepastian pembayaran kembali seluruh PINJAMAN baik pokok, bunga dan
                                biaya-biaya lainnya
                                yang timbul dari perjanjian ini, maka PEMINJAM dan atau PENJAMIN menyerahkan jaminan
                                kebendaan yang
                                cukup
                                berupa :
                                <ol
                                    style="text-transform: uppercase;margin-left: -25px; padding-top:5px;padding-bottom: 5px;text-transform:uppercase;">
                                    @forelse ($jaminan as $item)
                                        <font class="text-hg">{{ $item->catatan }}</font> <br>
                                    @empty
                                    @endforelse
                                </ol>
                                Mengenai pengaturan dan pelaksanaan perikatan jaminan akan dilakukan dengan perjanjian
                                tersendiri sesuai
                                peraturan dan perundang-undangan yang berlaku. Perjanjian perikatan agunan tersebut
                                merupakan satu
                                kesatuan dan bagian yang tidak terpisahkan dari Perjanjian Kredit ini.
                            </li>
                            <li>
                                PEMINJAM dan atau PENJAMIN dengan ini menyatakan dengan sebenarnya bahwa barang-barang
                                jaminan tersebut
                                diatas adalah hak milik secara sah dari PEMINJAM dan atau PENJAMIN baik secara hukum
                                maupun fisik dan
                                barang-barang tersebut dikuasai oleh PEMINJAM dan atau PENJAMIN baik sekarang maupun
                                sampai dengan
                                pinjaman tersebut di atas lunas atau pada saat BANK akan melaksanakan ketentuan Pasal 7
                                tersebut di
                                bawah ini, tidak dalam keadaan dijaminkan atas sesuatu hutang pada pihak lain ataupun
                                tidak tersangkut
                                dalam perkara / sengketa baik di dalam maupun di luar pengadilan serta tidak ditaruh
                                dibawah penyitaan
                                jaminan ( conversatoir / revindicatoir beslag ) serta bebas dari segala beban hukum lain
                                yang
                                bagaimanapun sifatnya serta tidak diperoleh dari hasil perbuatan yang bertentangan
                                dengan hukum yang
                                berlaku.
                            </li>
                            <li>
                                PEMINJAM dan atau PENJAMIN dengan ini berjanji tidak akan menyewakan, menjual,
                                menghilangkan dan
                                memindahtangankan kepada pihak ketiga asset-asset yang berhubungan dengan
                                jaminan-jaminan yang telah dan
                                akan diserahkan kepada BANK tanpa persetujuan tertulis terlebih dahulu dari BANK.
                                Setelah pinjaman
                                dinyatakan lunas oleh BANK atau berdasarkan pertimbangan BANK barang agunan pada pasal 4
                                ayat 1 ini
                                sudah tidak diperlukan lagi sebagai jaminan kredit, BANK wajib mengembalikan bukti-bukti
                                pemilikan
                                barang agunan tersebut kepada PEMINJAM.
                            </li>
                            <li>
                                Bilamana barang jaminan pada Pasal 4 Ayat 1 diatas hilang, musnah, berkurang nilainya
                                baik sebagian
                                maupun seluruhnya, atau karena suatu hal berakhir penguasaannya oleh pihak yang
                                berwenang, maka PEMINJAM
                                berkewajiban dan bersedia mengganti dengan barang jaminan apapun lainnya yang nilainya
                                oleh BANK
                                dianggap cukup untuk melunasi hutang dan seluruh kewajiban PEMINJAM terhadap BANK.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 5 <br>
                                DENDA
                            </center>
                            Dalam hal PEMINJAM tidak membayar angsuran dan setoran bunga tepat pada waktunya sebagaimana
                            telah
                            ditentukan dalam pasal (2) perjanjian kredit ini, oleh sebab itu PEMINJAM dikenakan denda
                            sebesar
                            <font class="text-hg">{{ $data->b_denda }} %</font>
                            perhari keterlambatan dari jumlah angsuran dan setoran bunga yang menjadi kewajibannya.
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 6 <br>
                                PELUNASAN SEBELUM JATUH TEMPO
                            </center>
                            PEMINJAM berhak untuk melunasi pinjaman sewaktu-waktu sebelum jatuh tempo pada hari dan jam
                            kerja, melunasi
                            baik pokok, bunga, hutang denda dan biaya-biaya yang sudah terjadi yang timbul karena adanya
                            perjanjian ini,
                            sesuai jadwal angsuran pinjaman ditambah dengan penalti pelunasan sebesar <font
                                class="text-hg">3,00%</font>
                            dari fasilitas kredit /
                            plafon kredit.
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 7 <br>
                                KEWENANGAN BANK
                            </center>
                            PEMINJAM karena sebab apapun juga masih berhutang kepada BANK, dengan ini menyetujui dan
                            mengizinkan BANK:
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                Memeriksa keadaan usaha PEMINJAM dan memasuki atau memeriksa keberadaan atau fisik
                                agunan-agunan baik
                                dalam rangka penilaian kembali maupun dalam rangka pemeriksaan dan pengawasan
                                (monitoring), apabila hal
                                ini dianggap perlu oleh BANK.
                            </li>
                            <li>
                                Menelepon dan/atau mengunjungi PEMINJAM atau pasangan PEMINJAM tanpa pembatasan
                                jam/waktu dan hari
                                pada nomor telepon PEMINJAM atau pasangan PEMINJAM pada domisili yang telah ditetapkan
                                dalam Perjanjian
                                Kredit ini, atau pada tempat/domisili senyatanya PEMINJAM atau pengganti PEMINJAM atau
                                pada tempat
                                tinggal lain yang diketahui oleh BANK.
                            </li>
                            <li>
                                Apabila PEMINJAM sulit dihubungi atau sulit ditemui pada nomor telepon PEMINJAM/pasangan
                                PEMINJAM
                                atau pada domisili yang telah dipilih dalam Perjanjian Kredit ini atau domisili nyata
                                PEMINJAM yang
                                diketahui oleh BANK, BANK dapat menghubungi dan/atau menemui PEMINJAM/pasangan PEMINJAM
                                pada nomor atau
                                pada tempat kerja/perusahaan PEMINJAM/pasangan PEMINJAM, satu dan lain untuk menjaga
                                kesinambungan
                                komunikasi atau efektivitas pemberitahuan/penyampaian informasi yang berkaitan dengan
                                pinjaman,
                                pengelolaan pinjaman dan/atau jaminan/agunan, atau proses penyelesaian pinjaman/agunan.
                            </li>
                        </ol>
                        </p>


                        <p style="text-align: justify;">
                            <center>
                                Pasal 8 <br>
                                KEADAAN INGKAR JANJI
                            </center>
                            PEMINJAM dengan ini mengijinkan kepada BANK bahwa selama PEMINJAM karena sebab apapun juga
                            masih berhutang
                            kepada BANK maka PEMINJAM :
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                PEMINJAM menyatakan semua data dan informasi yang diberikannya pada Bank adalah benar
                                dan PEMINJAM
                                benjanji untuk melaksanakan semua kewajibannya terkait pinjamannya ini dengan baik,
                                namun apabila
                                ternyata :
                                <ol type="a" style="margin-left: -25px;">

                                    <li>
                                        PEMINJAM tidak membayar baik pokok dan atau bunga sesuai jadwal angsuran, atau
                                    </li>
                                    <li>
                                        PEMINJAM tidak bisa melunasi seluruh pinjamannya tepat pada waktunya, atau
                                    </li>
                                    <li>
                                        PEMINJAM melanggar dan atau tidak melaksanakan kewajiban yang disyaratkan dalam
                                        perjanjian ini.
                                    </li>
                                </ol>
                                Maka PARA PIHAK sepakat menyatakan PEMINJAM dalam keadaan ingkar janji.
                            </li>
                            <li>
                                Apabila PEMINJAM telah ingkar janji maka BANK berhak melakukan kunjungan atau penagihan
                                melalui
                                telepon dan/atau kunjungan dan/atau dengan mengirimkan surat pemberitahuan dan/atau
                                surat panggilan
                                dan/atau surat teguran dan/atau surat peringatan dan/atau surat somasi kepada PEMINJAM.
                            </li>

                            <li>
                                Bilamana PEMINJAM dalam keadaan ingkar janji, maka PEMINJAM setuju bahwa BANK berhak
                                untuk melakukan
                                tindakan hukum yang diperlukan sesuai ketentuan yang berlaku, baik yang diatur dalam
                                perjanjian ini,
                                maupun yang diatur oleh Undang-Undang terkait dengan jaminan / agunan.
                            </li>
                            <li>
                                Tindakan hukum sebagaimana Pasal 8 ayat 3 dalam perjanjian ini termasuk namun tidak
                                terbatas diantaranya
                                pengalihan piutang atasnama PEMINJAM oleh BANK kepada Kreditur Baru.
                            </li>
                            <!-- JIKA AGUNAN KENDARAAN DAN TANAH GUNAKAN SEMUA -->

                            <!-- JIKA AGUNAN KENDARAAN SAJA -->
                            @forelse ($agunan as $item)
                                @if ($item->jenis_jaminan == 'Kendaraan')
                                    <li>
                                        Apabila PEMINJAM dalam keadaan ingkar janji, maka PEMINJAM setuju bahwa BANK
                                        berhak melakukan
                                        pengamanan
                                        agunan untuk disimpan di kantor Bank sampai adanya pembayaran Kredit.
                                    </li>
                                @endif

                                <!-- JIKA AGUNAN TANAH SAJA -->
                                @if ($item->jenis_jaminan == 'Tanah')
                                    <li>
                                        Apabila PEMINJAM dalam keadaan ingkar janji, maka PEMINJAM setuju dan memberi
                                        ijin kepada BANK
                                        untuk
                                        melakukan pemasangan papan / pemberitahuan didepan rumah dan atau di atas tanah
                                        agunan dengan
                                        tulisan
                                        "RUMAH DAN / TANAH INI MERUPAKAN JAMINAN PINJAMAN DI PT BPR BANGUNARTA".
                                    </li>
                                @endif

                            @empty
                            @endforelse

                            <li>
                                Bahwa PEMINJAM dengan ini menyetujui dan sepakat untuk memberikan hak sepenuhnya kepada
                                BANK untuk
                                menyerahkan piutang (Cessie) dan/atau tagihan BANK terhadap PEMINJAM berikut semua
                                janji-janji
                                accesoir-nya termasuk hak-hak atas jaminan kredit kepada pihak lain yang ditetapkan BANK
                                sendiri setiap
                                saat jika diperlukan oleh BANK.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 9 <br>
                                KEPASTIAN PEMINJAM
                            </center>
                            Bahwa PEMINJAM selama perjanjian kredit tidak tersangkut dalam perkara / sengketa berupa apa
                            pun juga di
                            muka pengadilan dan / atau instansi-instansi lainnya yang dapat mengancam harta kekayaan
                            PEMINJAM dan dapat
                            mempengaruhi kemampuan PEMINJAM untuk melaksanakan kewajiban-kewajibanya yang termaksud
                            dalam perjanjian
                            kredit ini. Apabila hal-hal seperti tersebut terjadi, maka BANK diprioritaskan pembayarannya
                            dari hasil
                            penjualan barang jaminan, hasil lelang dan kepailitan, selain yang ada berdasarkan
                            perjanjian penyerahan
                            jaminan.
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 10 <br>
                                ASURANSI
                            </center>
                            PEMINJAM mengetahui dan setuju bahwa penutupan asuransi terkait dengan pinjaman ini, pada
                            polisnya akan
                            dipasang syarat BANKER’s Clouse yaitu apabila ada pembayaran dari asuransi akan diterima
                            terlebih dahulu
                            oleh BANK untuk membayar jumlah seluruh hutang PEMINJAM, apabila ada kelebihan akan
                            dikembalikan pada
                            PEMINJAM, apabila terjadi kekurangan maka BANK berhak menagih kekurangannya pada PEMINJAM.
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 11 <br>
                                DOMISILI DAN PEMBERITAHUAN
                            </center>
                            PARA PIHAK dengan ini menyatakan bahwa :
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                Alamat BANK dan PEMINJAM sebagaimana tercantum pada awal Perjanjian Kredit ini merupakan
                                alamat tetap
                                bagi masing-masing PIHAK, dan secara sah dipergunakan untuk segala surat menyurat atau
                                komunikasi
                                diantara PARA PIHAK.
                            </li>
                            <li>
                                Apabila ada perubahan alamat maka PARA PIHAK wajib memberitahukan secara tertulis alamat
                                barunya kepada
                                pihak lainnya paling lambat 7 ( Tujuh ) hari sejak terjadinya perubahan alamat.
                            </li>
                            <li>
                                Selama tidak terdapat pemberitahuan tentang perubahan alamat sebagaimana dimaksud pada
                                ayat 2 pasal ini,
                                maka untuk surat menyurat atau komunikasi yang dilakukan ke alamat yang tercantum pada
                                awal Perjanjian
                                Kredit dianggap sah menurut hukum.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 12 <br>
                                DOMISILI HUKUM YANG BERLAKU
                            </center>
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                Dalam hal terjadi perbedaan pendapat dalam memahami atau menafsirkan bagian-bagian dari
                                isi perjanjian
                                atau terjadi perselisihan dalam melaksanakan perjanjian ini, maka PARA PIHAK sepakat
                                untuk menyelesaikan
                                secara musyawarah dan mufakat.
                            </li>
                            <li>
                                Apabila penyelesaian secara musyawarah dan mufakat tidak dapat menyelesaikan
                                permasalahan maka PARA
                                PIHAK sepakat untuk memilih domisili hukum di kantor Panitera Pengadilan Negeri Subang.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            <center>
                                Pasal 13 <br>
                                KETENTUAN PASAL PENUTUP
                            </center>
                        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
                            <li>
                                Perjanjian kredit ini merupakan perjanjian yang tidak dapat dipisahkan dengan perjanjian
                                yang dibuat
                                dikemudian hari oleh PARA PIHAK baik berupa penambahan, perpanjangan maupun
                                perubahan-perubahan lainnya
                                dan kuasa-kuasa yang dibuat tersendiri dari perjanjian kredit ini adalah merupakan
                                bagian terpenting dan
                                tidak dapat dipisah-pisahkan dari perjanjian ini yang tidak akan dibuat tanpa adanya
                                kuasa-kuasa
                                tersebut. Dan karenanya kuasa-kuasa tersebut dan kuasa yang ada dalam surat perjanjian
                                ini tidak dapat
                                dicabut kembali dan tidak akan berakhir karena sebab-sebab apapun juga, selama
                                perjanjian ini
                                berlangsung dan selama PEMINJAM belum melunasi seluruh hutangnya kepada BANK.
                            </li>
                            <li>
                                PEMINJAM telah membaca dan memahami seluruh ketentuan yang ada dalam Perjanjian Kredit
                                dan Syarat dan
                                Ketentuan Umum Pemberian Fasilitas Kredit serta PEMINJAM memperoleh informasi yang jelas
                                dan benar
                                tentang Fasilitas kredit yang diberikan oleh BANK kepada PEMINJAM.
                            </li>
                            <li>
                                Perjanjian kredit ini telah disesuaikan dengan Ketentuan Perundangan-undangan termasuk
                                Ketentuan
                                Peraturan Otorisasi Jasa Keuangan.
                            </li>
                        </ol>
                        </p>

                        <p style="text-align: justify;">
                            Demikian perjanjian kredit ini dibuat, disetujui dan ditandatangani dalam keadaan sadar,
                            sehat jasmani dan
                            rohani dengan sebenar-benarnya tanpa ada paksaan dari pihak manapun oleh PARA PIHAK di
                            Pamanukan serta
                            dibuat salinan perjanjian ini bagi kedua belah pihak.
                        </p>

                        <table style="margin-top: -25px;">
                            <tr>
                                <td class="text-center" width="40%">
                                    B&nbsp;A&nbsp;N&nbsp;K
                                    <p style="margin-top:95px;"></p>
                                    MOHAMAD MUKSIN
                                    <br>
                                    &nbsp;
                                </td>
                                <td></td>
                                <td class="text-center" width="40%">
                                    <br>
                                    <br>
                                    P&nbsp;E&nbsp;M&nbsp;I&nbsp;N&nbsp;J&nbsp;A&nbsp;M
                                    <p style="margin-top:95px;"></p>
                                    <u style="text-transform: uppercase;">
                                        <font style="text-transform: uppercase;">{{ $data->nama_nasabah }}</font>
                                    </u>
                                    <br>
                                    <br>
                                    <br>
                                    M&nbsp;E&nbsp;N&nbsp;Y&nbsp;E&nbsp;T&nbsp;U&nbsp;J&nbsp;U&nbsp;I
                                </td>
                            </tr>

                            <tr>
                                <td class="text-center" width="40%">
                                    <p style="margin-top:80px;"></p>
                                    &nbsp;
                                    <br>
                                    SAKSI 2, <br>
                                    <p style="margin-top:60px;"></p>
                                    (<u>...........................................</u>)
                                </td>
                                <td></td>
                                <td class="text-center" width="40%">
                                    <p style="margin-top:80px;"></p>
                                    <u style="text-transform: uppercase;">
                                        <font style="text-transform: uppercase;">{{ $data->nama_pendamping }}</font>
                                    </u>
                                    <br>
                                    SAKSI 1, <br>
                                    <p style="margin-top:65px;"></p>
                                    (<u>...........................................</u>)
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
