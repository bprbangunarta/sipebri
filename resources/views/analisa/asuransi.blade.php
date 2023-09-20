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
                                                            <th class="text-center" colspan="4">Form Asuransi</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Data Asuransi</th>
                                                            <th class="text-center">Keterangan</th>
                                                            <th class="text-center">Data Asuransi</th>
                                                            <th class="text-center">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="nama_usaha"
                                                                    id="" value="Jenis Tanggungan" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="jenis_tanggungan" value="">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="tgl_lahir"
                                                                    id="" value="Tanggal Lahir" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                    name="tgl_lahir" id="datepicker-masa-identitas"
                                                                    value="{{ old('tgl_lahir') }}" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="No Polisi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    value="{{ old('no_bpkb') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="tgl_realisasi" id=""
                                                                    value="Tanggal Realisasi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                    name="tgl_realisasi" id="datepicker-realisasi"
                                                                    value="{{ old('tgl_realisasi') }}" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Jangka Waktu" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="jangka_waktu" value="{{ old('jangka_waktu') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="tgl_realisasi" id=""
                                                                    value="Nilai Pertanggungan" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="nilai_pertanggungan"
                                                                    value="{{ old('nilai_pertanggungan') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Maksimal" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="maksimal" value="{{ old('maksimal') }}"
                                                                    readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Jenis Asuransi" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="jenis_asuransi"
                                                                    id="" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1"
                                                                        {{ old('jenis_asuransi') == '1' ? 'selected' : '' }}>
                                                                        TLO</option>
                                                                    <option value="2"
                                                                        {{ old('jenis_asuransi') == '2' ? 'selected' : '' }}>
                                                                        Bumida Menurun</option>
                                                                    <option value="3"
                                                                        {{ old('jenis_asuransi') == '3' ? 'selected' : '' }}>
                                                                        Bumida Tetap</option>
                                                                    <option value="4"
                                                                        {{ old('jenis_asuransi') == '4' ? 'selected' : '' }}>
                                                                        Jiwasraya</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Rate" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="rate"
                                                                    value="{{ old('rate') }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="tgl_realisasi" id=""
                                                                    value="Tanggal Akhir Asuransi" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="tgl_akhir_asuransi" id="datepicker-tgl-asuransi"
                                                                    value="{{ old('tgl_akhir_asuransi') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" name="no_bpkb"
                                                                    id="" value="Usia" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="usia"
                                                                    value="{{ old('usia') }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" value="Premi"
                                                                    readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="premi"
                                                                    value="{{ old('premi') }}" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" id=""
                                                                    value="Premi KP" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="premi_kp" value="{{ old('premi_kp') }}">
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    value="Biaya ADM" readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text"
                                                                    name="biaya_adm" value="{{ old('biaya_adm') }}"
                                                                    readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input class="form-control" type="text" value="Total"
                                                                    readonly>
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="text" name="total"
                                                                    value="{{ old('total') }}" readonly>
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
