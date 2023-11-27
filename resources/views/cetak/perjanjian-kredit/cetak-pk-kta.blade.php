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
            margin-bottom: 0.5cm;
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
    <!-- Halaman 1 -->
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <h4 style="text-align: center;font-size: 12pt;">
            <u>PERJANJIAN KREDIT</u> <br>
            No. {{ $data->no_spk }}
        </h4>

        Pada hari ini {{ $data->hari }}, tanggal {{ $data->tgl_bln_thn }} telah disepakati Perjanjian Kredit oleh dan
        antara :

        <table>
            <tr>
                <td width="2%">I. </td>
                <td width="15%">Nama</td>
                <td class="text-center" width="1%"> : </td>
                <td>{{ $data->nama_nasabah }}</td>
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
                <td>KTP Nomor : {{ $data->no_identitas }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="vertical-align: text-top;">Alamat</td>
                <td class="text-center" style="vertical-align: text-top;"> : </td>
                <td>{{ $data->alamat_ktp }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="text-align: justify;">
                    Dalam melakukan tindakan hukum tersebut dibawah ini telah mendapat persetujuan dari
                    {{ $data->status_pendamping }} {{ $data->nama_pendamping }} yang ikut serta menandatangani
                    perjanjian ini yang kapasitasnya sebagai Ketua Serikat Pekerja
                    Tingkat Perusahaan {{ $data->tempat_kerja }}. Untuk selanjutnya disebut PEMINJAM.
                </td>
            </tr>

            <tr>
                <td style="vertical-align: text-top;">II. </td>
                <td colspan="3" style="text-align: justify;">
                    MOHAMAD MUKSIN dalam hal ini bertindak dalam jabatannya selaku Direktur Utama berdasarkan Anggaran
                    Dasar Perseroan Terbatas Bank Perkreditan Rakyat Pamanukan Bangunarta beserta perubahan-perubahannya
                    dan yang terakhir termaktub dalam Akta Penegasan Pernyataan Keputusan Rapat Perseroan Terbatas “PT.
                    Bank Perekonomian Rakyat Bangunarta” tanggal 30-10-2023 ( tiga puluh oktober dua ribu dua puluh tiga
                    ) nomor 58, yang dibuat dihadapan NANA SAPTUNAH ZUHRI, S.H., M.Kn, Notaris Kabupaten Subang, yang
                    telah mendapat persetujuan Menteri Hukum dan Hak Asasi Manusia Republik Indonesia tanggal 31-10-2023
                    ( tiga puluh satu oktober dua ribu dua puluh tiga ) nomor: AHU-AH.01.09.0179704 Tahun 2023,
                    karenanya untuk dan atas nama serta sah mewakili Perseroan Terbatas Bank Perekonomian Rakyat
                    Bangunarta berkantor pusat di Pamanukan, dengan alamat di Jl. Haji Iksan No. 89, Desa Mulyasari,
                    Kecamatan Pamanukan, Kabupaten Subang. Untuk selanjutnya disebut BANK.
                </td>
            </tr>
        </table>

        <p style="text-align: justify;">
            PEMINJAM dan BANK secara bersama-sama selanjutnya disebut PARA PIHAK.
        </p>

        <p style="text-align: justify;">
            Bahwa guna keperluan {{ $data->penggunaan_debitur }} PEMINJAM telah mengajukan permohonan pinjam uang
            secara tertulis kepada BANK tanggal {{ $data->tgl_pengajuan }} dan BANK telah memberi persetujuan secara
            tertulis pada tanggal {{ $data->tgl_bln_thn }} dengan ketentuan pokok yang telah disetujui PEMINJAM.
            Ketentuan pokok tersebut akan diuraikan lebih lanjut dalam ketentuan dan syarat-syarat perjanjian kredit
            dalam pasal-pasal sebagai berikut :
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 1 <br>
                FASILITAS PINJAMAN
            </center>
            BANK setuju untuk memberikan fasilitas pinjaman kepada PEMINJAM berupa pinjaman uang sebesar
            {{ 'Rp. ' . ' ' . number_format($data->plafon, 0, ',', '.') }} ( <font style="text-transform: capitalize;">
                {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->plafon) }}</font> ) yang akan dipindah
            bukukan kedalam Rekening Tabungan PEMINJAM yang ada di BANK.
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 2 <br>
                BUNGA
            </center>
            Atas pinjaman tersebut diatas, PEMINJAM wajib membayar kepada BANK dengan Bunga sebesar :
            {{ $data->suku_bunga }} % Flate per
            tahun dihitung secara merata setiap bulannya.
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 3 <br>
                JANGKA WAKTU DAN ANGSURAN PINJAMAN
            </center>
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                Pembayaran angsuran pokok berikut bunga atas jumlah kredit yang terhutang oleh PEMINJAM kepada BANK (
                selanjutnya disebut angsuran ) wajib dilakukan oleh PEMINJAM secara bulanan dalam
                {{ $data->jangka_waktu }} ( <font style="text-transform: capitalize;">
                    {{ Riskihajar\Terbilang\Facades\Terbilang::make($data->jangka_waktu) }}</font> ) kali
                angsuran setiap tanggal 02 ( selanjutnya disebut tanggal angsuran ) yang dimulai pada​tanggal 02
                November 2020 dan demikian seterusnya hingga berakhir pada tanggal 02 Oktober 2021.
            </li>
            <li>
                Jumlah kewajiban angsuran dan setoran bunga diuraikan dalam Rincian Jadwal Angsuran sebagaimana
                terlampir, yang merupakan suatu kesatuan yang tidak terpisahkan dengan Perjanjian Kredit ini.
            </li>
            <li>
                Semua pembayaran dapat dilakukan PEMINJAM dikantor BANK dengan menunjukkan kartu angsuran dan PEMINJAM
                akan memperoleh bukti pembayaran dari BANK atau pembayaran dilakukan melalui transfer ke Rekening milik
                BANK yang tercantum dalam kartu angsuran pinjaman. Apabila PEMINJAM melakukan pembayaran angsuran
                pinjaman melalui transfer ke rekening Bank yang tercantum dalam kartu angsuran pinjaman maka setelah
                dana diterima direkening Bank baru dapat diakui sebagai pembayaran angsuran pinjaman dan bukti transfer
                yang sah dapat diakui sebagai bukti pembayaran angsuran pinjaman.
            </li>
            <li>
                PEMINJAM menyetujui bahwa pembukuan BANK selalu menjadi dasar untuk menetapkan jumlah hutang yang wajib
                dibayar oleh PEMINJAM kepada BANK berdasarkan Perjanjian Kredit ini, baik jumlah pokok, bunga, denda,
                provisi, biaya teguran/peringatan akibat PEMINJAM ingkar janji, biaya penafsiran, penyimpanan,
                pemeliharaan serta
            </li>
        </ol>
        </p>

    </div>

    <!-- Halaman 2 -->
    <div style="page-break-before: always;"></div>
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <p style="text-align: justify;">
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;list-style-type: none;">
            <li>
                biaya pemeriksaan barang-barang jaminan, dan PEMINJAM akan menerima baik perhitungan yang dibuat dan
                diberikan oleh BANK diatas dengan tanpa mengurangi hak PEMINJAM untuk membuktikan sebaliknya, dan
                apabila ada catatan BANK yang tidak benar, BANK akan melakukan pembetulan.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 4 <br>
                DENDA
            </center>
            Dalam hal PEMINJAM tidak membayar angsuran dan setoran bunga tepat pada waktunya sebagaimana telah
            ditentukan dalam Pasal 3 Ayat 2 perjanjian kredit ini, oleh sebab itu PEMINJAM dikenakan denda sebesar
            {{ $data->b_penalti }} %
            perhari keterlambatan dari jumlah angsuran dan setoran bunga yang menjadi kewajibannya.
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 5 <br>
                PELUNASAN SEBELUM JATUH TEMPO
            </center>
            PEMINJAM berhak untuk melunasi pinjaman sewaktu-waktu sebelum jatuh tempo pada hari dan jam kerja dengan
            cara melunasi seluruh sisa pokok dan bunga yang di hitung sesuai dengan jangka waktu pinjaman serta hutang
            denda.
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 6 <br>
                PEMANTAUAN PINJAMAN
            </center>
            PEMINJAM dengan ini mengijinkan kepada BANK bahwa selama PEMINJAM karena sebab apapun juga masih berhutang
            kepada BANK maka PEMINJAM :
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                PEMINJAM bersedia untuk melakukan pembayaran angsuran sebagaimana dimaksud pada Pasal 3 Ayat 1 dengan
                cara pemotongan gaji PEMINJAM.
            </li>
            <li>
                Untuk keperluan tersebut pada Pasal 6 Ayat 1 di atas, PEMINJAM akan menyerahkan kepada BANK, Surat Kuasa
                Pemotongan Gaji dari PEMINJAM kepada Pihak Yang ditunjuk oleh BANK, termasuk tetapi tidak terbatas pada
                pemotongan gaji bulanan dan tunjangan-tunjangan lainnya.
            </li>
            <li>
                PEMINJAM memberi kuasa/hak kepada BANK untuk :
                <ol type="a" style="margin-left: -25px;">
                    <li>
                        Mengambil/memotong gaji bulanan PEMINJAM dengan jumlah yang sesuai dan telah diatur pada Pasal 3
                        Ayat perjanjian ini, dan/atau dengan jumlah yang merupakan total kewajiban pembayaran PEMINJAM
                        kepada BANK apabila PEMINJAM melakukan kelalaian atau wanprestasi.
                    </li>
                    <li>
                        Menghadap kepada pihak yang berkompeten untuk meminta informasi tentang penghasilan PEMINJAM dan
                        informasi lain yang dinilai perlu dan berhubungan dengan kredit PEMINJAM pada BANK.
                    </li>
                </ol>
            </li>
            <li>
                PEMINJAM mengizinkan wakil-wakil BANK yang sah untuk memeriksa keadaan usaha PEMINJAM
            </li>
            <li>
                PEMINJAM akan menyampaikan kepada BANK, informasi yang sebenar-benarnya dan selengkap-lengkapnya tentang
                kondisi usaha dan atau perubahan-perubahan atas barang jaminan.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 7 <br>
                KEADAAN INGKAR JANJI
            </center>
            PEMINJAM dengan ini mengijinkan kepada BANK bahwa selama PEMINJAM karena sebab apapun juga masih berhutang
            kepada BANK maka PEMINJAM :
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                PEMINJAM menyatakan semua data dan informasi yang diberikannya pada Bank adalah benar dan PEMINJAM
                benjanji untuk melaksanakan semua kewajibannya terkait pinjamannya ini dengan baik, namun apabila
                ternyata :
                <ol type="a" style="margin-left: -25px;">
                    <li>
                        PEMINJAM tidak membayar baik pokok dan atau bunga sesuai jadwal angsuran, atau
                    </li>
                    <li>
                        PEMINJAM tidak bisa melunasi seluruh pinjamannya tepat pada waktunya, atau
                    </li>
                    <li>
                        PEMINJAM melanggar dan atau tidak melaksanakan kewajiban yang disyaratkan dalam perjanjian ini.
                        Maka
                    </li>
                </ol>
                PARA PIHAK sepakat menyatakan PEMINJAM dalam keadaan ingkar janji.
            </li>
            <li>
                Apabila PEMINJAM telah ingkar janji maka BANK berhak melakukan penagihan melalui telephone dan atau
                kunjungan ke PEMINJAM dan atau dengan mengirimkan surat pemberitahuan dan atau surat panggilan dan atau
                surat teguran dan atau surat peringatan dan atau surat somasi kepada PEMINJAM.
            </li>
            <li>
                Apabila PEMINJAM dalam keadaan ingkar janji, maka PEMINJAM setuju bahwa BANK berhak melakukan pemotongan
                gaji bulanan dan tunjangan-tunjangan lain milik PEMINJAM, sesuai jumlah kewajiban yang harus di bayar
                dan atau melakukan tindakan hukum yang diperlukan sesuai ketentuan yang berlaku, baik yang diatur dalam
                perjanjian ini, maupun dalam peraturan perundang-undangan yang berlaku.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 8 <br>
                ASURANSI
            </center>
            PEMINJAM mengetahui dan setuju bahwa tidak terdapat penutupan asuransi terkait dengan pinjaman ini, apabila
            terjadi sesuatu hal kepada PEMINJAM yang dimana PEMINJAM meninggal dunia, maka BANK berhak menagih
            kekurangan sisa pembayaran angsuran pada ahli waris / keluarga PEMINJAM.
        </p>
    </div>

    <!-- Halaman 3 -->
    <div style="page-break-before: always;"></div>
    <div class="content" style="margin-top: -57px;">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <p style="text-align: justify;">
            <center>
                Pasal 9 <br>
                DOMISILI DAN PEMBERITAHUAN
            </center>
            Para PIHAK dengan ini menyatakan bahwa :
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                Alamat BANK dan PEMINJAM sebagaimana tercantum pada awal Perjanjian Kredit ini merupakan alamat tetap
                bagi masing-masing PIHAK, dan secara sah dipergunakan untuk segala surat menyurat atau komunikasi
                diantara PARA PIHAK.
            </li>
            <li>
                Apabila ada perubahan alamat maka para PIHAK wajib memberitahukan secara tertulis alamat barunya kepada
                pihak lainnya paling lambat 7 ( Tujuh ) hari sejak terjadinya perubahan alamat.
            </li>
            <li>
                Selama tidak terdapat pemberitahuan tentang perubahan alamat sebagaimana dimaksud pada ayat 2 pasal ini,
                maka untuk surat menyurat atau komunikasi yang dilakukan ke alamat yang tercantum pada awal Perjanjian
                Kredit dianggap sah menurut hukum.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 10 <br>
                DOMISILI HUKUM YANG BERLAKU
            </center>
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                Dalam hal terjadi perbedaan pendapat dalam memahami atau menafsirkan bagian-bagian dari isi perjanjian
                atau terjadi perselisihan dalam melaksanakan perjanjian ini, maka para PIHAK sepakat untuk menyelesaikan
                secara musyawarah dan mufakat.
            </li>
            <li>
                Apabila penyelesaian secara musyawarah dan mufakat tidak dapat menyelesaikan permasalahan maka para
                PIHAK sepakat untuk memilih domisili hukum di kantor Panitera Pengadilan Negeri Subang.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            <center>
                Pasal 11 <br>
                KETENTUAN PASAL PENUTUP
            </center>
        <ol style="text-align: justify;margin-top:-1px;margin-left: -25px;">
            <li>
                Perjanjian kredit ini merupakan perjanjian yang tidak dapat dipisahkan dengan perjanjian yang dibuat
                dikemudian hari oleh PARA PIHAK baik berupa penambahan, perpanjangan maupun perubahan-perubahan lainnya
                dan kuasa-kuasa yang dibuat tersendiri dari perjanjian kredit ini adalah merupakan bagian terpenting dan
                tidak dapat dipisah-pisahkan dari perjanjian ini yang tidak akan dibuat tanpa adanya kuasa-kuasa
                tersebut. Dan karenanya kuasa-kuasa tersebut dan kuasa yang ada dalam surat perjanjian ini tidak dapat
                dicabut kembali dan tidak akan berakhir karena sebab-sebab apapun juga, selama perjanjian ini
                berlangsung dan selama PEMINJAM belum melunasi seluruh hutangnya kepada BANK.
            </li>
            <li>
                PEMINJAM telah membaca dan memahami seluruh ketentuan yang ada dalam Perjanjian Kredit dan Syarat dan
                Ketentuan Umum Pemberian Fasilitas Kredit serta PEMINJAM memperoleh informasi yang jelas dan benar
                tentang Fasilitas kredit yang diberikan oleh BANK kepada PEMINJAM.
            </li>
            <li>
                Perjanjian kredit ini telah disesuaikan dengan Ketentuan Perundangan-undangan termasuk Ketentuan
                Peraturan Otorisasi Jasa Keuangan.
            </li>
        </ol>
        </p>

        <p style="text-align: justify;">
            Demikian perjanjian kredit ini dibuat, disetujui dan ditandatangani dalam keadaan sadar, sehat jasmani dan
            rohani dengan sebenar-benarnya tanpa ada paksaan dari pihak manapun oleh PARA PIHAK di Pamanukan serta
            dibuat salinan perjanjian ini bagi kedua belah pihak.
        </p>

        <table>
            <tr>
                <td class="text-center" width="40%">
                    B&nbsp;A&nbsp;N&nbsp;K
                    <p style="margin-top:70px;"></p>
                    MOHAMAD MUKSIN
                    <br>
                    &nbsp;
                </td>
                <td></td>
                <td class="text-center" width="40%">
                    P&nbsp;E&nbsp;M&nbsp;I&nbsp;N&nbsp;J&nbsp;A&nbsp;M
                    <p style="margin-top:70px;"></p>
                    <u style="text-transform: uppercase;">
                        <font style="text-transform: uppercase;">{{ $data->nama_nasabah }}</font>
                    </u>
                    <br>
                    M&nbsp;E&nbsp;N&nbsp;Y&nbsp;E&nbsp;T&nbsp;U&nbsp;J&nbsp;U&nbsp;I
                </td>
            </tr>

            <tr>
                <td class="text-center" width="40%">
                    <br><br>
                    &nbsp;
                    <br>
                    SAKSI 2, <br>
                    <p style="margin-top:70px;"></p>
                    (<u>...........................................</u>)
                </td>
                <td></td>
                <td class="text-center" width="40%">
                    <br><br>
                    <u style="text-transform: uppercase;">
                        <font style="text-transform: uppercase;">{{ $data->nama_pendamping }}</font>
                    </u>
                    <br>
                    SAKSI 1, <br>
                    <p style="margin-top:70px;"></p>
                    (<u>...........................................</u>)
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>