@extends('templates.app')
@section('title', 'Analisa 5C')
@yield('jquery')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class=" card-header">

                            <div iv class="container-xl">
                                <div div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('jaminan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
                                                class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-left" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l14 0"></path>
                                                    <path d="M5 12l6 6"></path>
                                                    <path d="M5 12l6 -6"></path>
                                                </svg>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">
                            <div class="card">
                                <div class="row g-0">

                                    @include('templates.menu-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col d-flex flex-column">
                                        <div class="card-body">
                                            <form action="#" method="post">
                                                @csrf
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Character
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="30%">Karakter</th>
                                                            <th class="text-center" width="20%">Keterangan</th>
                                                            <th class="text-center" width="30%">Karakter</th>
                                                            <th class="text-center" width="20%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Gaya Hidup" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="gaya_hidup"
                                                                    id="select1">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Terbuka dan Konsisten" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="konsisten"
                                                                    id="select2">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pengendalian Emosi" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="pengendalian"
                                                                    id="select3">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Kepatuhan Terhadap Kewajiban" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="kepatuhan"
                                                                    id="select4">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Melakukan Perbuatan Tercela" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="tercela"
                                                                    id="select5">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Hubungan Sosial dgn Lingkungan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="sosial"
                                                                    id="select6">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Keharmonisan Rumah Tangga" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="harmonis"
                                                                    id="select7">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="3">Baik</option>
                                                                    <option value="2">Cukup Baik</option>
                                                                    <option value="1">Kurang Baik</option>
                                                                </select>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="text"
                                                                    class="form-control bg-primary fw-bold text-white text-center"
                                                                    name="nilai1" id="n1"
                                                                    value="BAIK/ CUKUP BAIK/ KURANG BAIK" readonly>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Capacity
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="26%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                            <th class="text-center" width="25%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="25%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Kontinuitas Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Tidak Tentu">Tidak Tentu</option>
                                                                    <option value="Terus Menerus">Terus Menerus</option>
                                                                    <option value="Kadang-Kadang">Kadang-Kadang</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Kondisi SLIK" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Lancar">Lancar</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="Tidak Baik">Tidak Baik</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pengalaman Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="0 Tahun">0 Tahun</option>
                                                                    <option value="< 1 Tahun">
                                                                        < 1 Tahun</option>
                                                                    <option value="1 - 3 Tahun">1 - 3 Tahun</option>
                                                                    <option value="> 3 - 5 Tahun">> 3 - 5 Tahun</option>
                                                                    <option value="> 5 Tahun">> 5 Tahun</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Aset Diluar Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Liquid">Liquid</option>
                                                                    <option value="Cukup Liquid">Cukup Liquid</option>
                                                                    <option value="Tidak Liquid">Tidak Liquid</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pertumbuhan Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Tetap">Tetap</option>
                                                                    <option value="Turun">Turun</option>
                                                                    <option value="Meningkat">Meningkat</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Aset Terkait Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Mengcover">Mengcover</option>
                                                                    <option value="Cukup Mengcover">Cukup Mengcover
                                                                    </option>
                                                                    <option value="Tidak Mengcover">Tidak Mengcover
                                                                    </option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Catatan Laporan Keuangan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="Catatan Transaksi Harian">Transaksi
                                                                        Harian</option>
                                                                    <option value="Mengumpulkan Bukti">Mengumpulkan Bukti
                                                                    </option>
                                                                </select>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="text"
                                                                    class="form-control bg-primary fw-bold text-white text-center"
                                                                    name="" value="RC : 59.00">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Catatan Kredit Masa Lalu" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Lancar">Lancar</option>
                                                                    <option value="Menunggak > 2 Bulan">Menunggak > 2 Bulan
                                                                    </option>
                                                                    <option value="Lancar Menunggak 2 Bulan">Lancar
                                                                        Menunggak
                                                                        2 Bulan</option>
                                                                </select>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="text"
                                                                    class="form-control bg-primary fw-bold text-white text-center"
                                                                    name="" value="DSR : 646.43">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Capital
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="26%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Sumber Modal" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="sumber_modal"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Modal Sendiri">Modal Sendiri</option>
                                                                    <option value="Kerjasama">Kerjasama</option>
                                                                    <option value="Pihak Lain">Pihak Lain</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Collateral
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="24%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                            <th class="text-center" width="28%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Agunan Utama" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="agunan_utama"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Milik Sendiri</option>
                                                                    <option value="2">Orang Lain/Milik Sendiri dan
                                                                        Orang Lian (Wariasan)</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Agunan Tambahan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="agunan_tambahan"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Milik Sendiri</option>
                                                                    <option value="2">Orang Lain/Milik Sendiri dan
                                                                        Orang Lain (Wariasan)</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Legalitas Agunan Utama" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="lagunan_utama"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Tidak Lengkap Nama
                                                                        Sendiri/Lengkap
                                                                        dan Nama Gabungan </option>
                                                                    <option value="2">Nama Orang Lain </option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Legalitas Agunan Tambahan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="lagunan_tambahan"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Lengkap Atas Nama Sendiri
                                                                    </option>
                                                                    <option value="2">
                                                                        Tidak Lengkap Nama
                                                                        Sendiri/Lengkap dan Nama Gabungan </option>
                                                                    <option value="3">Nama Orang Lain </option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Mudah Diuangkan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="mudah_diuangkan"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Deposito,
                                                                        Tabungan, Emas
                                                                    </option>
                                                                    <option value="2">BPKB, SHM</option>
                                                                    <option value="3">Lainnya</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Stabilitas Harga" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="stabilitas_harga"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">SHM</option>
                                                                    <option value="2">Deposito,Tabungan, Emas</option>
                                                                    <option value="3">BPKB</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Kondisi Kendaraan" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="kondisi_kendaraan"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="2">Original,
                                                                        Lengkap, Tidak Cacat
                                                                    </option>
                                                                    <option value="2">Original, Tidak
                                                                        Lengkap</option>
                                                                    <option value="3">Tidak
                                                                        Original, Tidak Lengkap, Cacat</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Lokasi SHM" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="lokasi_shm"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Strategis dan
                                                                        atau Produktif</option>
                                                                    <option value="2">
                                                                        Strategis dan Produktif (Atau Sebaliknya)
                                                                    </option>
                                                                    <option value="3">
                                                                        Kurang Strategis dan Kurang Produktif</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pengikatan / Aspek Hukum" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="aspek_hukum"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Emas dan deposito/tabungan yang
                                                                        saldonya di blokir dan dilengkapi dengan surat kuasa
                                                                        pencairan</option>
                                                                    <option value="2">SHM (dilengkapi dengan SPPT
                                                                        tahun
                                                                        berjalan atau 1 tahun yang lalu) diikat dengan hak
                                                                        tanggungan / BPKB (Kendaraan) diikat dengan fidusia
                                                                    </option>
                                                                    <option value="3">SHM (dilengkapi dengan SPPT
                                                                        tahun
                                                                        berjalan atau 1 tahun yang lalu) / BPKB tanpa
                                                                        pengikatan
                                                                    </option>
                                                                    <option value="4">AJB / SPOP (dilengkapi dengan
                                                                        SPPT
                                                                        tahun berjalan atau 1 tahun yang lalu) tanpa
                                                                        pengikatan
                                                                        hak</option>
                                                                    <option value="5">Agunan lain yang tidak memenuhi
                                                                        syarat</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control fs-4"
                                                                    name="" value="Permohonan Taksasi Agunan"
                                                                    readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control bg-primary fw-bold text-white text-center"
                                                                    name="permohonan_taksasi" value="15.00">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Conition
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="26%">Rawayat/ Aset</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Persaingan Usaha" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="persaingan_modal"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Persaingan Usaha Tidak Ketat
                                                                    </option>
                                                                    <option value="2">Persaingan Usaha Kurang Ketat
                                                                    </option>
                                                                    <option value="3">Persaingan Usaha Ketat</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Kondisi Alam" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="kondisi_alam"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Resiko Sangat Rendah</option>
                                                                    <option value="2">Resiko Rendah</option>
                                                                    <option value="3">Resiko Sedang</option>
                                                                    <option value="4">Resiko Tinggi</option>
                                                                    <option value="5">Resiko Sangat Tinggi</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Regulasi Pemerintah" readonly="">
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="regulasi_pemerintah"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1">Sangat Mendukung</option>
                                                                    <option value="2">Mendukung</option>
                                                                    <option value="3">Tidak Mendukung</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <button href="#" class="btn btn-primary">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endsection
