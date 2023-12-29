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
                                                                    <option value="Permanen"
                                                                        {{ old('rumah') == 'Permanen' ? 'selected' : '' }}>
                                                                        Permanen</option>
                                                                    <option value="Sederhana"
                                                                        {{ old('rumah') == 'Sederhana' ? 'selected' : '' }}>
                                                                        Sederhana</option>
                                                                    <option value="Semi Permanen"
                                                                        {{ old('rumah') == 'Semi Permanen' ? 'selected' : '' }}>
                                                                        Semi Permanen</option>
                                                                </select>
                                                            </td>
                                                            <th>
                                                                <input class="form-control" disabled="" value="Komputer">
                                                            </th>
                                                            <td>
                                                                <select class="form-control" name="komputer" id="">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Ada"
                                                                        {{ old('komputer') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('komputer') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="1 Unit"
                                                                        {{ old('mobil') == '1 Unit' ? 'selected' : '' }}>1
                                                                        Unit</option>
                                                                    <option value="2 Unit"
                                                                        {{ old('mobil') == '2 Unit' ? 'selected' : '' }}>2
                                                                        Unit</option>
                                                                    <option value="3 Unit"
                                                                        {{ old('mobil') == '3 Unit' ? 'selected' : '' }}>3
                                                                        Unit</option>
                                                                    <option value="4 Unit"
                                                                        {{ old('mobil') == '4 Unit' ? 'selected' : '' }}>4
                                                                        Unit</option>
                                                                    <option value="5 Unit"
                                                                        {{ old('mobil') == '5 Unit' ? 'selected' : '' }}>5
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
                                                                    <option value="Ada"
                                                                        {{ old('mesin_cuci') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('mesin_cuci') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="1 Unit"
                                                                        {{ old('motor') == '1 Unit' ? 'selected' : '' }}>
                                                                        1 Unit</option>
                                                                    <option value="2 Unit"
                                                                        {{ old('motor') == '2 Unit' ? 'selected' : '' }}>
                                                                        2 Unit</option>
                                                                    <option value="3 Unit"
                                                                        {{ old('motor') == '3 Unit' ? 'selected' : '' }}>
                                                                        3 Unit</option>
                                                                    <option value="4 Unit"
                                                                        {{ old('motor') == '4 Unit' ? 'selected' : '' }}>
                                                                        4 Unit</option>
                                                                    <option value="5 Unit"
                                                                        {{ old('motor') == '5 Unit' ? 'selected' : '' }}>
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
                                                                    <option value="Ada"
                                                                        {{ old('kursi') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('kursi') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="LCD"
                                                                        {{ old('tv') == 'LCD' ? 'selected' : '' }}>
                                                                        LCD</option>
                                                                    <option value="LED"
                                                                        {{ old('tv') == 'LED' ? 'selected' : '' }}>
                                                                        LED</option>
                                                                    <option value="CRT Flat"
                                                                        {{ old('tv') == 'CRT Flat' ? 'selected' : '' }}>
                                                                        CRT Flat</option>
                                                                    <option value="CRT Cembung"
                                                                        {{ old('tv') == 'CRT Cembung' ? 'selected' : '' }}>
                                                                        CRT Cembung</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('tv') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="Ada"
                                                                        {{ old('lemari') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('lemari') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="Ada"
                                                                        {{ old('lainnya1') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('lainnya1') == 'Tidak Ada' ? 'selected' : '' }}>
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
                                                                    <option value="Ada"
                                                                        {{ old('lainnya2') == 'Ada' ? 'selected' : '' }}>
                                                                        Ada</option>
                                                                    <option value="Tidak Ada"
                                                                        {{ old('lainnya2') == 'Tidak Ada' ? 'selected' : '' }}>
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
