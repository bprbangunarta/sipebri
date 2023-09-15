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

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('keuangan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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
                                            <form action="{{ route('kepemilikan.store') }}" method="POST">
                                                @csrf
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
                                                                <input class="form-control" disabled="" value="Rumah">
                                                                <input class="form-control" name="kode_pengajuan"
                                                                    value="{{ $data->kd_pengajuan }}" hidden>
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="rumah" id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Permanen">
                                                                        Permanen</option>
                                                                    <option value="Sederhana">
                                                                        Sederhana</option>
                                                                    <option value="Semi Permanen">
                                                                        Semi Permanen</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled="" value="Komputer">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="komputer" id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled="" value="Mobil">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="mobil" id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1 Unit">1
                                                                        Unit</option>
                                                                    <option value="2 Unit">2
                                                                        Unit</option>
                                                                    <option value="3 Unit">3
                                                                        Unit</option>
                                                                    <option value="4 Unit">4
                                                                        Unit</option>
                                                                    <option value="5 Unit">5
                                                                        Unit</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Mesin Cuci">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="mesin_cuci"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled="" value="Motor">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="motor" id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1 Unit">
                                                                        1 Unit</option>
                                                                    <option value="2 Unit">
                                                                        2 Unit</option>
                                                                    <option value="3 Unit">
                                                                        3 Unit</option>
                                                                    <option value="4 Unit">
                                                                        4 Unit</option>
                                                                    <option value="5 Unit">
                                                                        5 Unit</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Kursi Tamu">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="kursi"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Televisi">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="tv"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="LCD">
                                                                        LCD</option>
                                                                    <option value="LED">
                                                                        LED</option>
                                                                    <option value="CRT Flat">
                                                                        CRT Flat</option>
                                                                    <option value="CRT Cembung">
                                                                        CRT Cembung</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled=""
                                                                    value="Lemari Panjang">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="lemari"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <input class="form-control" name="nama_lain1"
                                                                    id="" placeholder="Isi Lainnya">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="lainnya1"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" name="nama_lain2"
                                                                    id="" placeholder="Isi Lainnya">

                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="lainnya2"
                                                                    id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada">
                                                                        Ada</option>
                                                                    <option value="Tidak Ada">
                                                                        Tidak Ada</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </thead>
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
@endsection
