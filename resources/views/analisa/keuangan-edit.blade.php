@extends('templates.app')
@section('title', 'Analisa Kemampuan Keuangan')
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

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('lain.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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
                                        <form
                                            action="{{ route('keuangan.update_detail', ['keuangan' => $biaya[0]->kode_keuangan]) }}"
                                            method="post">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="5">Informasi Keuangan</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Jenis Usaha</th>
                                                            <th class="text-center">Pendapatan Usaha</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Usaha Perdagangan"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($kemampuan['perdagangan'], 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Usaha Pertanian"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($kemampuan['pertanian'], 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Usaha Jasa">
                                                            </th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($kemampuan['jasa'], 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Usaha Lainnya"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($kemampuan['lain'], 0, ',', '.') }}">
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Biaya Rumah Tangga</th>
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
                                                            <th><input type="text" name="kode_biaya1"
                                                                    value="{{ $biaya[0]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly
                                                                    value="Biaya Konsumsi Pokok" name="nama1">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya1"
                                                                    id="konsumsi"
                                                                    value="{{ old('biaya1') ?? ($biaya[0]->nominal = 'Rp ' . number_format($biaya[0]->nominal, 0, ',', '.')) }}"
                                                                    required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya2"
                                                                    value="{{ $biaya[1]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly value="Kesehatan"
                                                                    name="nama2">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya2"
                                                                    value="{{ old('biaya2') ?? ($biaya[1]->nominal = 'Rp ' . number_format($biaya[1]->nominal, 0, ',', '.')) }}"
                                                                    id="kesehatan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya3"
                                                                    value="{{ $biaya[2]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly value="Pendidikan"
                                                                    name="nama3">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya3"
                                                                    id="pendidikan"
                                                                    value="{{ old('biaya3') ?? ($biaya[2]->nominal = 'Rp ' . number_format($biaya[2]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya4"
                                                                    value="{{ $biaya[3]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly value="Gatel"
                                                                    name="nama4">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya4"
                                                                    id="gatel"
                                                                    value="{{ old('biaya4') ?? ($biaya[3]->nominal = 'Rp ' . number_format($biaya[3]->nominal, 0, ',', '.')) }}"
                                                                    required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya5"
                                                                    value="{{ $biaya[4]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly value="Jajan Anak"
                                                                    name="nama5">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya5"
                                                                    id="jajan"
                                                                    value="{{ old('biaya5') ?? ($biaya[4]->nominal = 'Rp ' . number_format($biaya[4]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya6"
                                                                    value="{{ $biaya[5]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly
                                                                    value="Sumbangan Sosial" name="nama6">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya6"
                                                                    id="sumbangan"
                                                                    value="{{ old('biaya6') ?? ($biaya[5]->nominal = 'Rp ' . number_format($biaya[5]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" name="kode_biaya7"
                                                                    value="{{ $biaya[6]->kode_biaya }}" hidden>
                                                                <input class="form-control" readonly name="nama7"
                                                                    value="Rokok">
                                                            </th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="biaya7"
                                                                    id="roko"
                                                                    value="{{ old('biaya7') ?? ($biaya[6]->nominal = 'Rp ' . number_format($biaya[6]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Kawajiban Lainnya</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Kewajiban Untuk</th>
                                                            <th class="text-center">Nominal Kewajiban</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" name="kd_biaya1"
                                                                    value="{{ $biaya[7]->kode_biaya }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Kewajiban" name="data1"
                                                                    value="{{ $biaya[7]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="kewajiban1"
                                                                    id="kewajiban1"
                                                                    value="{{ old('kewajiban1') ?? ($biaya[7]->nominal = 'Rp ' . number_format($biaya[7]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="kd_biaya2"
                                                                    value="{{ $biaya[8]->kode_biaya }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Kewajiban" name="data2"
                                                                    value="{{ $biaya[8]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="kewajiban2"
                                                                    id="kewajiban2"
                                                                    value="{{ old('kewajiban2') ?? ($biaya[8]->nominal = 'Rp ' . number_format($biaya[8]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" name="kd_biaya3"
                                                                    value="{{ $biaya[9]->kode_biaya }}" hidden>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Kewajiban" name="data3"
                                                                    value="{{ $biaya[9]->pengeluaran }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="kewajiban3"
                                                                    id="kewajiban3"
                                                                    value="{{ old('kewajiban3') ?? ($biaya[9]->nominal = 'Rp ' . number_format($biaya[9]->nominal, 0, ',', '.')) }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Analisa Keuangan</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pendapatan Usaha"></th>
                                                            <td><input type="text" class="form-control" name="p_usaha"
                                                                    readonly=""
                                                                    value="{{ 'Rp ' . number_format($kemampuan['total'], 0, ',', '.') }}"
                                                                    id="pendapatan">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Biaya Rumah Tangga"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($biaya[0]->b_rumah_tangga, 0, ',', '.') }}"
                                                                    name="b_rumah_tangga" id="biaya">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Kewajiban Lainnya"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="{{ 'Rp ' . number_format($biaya[0]->b_kewajiban_lainya, 0, ',', '.') }}"
                                                                    name="b_kewajiban_lainya" id="kewajiban_lain"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Keuangan Perbulan"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary fw-bold text-white"
                                                                    value="{{ 'Rp ' . number_format($kemampuan['keuangan_perbulan'], 0, ',', '.') }}"
                                                                    name="keuangan_perbulan" id="hasilbersih" readonly>
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
    <script src="{{ asset('assets/js/myscript/keuangan.js') }}"></script>
@endsection
