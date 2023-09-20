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
                                        <form action="{{ route('asuransi.update', ['asuransi' => $data->kd_pengajuan]) }}"
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
                                                                <input class="form-control" type="text" name="nama_usaha"
                                                                    id="" value="Modal Kerja" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number"
                                                                    name="modal_kerja" value="{{ old('modal_kerja') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="tgl_lahir"
                                                                    id="" value="Investasi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" type="number"
                                                                    name="investasi" value="{{ old('investasi') }}" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Konsumtif" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number" name="konsumtif"
                                                                    value="{{ old('konsumtif') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Pelunasan Kredit" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" type="number"
                                                                    name="pelunasan_kredit"
                                                                    value="{{ old('pelunasan_kredit') }}" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Take Over" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number" name="take_over"
                                                                    value="{{ old('take_over') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Administrasi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number"
                                                                    name="administrasi" value="{{ old('administrasi') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Asuransi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number" name="asuransi"
                                                                    value="{{ old('asuransi') }}">
                                                            </td>

                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Jumlah Kebutuhan Dana" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number"
                                                                    name="kebutuhan_dana"
                                                                    value="{{ old('kebutuhan_dana') }}">
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
                                                                    value="{{ old('nama_lain') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number"
                                                                    name="nilai_lain" value="{{ old('nilai_lain') }}">
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
    <script src="{{ asset('assets/js/myscript/jasa.js') }}"></script>
@endsection
<script>
    // JS Darepicker
    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker && (new Litepicker({
            element: document.getElementById('datepicker-masa-identitas'),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
            },
        }));
    });

    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker && (new Litepicker({
            element: document.getElementById('datepicker-realisasi'),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
            },
        }));
    });

    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker && (new Litepicker({
            element: document.getElementById('datepicker-tgl-asuransi'),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
            },
        }));
    });
</script>
