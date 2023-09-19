@extends('templates.app')
@section('title', 'Analisa 5C')
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
                                        <div class="card-body">
                                            <form action="#" method="post">
                                                @csrf
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Analisa dan Evaluasi
                                                                Kredit
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="25%">Analisa</th>
                                                            <th class="text-center" width="25%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Analisa Watak (Character)" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Baik" readonly="">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Analisa Kemampuan (Capacity)" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Baik" readonly="">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Analisa Modal (Capital)" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Baik" readonly="">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Analisa Agunan (Collateral)" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Cukup Baik" readonly="">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Analisa Kondisi dan Prospek Usaha (Condition) (Collateral)"
                                                                    readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Cukup Baik" readonly="">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Rekomendasi</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="25%">Analisa</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                            <th class="text-center" width="27%">Analisa</th>
                                                            <th class="text-center" width="24%">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Plafon maksimum" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="max_plafon" value="{{ old('max_plafon') }}"
                                                                    readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Plafon Usulan Kredit" readonly="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="plafon_usulan"
                                                                    value="{{ old('plafon_usulan') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Jenis Kredit" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="jenis_kredit"
                                                                    id="select1" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1"
                                                                        {{ old('jenis_kredit') == 1 ? 'selected' : '' }}>
                                                                        Modal Usaha
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ old('jenis_kredit') == 2 ? 'selected' : '' }}>
                                                                        Investasi</option>
                                                                    <option value="3"
                                                                        {{ old('jenis_kredit') == 3 ? 'selected' : '' }}>
                                                                        Kredit Pemilikan Rumah (KPR)</option>
                                                                    <option value="4"
                                                                        {{ old('jenis_kredit') == 4 ? 'selected' : '' }}>
                                                                        Kredit Pemilikan Kendaran Bermotor</option>
                                                                    <option value="5"
                                                                        {{ old('jenis_kredit') == 5 ? 'selected' : '' }}>
                                                                        Konsumsi Lainnya</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Jangka Waktu Kredit" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="jangka_waktu" value="{{ old('jangka_waktu') }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Suku bunga kredit per tahun" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="bunga_pertahun"
                                                                    value="{{ old('bunga_pertahun') }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Sistem Angsuran" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="sistem_angsuran"
                                                                    id="select1">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="Flat"
                                                                        {{ old('sistem_angsuran') == 'Flat' ? 'selected' : '' }}>
                                                                        Flat
                                                                    </option>
                                                                    <option value="Efektif"
                                                                        {{ old('sistem_angsuran') == 'Efektif' ? 'selected' : '' }}>
                                                                        Efektif</option>
                                                                    <option value="Efektif Musiman"
                                                                        {{ old('sistem_angsuran') == 'Efektif Musiman' ? 'selected' : '' }}>
                                                                        Efektif Musiman</option>
                                                                    <option value="Efektif Anuitas"
                                                                        {{ old('sistem_angsuran') == 'Efektif Anuitas' ? 'selected' : '' }}>
                                                                        Efektif Anuitas</option>
                                                                    <option value="Rekening Koran"
                                                                        {{ old('sistem_angsuran') == 'Rekening Koran' ? 'selected' : '' }}>
                                                                        Rekening Koran</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Provisi Kredit" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="provisi_kredit"
                                                                    id="select1">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="0.00"
                                                                        {{ old('provisi_kredit') == '0.00' ? 'selected' : '' }}>
                                                                        0.00 %
                                                                    </option>
                                                                    <option value="0.50"
                                                                        {{ old('provisi_kredit') == '0.50' ? 'selected' : '' }}>
                                                                        0.50 %</option>
                                                                    <option value="1.00"
                                                                        {{ old('provisi_kredit') == '1.00' ? 'selected' : '' }}>
                                                                        1.00 %</option>
                                                                    <option value="1.25"
                                                                        {{ old('provisi_kredit') == '1.25' ? 'selected' : '' }}>
                                                                        1.25 %</option>
                                                                    <option value="1.50"
                                                                        {{ old('provisi_kredit') == '1.50' ? 'selected' : '' }}>
                                                                        1.50 %</option>
                                                                    <option value="1.75"
                                                                        {{ old('provisi_kredit') == '1.75' ? 'selected' : '' }}>
                                                                        1.75 %</option>
                                                                    <option value="2.00"
                                                                        {{ old('provisi_kredit') == '2.00' ? 'selected' : '' }}>
                                                                        2.00 %</option>
                                                                    <option value="2.25"
                                                                        {{ old('provisi_kredit') == '2.25' ? 'selected' : '' }}>
                                                                        2.25 %</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Biaya Administrasi" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="biaya_administrasi"
                                                                    id="select1">
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="0.00"
                                                                        {{ old('biaya_administrasi') == '0.00' ? 'selected' : '' }}>
                                                                        0.00 %
                                                                    </option>
                                                                    <option value="0.50"
                                                                        {{ old('biaya_administrasi') == '0.50' ? 'selected' : '' }}>
                                                                        0.50 %</option>
                                                                    <option value="1.00"
                                                                        {{ old('biaya_administrasi') == '1.00' ? 'selected' : '' }}>
                                                                        1.00 %</option>
                                                                    <option value="1.25"
                                                                        {{ old('biaya_administrasi') == '1.25' ? 'selected' : '' }}>
                                                                        1.25 %</option>
                                                                    <option value="1.50"
                                                                        {{ old('biaya_administrasi') == '1.50' ? 'selected' : '' }}>
                                                                        1.50 %</option>
                                                                    <option value="1.75"
                                                                        {{ old('biaya_administrasi') == '1.75' ? 'selected' : '' }}>
                                                                        1.75 %</option>
                                                                    <option value="2.00"
                                                                        {{ old('biaya_administrasi') == '2.00' ? 'selected' : '' }}>
                                                                        2.00 %</option>
                                                                    <option value="2.25"
                                                                        {{ old('biaya_administrasi') == '2.25' ? 'selected' : '' }}>
                                                                        2.25 %</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pinalty" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="pinalty"
                                                                    value="0.10 %">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Pengikatan Agunan" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="Pengikatan_agunan"
                                                                    id="select1" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="0.00"
                                                                        {{ old('Pengikatan_agunan') == '0.00' ? 'selected' : '' }}>
                                                                        APHT
                                                                    </option>
                                                                    <option value="1"
                                                                        {{ old('Pengikatan_agunan') == '0.50' ? 'selected' : '' }}>
                                                                        Fiducia</option>
                                                                    <option value="2"
                                                                        {{ old('Pengikatan_agunan') == '1.00' ? 'selected' : '' }}>
                                                                        APHT dan Fiducia</option>
                                                                    <option value="3"
                                                                        {{ old('Pengikatan_agunan') == '1.25' ? 'selected' : '' }}>
                                                                        Tanpa Pengiktan</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Asuransi Kendaraan (TLO)" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="asuransi_kendaraan"
                                                                    id="select1" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1"
                                                                        {{ old('asuransi_kendaraan') == '1' ? 'selected' : '' }}>
                                                                        Ya
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ old('asuransi_kendaraan') == '2' ? 'selected' : '' }}>
                                                                        Dilanjutkan</option>
                                                                    <option value="3"
                                                                        {{ old('asuransi_kendaraan') == '3' ? 'selected' : '' }}>
                                                                        Tidak</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Asuransi Jiwa (Ekawaktu)" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="asuransi_jiwa"
                                                                    id="select1" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1"
                                                                        {{ old('asuransi_jiwa') == '1' ? 'selected' : '' }}>
                                                                        Ya
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ old('asuransi_jiwa') == '2' ? 'selected' : '' }}>
                                                                        Dilanjutkan</option>
                                                                    <option value="3"
                                                                        {{ old('asuransi_jiwa') == '3' ? 'selected' : '' }}>
                                                                        Tidak</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Asuransi Kecelakaan Plus" readonly>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="asuransi_kecelakaan"
                                                                    id="select1" required>
                                                                    <option value="">--Pilih--</option>
                                                                    <option value="1"
                                                                        {{ old('asuransi_kecelakaan') == '1' ? 'selected' : '' }}>
                                                                        Ya
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ old('asuransi_kecelakaan') == '2' ? 'selected' : '' }}>
                                                                        Dilanjutkan</option>
                                                                    <option value="3"
                                                                        {{ old('asuransi_kecelakaan') == '3' ? 'selected' : '' }}>
                                                                        Tidak</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Sebelum Kredit direalisasikan" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="realisasi_kredit" value="">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Syarta-syarat tambahan" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="syarat_tambahan" value="">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name=""
                                                                    value="Syarta tambahan lainnya" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="syarat_tambahan_lain" value="">
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
    </div>
    <script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endsection
