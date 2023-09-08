@extends('templates.app')
@section('title', 'Analisa Harta Kepemilikan')

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
                                                <a href="{{ route('tambah.index') }}"
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
                                                <a href="{{ route('pertanian.index') }}"
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
                                                <a href="{{ route('jasa.index') }}"
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
                                                <a href="{{ route('lainnya.index') }}"
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
                                                <a href="{{ route('analisa.keuangan') }}"
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
                                                <a href="{{ route('analisa.harta.kepemilikan') }}"
                                                    class="list-group-item list-group-item-action d-flex align-items-center active">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7l5 5l-5 5"></path>
                                                        <path d="M13 7l5 5l-5 5"></path>
                                                    </svg> &nbsp;Harta Kepemilikan</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <div class="card-body">
                                            <form action="#">
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="4">Informasi Harta
                                                                Kepemilikan
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="25%">Nama Harta</th>
                                                            <th class="text-center" width="25%">Kepemilikan</th>
                                                            <th class="text-center" width="25%">Nama Harta</th>
                                                            <th class="text-center" width="25%">Kepemilikan</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Rumah">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Permanen">Permanen</option>
                                                                    <option value="Sederhana">Sederhana</option>
                                                                    <option value="Semi Permanen">Semi Permanen</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Komputer">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Mobil">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1 Unit">1 Unit</option>
                                                                    <option value="2 Unit">2 Unit</option>
                                                                    <option value="3 Unit">3 Unit</option>
                                                                    <option value="4 Unit">4 Unit</option>
                                                                    <option value="5 Unit">5 Unit</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Mesin Cuci">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Motor">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1 Unit">1 Unit</option>
                                                                    <option value="2 Unit">2 Unit</option>
                                                                    <option value="3 Unit">3 Unit</option>
                                                                    <option value="4 Unit">4 Unit</option>
                                                                    <option value="5 Unit">5 Unit</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Kursi Tamu">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Televisi">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="LCD">LCD</option>
                                                                    <option value="LED">LED</option>
                                                                    <option value="CRT Flat">CRT Flat</option>
                                                                    <option value="CRT Cembung">CRT Cembung</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Lemari Panjang">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" name="" id=""
                                                                    placeholder="Isi Lainnya">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" name="" id=""
                                                                    placeholder="Isi Lainnya">

                                                            </th>
                                                            <td>
                                                                <select class="form-control" name=""
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">Ada</option>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <a href="#" class="btn btn-primary">
                                                            Simpan
                                                        </a>
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
@endsection
