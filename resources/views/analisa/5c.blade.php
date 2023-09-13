@extends('templates.app')
@section('title', 'Analisa 5C')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class=" card-header">

                            <div iv class="container-xl">
                                <div div class="row g-2 align-items-center">

                                    {{-- @include('templates.header-analisa') --}}

                                    <div class="col">
                                        <h1 class="fw-bold">YANDI ROSYANDI</h1>
                                        <div class="my-2"></div>
                                        <div class="list-inline list-inline-dots text-muted">
                                            <div class="list-inline-item">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-database-dollar" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3">
                                                    </path>
                                                    <path d="M4 6v6c0 1.657 3.582 3 8 3c.415 0 .822 -.012 1.22 -.035">
                                                    </path>
                                                    <path d="M20 10v-4"></path>
                                                    <path d="M4 12v6c0 1.657 3.582 3 8 3c.352 0 .698 -.009 1.037 -.025">
                                                    </path>
                                                    <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5"></path>
                                                    <path d="M19 21v1m0 -8v1"></path>
                                                </svg>
                                                Rp. 1.000.000
                                            </div>
                                            <div class="list-inline-item">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-calendar-time" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4">
                                                    </path>
                                                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                                    <path d="M15 3v4"></path>
                                                    <path d="M7 3v4"></path>
                                                    <path d="M3 11h16"></path>
                                                    <path d="M18 16.496v1.504l1 1"></path>
                                                </svg>
                                                36 Bulan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pertanian.index') }}" class="btn btn-primary">
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

                                    {{-- @include('templates.menu-analisa') --}}

                                    <div class="col-3 d-none d-md-block border-end">
                                        <div class="card-body">
                                            <div class="list-group list-group-transparent">
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/perdagangan', 'analisa/usaha/perdagangan/detail') ? 'active' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Usaha Perdagangan
                                                </a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/pertanian', 'analisa/usaha/pertanian/detail') ? 'active' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Usah Pertanian
                                                </a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/jasa', 'analisa/usaha/jasa/detail') ? 'active' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Usaha Jasa
                                                </a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/lainnya', 'analisa/usaha/lainnya/detail') ? 'active' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Usaha Lainnya
                                                </a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/keuangan') ? 'active' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Kemampuan Keuangan</a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Harta Kepemilikan
                                                </a>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex align-items-center active">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Analisa 5C
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <div class="card-body">
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
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Terbuka dan Konsisten" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pengendalian Emosi" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kepatuhan Terhadap Kewajiban" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Melakukan Perbuatan Tercela" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Hubungan Sosial dgn Lingkungan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Keharmonisan Rumah Tangga" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Baik">Baik</option>
                                                                <option value="Cukup Baik">Cukup Baik</option>
                                                                <option value="Kurang Baik">Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td colspan="2">
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="" value="BAIK/ CUKUP BAIK/ KURANG BAIK">
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
                                                            <select class="form-control" name="" id="">
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
                                                            <select class="form-control" name="" id="">
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
                                                            <select class="form-control" name="" id="">
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
                                                            <select class="form-control" name="" id="">
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
                                                            <select class="form-control" name="" id="">
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
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Mengcover">Mengcover</option>
                                                                <option value="Cukup Mengcover">Cukup Mengcover</option>
                                                                <option value="Tidak Mengcover">Tidak Mengcover</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Catatan Laporan Keuangan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Tidak Ada">Tidak Ada</option>
                                                                <option value="Catatan Transaksi Harian">Transaksi
                                                                    Harian</option>
                                                                <option value="Mengumpulkan Bukti">Mengumpulkan Bukti
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Sumber Permodalan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Pihak Lain">Pihak Lain</option>
                                                                <option value="Modal Sendiri">Modal Sendiri</option>
                                                                <option value="Modal Bersama">Modal Bersama</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Catatan Kredit Masa Lalu" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option value="">--Pilih--</option>
                                                                <option value="Lancar">Lancar</option>
                                                                <option value="Menunggak > 2 Bulan">Menunggak > 2 Bulan
                                                                </option>
                                                                <option value="Lancar Menunggak 2 Bulan">Lancar Menunggak
                                                                    2 Bulan</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="" value="RC : 59.00">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="" value="DSR : 646.43">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
@endsection
