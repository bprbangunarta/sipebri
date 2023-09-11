@extends('templates.app')
@section('title', 'Analisa Usaha Lainnya')
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
                                            <a href="{{ route('jasa.index') }}" class="btn btn-primary">
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
                                        <form action="{{ route('lain.update_edit', ['lain' => $lain->kd_usaha]) }}"
                                            method="post">
                                            @csrf
                                            @method('put')
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="5">Informasi Usaha</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Jenis Usaha</th>
                                                            <th class="text-center">Nama Usaha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="jenis_usaha" id=""
                                                                    value="{{ $lain->jenis_usaha }}" readonly></td>
                                                            <td><input class="form-control" type="text" name="nama_usaha"
                                                                    id="" value="{{ $lain->nama_usaha }}" readonly>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Omset Penjualan</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Nama Pendapatan</th>
                                                            <th class="text-center">Nominal Pendapatan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" name="du_kode1"
                                                                    value="{{ $omset[0]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pendapatan" name="nama1"
                                                                    value="{{ $omset[0]->penjualan }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="nominal1"
                                                                    id="nominal1"
                                                                    value="{{ $omset[0]->nominal = 'Rp. ' . number_format($omset[0]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="du_kode2"
                                                                    value="{{ $omset[1]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pendapatan" name="nama2"
                                                                    value="{{ $omset[1]->penjualan }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="nominal2"
                                                                    id="nominal2"
                                                                    value="{{ $omset[1]->nominal = 'Rp. ' . number_format($omset[1]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="du_kode3"
                                                                    value="{{ $omset[2]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pendapatan" name="nama3"
                                                                    value="{{ $omset[2]->penjualan }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="nominal3"
                                                                    id="nominal3"
                                                                    value="{{ $omset[2]->nominal = 'Rp. ' . number_format($omset[2]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="du_kode4"
                                                                    value="{{ $omset[3]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pendapatan" name="nama4"
                                                                    value="{{ $omset[3]->penjualan }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="nominal4"
                                                                    id="nominal4"
                                                                    value="{{ $omset[3]->nominal = 'Rp. ' . number_format($omset[3]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="du_kode5"
                                                                    value="{{ $omset[4]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pendapatan" name="nama5"
                                                                    value="{{ $omset[4]->penjualan }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="nominal5"
                                                                    id="nominal5"
                                                                    value="{{ $omset[4]->nominal = 'Rp. ' . number_format($omset[4]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Biaya Operasional</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Pengeluaran Untuk</th>
                                                            <th class="text-center">Nominal Pengeluaran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" name="bu_kode1"
                                                                    value="{{ $biaya[0]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pengeluaran" name="nampe1"
                                                                    value="{{ $biaya[0]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengeluaran1"
                                                                    id="pengeluaran1"
                                                                    value="{{ $biaya[0]->nominal = 'Rp. ' . number_format($biaya[0]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="bu_kode2"
                                                                    value="{{ $biaya[1]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pengeluaran" name="nampe2"
                                                                    value="{{ $biaya[1]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengeluaran2"
                                                                    id="pengeluaran2"
                                                                    value="{{ $biaya[1]->nominal = 'Rp. ' . number_format($biaya[1]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="bu_kode3"
                                                                    value="{{ $biaya[2]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pengeluaran" name="nampe3"
                                                                    value="{{ $biaya[2]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengeluaran3"
                                                                    id="pengeluaran3"
                                                                    value="{{ $biaya[2]->nominal = 'Rp. ' . number_format($biaya[2]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="bu_kode4"
                                                                    value="{{ $biaya[3]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pengeluaran" name="nampe4"
                                                                    value="{{ $biaya[3]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengeluaran4"
                                                                    id="pengeluaran4"
                                                                    value="{{ $biaya[3]->nominal = 'Rp. ' . number_format($biaya[3]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="bu_kode5"
                                                                    value="{{ $biaya[4]->kode_lain }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Pengeluaran" name="nampe5"
                                                                    value="{{ $biaya[4]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengeluaran5"
                                                                    id="pengeluaran5"
                                                                    value="{{ $biaya[4]->nominal = 'Rp. ' . number_format($biaya[4]->nominal, 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Analisa Usaha</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pendapatan Usaha"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ $lain->pendapatan = 'Rp. ' . number_format($lain->pendapatan, 0, ',', '.') }}"
                                                                    name="pendapatan" id="penus"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Biaya Operasional"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ $lain->pengeluaran = 'Rp. ' . number_format($lain->pengeluaran, 0, ',', '.') }}"
                                                                    name="pengeluaran" id="biayaop">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Proyeksi Penambahan Hasil Usaha"></th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="proyeksi"
                                                                    value="{{ $lain->proyeksi = 'Rp. ' . number_format($lain->proyeksi, 0, ',', '.') }}"
                                                                    id="pph"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Bersih Usaha"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary fw-bold text-white"
                                                                    value="{{ $lain->laba_bersih = 'Rp. ' . number_format($lain->laba_bersih, 0, ',', '.') }}"
                                                                    name="laba_bersih" id="hasilbersih">
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
    <script src="{{ asset('assets/js/myscript/lainnya.js') }}"></script>
@endsection
