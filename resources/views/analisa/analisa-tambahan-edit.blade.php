@extends('templates.app')
@section('title', 'Analisa Tambahan')
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
                                        <form
                                            action="{{ route('tambahan.update', ['tambahan' => $data->kd_pengajuan, 'pengajuan' => $data->kd_pengajuan]) }}"
                                            method="POST">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="4">Analisa Tambahan</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Analisa Kebutuhan Dana</th>
                                                            <th class="text-center">Keterangan</th>
                                                            <th class="text-center">Analisa Kebutuhan Dana</th>
                                                            <th class="text-center">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Modal Kerja" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="modal_kerja" id="modal"
                                                                    value="{{ old('modal_kerja') ?? ('Rp. ' . number_format($tambahan->modal_kerja, 0, ',', '.') ?? 'Rp. 0') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Investasi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" type="text"
                                                                    name="investasi" id="inves"
                                                                    value="{{ old('investasi') ?? ('Rp. ' . number_format($tambahan->investasi, 0, ',', '.') ?? 'Rp. 0') }}" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Konsumtif" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="konsumtif"
                                                                    value="{{ old('konsumtif') ?? ('Rp. ' . number_format($tambahan->konsumtif, 0, ',', '.') ?? 'Rp. 0') }}"
                                                                    id="konsumtif">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Pelunasan Kredit" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" type="text"
                                                                    name="pelunasan_kredit"
                                                                    value="{{ old('pelunasan_kredit') ?? ('Rp. ' . number_format($tambahan->pelunasan_kredit, 0, ',', '.') ?? 'Rp. 0') }}"
                                                                    id="pelunasan" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Take Over" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="take_over"
                                                                    value="{{ old('take_over') ?? ('Rp. ' . number_format($tambahan->take_over, 0, ',', '.') ?? 'Rp. 0') }}"
                                                                    id="take_over">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" value="Administrasi"
                                                                    type="text" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="administrasi" id="administrasi"
                                                                    value="{{ old('administrasi') ?? ('Rp. ' . number_format($tambahan->administrasi, 0, ',', '.') ?? 'Rp. 0') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Asuransi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="asuransi"
                                                                    id="asuransi"
                                                                    value="{{ old('asuransi') ?? ('Rp. ' . number_format($tambahan->asuransi, 0, ',', '.') ?? 'Rp. 0') }}">
                                                            </td>

                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Jumlah Kebutuhan Dana" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="kebutuhan_dana" id="dana"
                                                                    value="{{ old('kebutuhan_dana') ?? ('Rp. ' . number_format($tambahan->kebutuhan_dana, 0, ',', '.') ?? 'Rp. 0') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Lain-lain" readonly>
                                                            </td>
                                                            <td></td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="nama_lain" placeholder="Nama Analisa Kebutuhan"
                                                                    value="{{ old('nama_lain') ?? $tambahan->nama_lain }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="nilai_lain" id="nilai_lain"
                                                                    value="{{ old('nilai_lain') ?? ('Rp. ' . number_format($tambahan->nilai_lain, 0, ',', '.') ?? 'Rp. 0') }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
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
    <script src="{{ asset('assets/js/myscript/tambahan.js') }}"></script>
@endsection
