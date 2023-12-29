@extends('templates.app')
@section('title', 'Analisa Kualitatif')
@yield('jquery')
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
                                            <a href="{{ route('jaminan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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
                                        <form action="{{ route('kualitatif.store', ['pengajuan' => $data->kd_pengajuan]) }}"
                                            method="post">
                                            @csrf

                                            {{-- Karakter Debitur --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Karakter Debitur
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
                                                                value="BI Checking (SID Bank Indonesia)" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="bi_checking" id="select1"
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="4"
                                                                    {{ old('bi_checking') == 4 ? 'selected' : '' }}>
                                                                    Lancar
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('bi_checking') == 3 ? 'selected' : '' }}>
                                                                    Kurang Lancar</option>
                                                                <option value="2"
                                                                    {{ old('bi_checking') == 2 ? 'selected' : '' }}>
                                                                    Diragukan</option>
                                                                <option value="1"
                                                                    {{ old('bi_checking') == 1 ? 'selected' : '' }}>
                                                                    Macet</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kewajiban kepada pihak lain" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kewajiban_pihak_lain"
                                                                id="select2" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="5"
                                                                    {{ old('kewajiban_pihak_lain') == 5 ? 'selected' : '' }}>
                                                                    Bank Umum
                                                                </option>
                                                                <option value="4"
                                                                    {{ old('kewajiban_pihak_lain') == 4 ? 'selected' : '' }}>
                                                                    BPR</option>
                                                                <option value="3"
                                                                    {{ old('kewajiban_pihak_lain') == 3 ? 'selected' : '' }}>
                                                                    Koperasi</option>
                                                                <option value="1"
                                                                    {{ old('kewajiban_pihak_lain') == 1 ? 'selected' : '' }}>
                                                                    Leasing</option>
                                                                <option value="1"
                                                                    {{ old('kewajiban_pihak_lain') == 1 ? 'selected' : '' }}>
                                                                    lainnya</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Judi / Berurusan dengan pihak berwajib"
                                                                readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="pihak_berwajib"
                                                                id="select3" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="2"
                                                                    {{ old('pihak_berwajib') == 2 ? 'selected' : '' }}>
                                                                    Pernah
                                                                </option>
                                                                <option value="1"
                                                                    {{ old('pihak_berwajib') == 1 ? 'selected' : '' }}>
                                                                    Tidak Pernah</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Hubungan dengan tetangga" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="hubungan_tetangga"
                                                                id="select4" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('hubungan_tetangga') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('hubungan_tetangga') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('hubungan_tetangga') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pengalaman menjadi TKI" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="pengalaman_tki"
                                                                id="select5" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="2"
                                                                    {{ old('pengalaman_tki') == 2 ? 'selected' : '' }}>
                                                                    Pernah
                                                                </option>
                                                                <option value="1"
                                                                    {{ old('pengalaman_tki') == 1 ? 'selected' : '' }}>
                                                                    Tidak Pernah</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Usaha Calon Debitura
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="40%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="40%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Sumber Bahan Baku / Barang Dagangan"
                                                                readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="sumber_bahan" value="{{ old('sumber_bahan') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Proses Aktivitas Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="aktivitas_usaha"
                                                                value="{{ old('aktivitas_usaha') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Wilayah Operasional" readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="wilayah_operasional"
                                                                value="{{ old('wilayah_operasional') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Sitem Pembayaran ( Tunai/Tempo/Transfer )"
                                                                readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text" name="pembayaran"
                                                                value="{{ old('pembayaran') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Faktor Pendukung Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="pendukung_usaha"
                                                                value="{{ old('pendukung_usaha') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Faktor Pengurang / Kendala Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="pengurang_usaha"
                                                                value="{{ old('pengurang_usaha') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Trade Checking" readonly="">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text"
                                                                name="trade_checking"
                                                                value="{{ old('trade_checking') }}">
                                                        </td>
                                                    </tr>
                                                </tbody>
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
    <script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endsection
