@extends('templates.app')
@section('title', 'Analisa Usaha Jasa')
@yield('jquery')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="container-xl">
                                <div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('jasa.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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
                                        <form action="{{ route('jasa.update', ['jasa' => $jasa->kd_usaha]) }}"
                                            method="POST">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Informasi Usaha</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Nama Usaha Jasa</th>
                                                            <th class="text-center">Penghasilan Jasa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input class="form-control" type="text" name="nama_usaha"
                                                                    id="" placeholder="Nama Usaha Jasa"
                                                                    value="{{ old('nama_usaha') ?? $jasa->nama_usaha }}">
                                                                <input type="text" value="{{ $jasa->kd_usaha }}"
                                                                    name="kode_usaha" hidden>
                                                            </td>
                                                            <td><input class="form-control" type="text" name="pendapatan"
                                                                    id="pendapatan" placeholder="Nominal"
                                                                    value="{{ old('pendapatan') ?? ($jasa->pendapatan = 'Rp. ' . number_format($jasa->pendapatan, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Biaya Pengeluaran</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Pajak Kendaraan</th>
                                                            <th class="text-center">Pengeluaran Lainnya</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input class="form-control" type="text" name="b_pajak"
                                                                    id="pajak" placeholder="Masukan Nominal"
                                                                    value="{{ old('b_pajak') ?? ($jasa->b_pajak = 'Rp. ' . number_format($jasa->b_pajak, 0, ',', '.')) }}">
                                                            </td>
                                                            <td><input class="form-control" type="text" name="b_lainnya"
                                                                    id="lainnya" placeholder="Masukan Nominal"
                                                                    value="{{ old('b_lainnya') ?? ($jasa->b_lainnya = 'Rp. ' . number_format($jasa->b_lainnya, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table
                                                    class="table
                                                                    table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Analisa Usaha
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Total Penghasilan"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    placeholder="Rp. " name="totalpenghasilan"
                                                                    id="tpenghasilan" value="{{ $jasa->pendapatan }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Total Pengeluaran"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    placeholder="Rp. " name="pengeluaran" id="tpengeluaran"
                                                                    value="{{ old('pengeluaran') ?? ($jasa->pengeluaran = 'Rp. ' . number_format($jasa->pengeluaran, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Bersih Usaha">
                                                            </th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary fw-bold text-white"
                                                                    value="{{ old('laba_bersih') ?? ($jasa->laba_bersih = 'Rp. ' . number_format($jasa->laba_bersih, 0, ',', '.')) }}"
                                                                    name="laba_bersih" id="laba">
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                            </div>
                                            <div class="card-footer bg-transparent mt-auto">
                                                <div class="btn-list justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
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
    <script src="{{ asset('assets/js/myscript/jasa.js') }}"></script>
@endsection
