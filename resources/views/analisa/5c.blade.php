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

                                        <form action="{{ route('a5c.store', ['pengajuan' => $data->kd_pengajuan]) }}"
                                            method="post">
                                            @csrf

                                            {{-- Informasi Character --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Character
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
                                                                value="Gaya Hidup" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="gaya_hidup" id="select1"
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('gaya_hidup') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('gaya_hidup') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('gaya_hidup') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Terbuka dan Konsisten" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="konsisten" id="select2"
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('konsisten') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('konsisten') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('konsisten') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pengendalian Emosi" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="pengendalian_emosi"
                                                                id="select3" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('pengendalian_emosi') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('pengendalian_emosi') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('pengendalian_emosi') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kepatuhan Terhadap Kewajiban" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kepatuhan" id="select4"
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('kepatuhan') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('kepatuhan') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('kepatuhan') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Melakukan Perbuatan Tercela" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="perbuatan_tercela"
                                                                id="select5" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('perbuatan_tercela') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('perbuatan_tercela') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('perbuatan_tercela') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Hubungan Sosial dgn Lingkungan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="hubungan_sosial"
                                                                id="select6" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('hubungan_sosial') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('hubungan_sosial') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('hubungan_sosial') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Keharmonisan Rumah Tangga" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="harmonis" id="select7"
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="3"
                                                                    {{ old('harmonis') == 3 ? 'selected' : '' }}>
                                                                    Baik
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('harmonis') == 2 ? 'selected' : '' }}>
                                                                    Cukup
                                                                    Baik</option>
                                                                <option value="1"
                                                                    {{ old('harmonis') == 1 ? 'selected' : '' }}>
                                                                    Kurang Baik</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Evaluasi" readonly="">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="nilai_karakter" id="n1"
                                                                value="{{ old('nilai_karakter') ? 'Kurang Baik' : '?' }}"
                                                                readonly required>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- Informasi Capacity --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Capacity
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="30%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="20%">Keterangan</th>
                                                        <th class="text-center" width="30%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="20%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kontinuitas Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kontinuitas"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('kontinuitas') == '1' ? 'selected' : '' }}>
                                                                    Tidak Tentu</option>
                                                                <option value="2"
                                                                    {{ old('kontinuitas') == '2' ? 'selected' : '' }}>
                                                                    Terus Menerus</option>
                                                                <option value="3"
                                                                    {{ old('kontinuitas') == '3' ? 'selected' : '' }}>
                                                                    Kadang-Kadang</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                name="kondisi_slik" value="Kondisi SLIK" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kondisi_slik"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('kondisi_slik') == '1' ? 'selected' : '' }}>
                                                                    Lancar</option>
                                                                <option value="2"
                                                                    {{ old('kondisi_slik') == '2' ? 'selected' : '' }}>
                                                                    Tidak Ada</option>
                                                                <option value="3"
                                                                    {{ old('kondisi_slik') == '3' ? 'selected' : '' }}>
                                                                    Tidak Baik</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pengalaman Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="pengalaman_usaha"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('pengalaman_usaha') == '1' ? 'selected' : '' }}>
                                                                    0 Tahun</option>
                                                                <option value="2"
                                                                    {{ old('pengalaman_usaha') == '2' ? 'selected' : '' }}>
                                                                    &lt; 1 Tahun</option>
                                                                <option value="3"
                                                                    {{ old('pengalaman_usaha') == '3' ? 'selected' : '' }}>
                                                                    1 - 3 Tahun</option>
                                                                <option value="4"
                                                                    {{ old('pengalaman_usaha') == '4' ? 'selected' : '' }}>
                                                                    &gt; 3 - 5 Tahun
                                                                </option>
                                                                <option value="5"
                                                                    {{ old('pengalaman_usaha') == '5' ? 'selected' : '' }}>
                                                                    &gt; 5 Tahun</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Aset Diluar Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="aset_diluar_usaha"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('aset_diluar_usaha') == '1' ? 'selected' : '' }}>
                                                                    Liquid</option>
                                                                <option value="2"
                                                                    {{ old('aset_diluar_usaha') == '2' ? 'selected' : '' }}>
                                                                    Cukup Liquid</option>
                                                                <option value="3"
                                                                    {{ old('aset_diluar_usaha') == '3' ? 'selected' : '' }}>
                                                                    Tidak Liquid</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pertumbuhan Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="pertumbuhan_usaha"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('pertumbuhan_usaha') == '1' ? 'selected' : '' }}>
                                                                    Tetap</option>
                                                                <option value="2"
                                                                    {{ old('pertumbuhan_usaha') == '2' ? 'selected' : '' }}>
                                                                    Turun</option>
                                                                <option value="3"
                                                                    {{ old('pertumbuhan_usaha') == '3' ? 'selected' : '' }}>
                                                                    Meningkat</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Aset Terkait Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="aset_terkait_usaha"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('aset_terkait_usaha') == '1' ? 'selected' : '' }}>
                                                                    Mengcover</option>
                                                                <option value="2"
                                                                    {{ old('aset_terkait_usaha') == '2' ? 'selected' : '' }}>
                                                                    Cukup Mengcover
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('aset_terkait_usaha') == '3' ? 'selected' : '' }}>
                                                                    Tidak Mengcover
                                                                </option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Catatan Laporan Keuangan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="laporan_keuangan"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('laporan_keuangan') == '1' ? 'selected' : '' }}>
                                                                    Tidak Ada</option>
                                                                <option value="2"
                                                                    {{ old('laporan_keuangan') == '2' ? 'selected' : '' }}>
                                                                    Transaksi
                                                                    Harian</option>
                                                                <option value="3"
                                                                    {{ old('laporan_keuangan') == '3' ? 'selected' : '' }}>
                                                                    Mengumpulkan Bukti
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Repayment Capacity" readonly="">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="rc"
                                                                value="{{ '0' . ' ' . old('rc') ?? '0' }}" readonly
                                                                required>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Catatan Kredit Masa Lalu" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="catatan_kredit"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('catatan_kredit') == '1' ? 'selected' : '' }}>
                                                                    Lancar</option>
                                                                <option value="2"
                                                                    {{ old('catatan_kredit') == '2' ? 'selected' : '' }}>
                                                                    Menunggak > 2 Bulan
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('catatan_kredit') == '3' ? 'selected' : '' }}>
                                                                    Lancar
                                                                    Menunggak
                                                                    2 Bulan</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Evaluasi" readonly="">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="evaluasi_capacity" value="?" readonly required>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- Informasi Capital --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Capital
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="40%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="40%">Keterangan</th>
                                                        <th class="text-center" width="20%">Evaluasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Sumber Modal" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="capital_sumber_modal"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('capital_sumber_modal') == '1' ? 'selected' : '' }}>
                                                                    Modal Sendiri</option>
                                                                <option value="2"
                                                                    {{ old('capital_sumber_modal') == '2' ? 'selected' : '' }}>
                                                                    Kerjasama</option>
                                                                <option value="3"
                                                                    {{ old('capital_sumber_modal') == '3' ? 'selected' : '' }}>
                                                                    Pihak Lain</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="evaluasi_capital" value="?" readonly required>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- Informasi Collateral --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Collateral
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="30%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="20%">Keterangan</th>
                                                        <th class="text-center" width="30%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="20%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kepemilikan Agunan Utama" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="agunan_utama"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('agunan_utama') == 1 ? 'selected' : '' }}>
                                                                    Milik Sendiri</option>
                                                                <option value="2"
                                                                    {{ old('agunan_utama') == 2 ? 'selected' : '' }}>
                                                                    Orang Lain/Milik Sendiri dan
                                                                    Orang Lian (Wariasan)</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kepemilikan Agunan Tambahan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="agunan_tambahan"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('agunan_tambahan') == 1 ? 'selected' : '' }}>
                                                                    Milik Sendiri</option>
                                                                <option value="2"
                                                                    {{ old('agunan_tambahan') == 2 ? 'selected' : '' }}>
                                                                    Orang Lain/Milik Sendiri dan
                                                                    Orang Lain (Wariasan)</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Legalitas Agunan Utama" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="legalitas_agunan"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('legalitas_agunan') == 1 ? 'selected' : '' }}>
                                                                    Milik Sendiri</option>
                                                                <option value="2"
                                                                    {{ old('legalitas_agunan') == 2 ? 'selected' : '' }}>
                                                                    Orang Lain/Milik Sendiri dan
                                                                    Orang Lian (Wariasan)</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Legalitas Agunan Tambahan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="legalitas_agunan_tambahan"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('legalitas_agunan_tambahan') == 1 ? 'selected' : '' }}>
                                                                    Milik Sendiri</option>
                                                                <option value="2"
                                                                    {{ old('legalitas_agunan_tambahan') == 2 ? 'selected' : '' }}>
                                                                    Orang Lain/Milik Sendiri dan
                                                                    Orang Lain (Wariasan)</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Mudah Diuangkan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="mudah_diuangkan"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('mudah_diuangkan') == 1 ? 'selected' : '' }}>
                                                                    Deposito,
                                                                    Tabungan, Emas
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('mudah_diuangkan') == 2 ? 'selected' : '' }}>
                                                                    BPKB, SHM</option>
                                                                <option value="3"
                                                                    {{ old('mudah_diuangkan') == 3 ? 'selected' : '' }}>
                                                                    Lainnya</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Stabilitas Harga" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="stabilitas_harga"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('stabilitas_harga') == 1 ? 'selected' : '' }}>
                                                                    SHM</option>
                                                                <option value="2"
                                                                    {{ old('stabilitas_harga') == 2 ? 'selected' : '' }}>
                                                                    Deposito,Tabungan, Emas</option>
                                                                <option value="3"
                                                                    {{ old('stabilitas_harga') == 3 ? 'selected' : '' }}>
                                                                    BPKB</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kondisi Kendaraan" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kondisi_kendaraan"
                                                                id="" required>

                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('kondisi_kendaraan') == 1 ? 'selected' : '' }}>
                                                                    Original,
                                                                    Lengkap, Tidak Cacat
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('kondisi_kendaraan') == 2 ? 'selected' : '' }}>
                                                                    Original, Tidak
                                                                    Lengkap</option>
                                                                <option value="3"
                                                                    {{ old('kondisi_kendaraan') == 3 ? 'selected' : '' }}>
                                                                    Tidak
                                                                    Original, Tidak Lengkap, Cacat</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Lokasi SHM" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="lokasi_shm" id=""
                                                                required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('lokasi_shm') == 1 ? 'selected' : '' }}>
                                                                    Strategis dan
                                                                    atau Produktif</option>
                                                                <option value="2"
                                                                    {{ old('lokasi_shm') == 2 ? 'selected' : '' }}>
                                                                    Strategis dan Produktif (Atau Sebaliknya)
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('lokasi_shm') == 3 ? 'selected' : '' }}>
                                                                    Kurang Strategis dan Kurang Produktif</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Pengikatan / Aspek Hukum" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="aspek_hukum"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('aspek_hukum') == 1 ? 'selected' : '' }}>
                                                                    Emas dan deposito/tabungan yang
                                                                    saldonya di blokir dan dilengkapi dengan surat kuasa
                                                                    pencairan</option>
                                                                <option value="2"
                                                                    {{ old('aspek_hukum') == 2 ? 'selected' : '' }}>
                                                                    SHM
                                                                    (dilengkapi dengan SPPT
                                                                    tahun
                                                                    berjalan atau 1 tahun yang lalu) diikat dengan hak
                                                                    tanggungan / BPKB (Kendaraan) diikat dengan fidusia
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('aspek_hukum') == 3 ? 'selected' : '' }}>
                                                                    SHM
                                                                    (dilengkapi dengan SPPT
                                                                    tahun
                                                                    berjalan atau 1 tahun yang lalu) / BPKB tanpa
                                                                    pengikatan
                                                                </option>
                                                                <option value="4"
                                                                    {{ old('aspek_hukum') == 4 ? 'selected' : '' }}>
                                                                    AJB
                                                                    / SPOP (dilengkapi dengan
                                                                    SPPT
                                                                    tahun berjalan atau 1 tahun yang lalu) tanpa
                                                                    pengikatan
                                                                    hak</option>
                                                                <option value="5"
                                                                    {{ old('aspek_hukum') == 5 ? 'selected' : '' }}>
                                                                    Agunan lain yang tidak memenuhi
                                                                    syarat</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control fs-4"
                                                                name="" value="Permohonan Taksasi Agunan"
                                                                readonly="" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                name="taksasi_agunan"
                                                                value="{{ old('taksasi_agunan') ?? '0' }}" required
                                                                readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Evaluasi" readonly="">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="evaluasi_collateral" value="?" readonly
                                                                required>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- Informasi Condition --}}
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Condition
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="40%">Rawayat/ Aset</th>
                                                        <th class="text-center" width="40%">Keterangan</th>
                                                        <th class="text-center" width="20%">Evaluasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Kondisi Alam" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="kondisi_alam"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('kondisi_alam') ? 'selected' : '' }}>
                                                                    Resiko Sangat Rendah</option>
                                                                <option value="2"
                                                                    {{ old('kondisi_alam') == 2 ? 'selected' : '' }}>
                                                                    Resiko Rendah</option>
                                                                <option value="3"
                                                                    {{ old('kondisi_alam') == 3 ? 'selected' : '' }}>
                                                                    Resiko Sedang</option>
                                                                <option value="4"
                                                                    {{ old('kondisi_alam') == 4 ? 'selected' : '' }}>
                                                                    Resiko Tinggi</option>
                                                                <option value="5"
                                                                    {{ old('kondisi_alam') == 5 ? 'selected' : '' }}>
                                                                    Resiko Sangat Tinggi</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control bg-primary fw-bold text-white text-center"
                                                                name="evaluasi_condition" value="?" readonly
                                                                required>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Persaingan Usaha" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="persaingan_usaha"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('persaingan_usaha') == 1 ? 'selected' : '' }}>
                                                                    Persaingan Usaha Tidak Ketat
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('persaingan_usaha') == 2 ? 'selected' : '' }}>
                                                                    Persaingan Usaha Kurang Ketat
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('persaingan_usaha') == 3 ? 'selected' : '' }}>
                                                                    Persaingan Usaha Ketat</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name=""
                                                                value="Regulasi Pemerintah" readonly="">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="regulasi_pemerintah"
                                                                id="" required>
                                                                <option value="">--Pilih--</option>
                                                                <option value="1"
                                                                    {{ old('regulasi_pemerintah') == 1 ? 'selected' : '' }}>
                                                                    Sangat Mendukung</option>
                                                                <option value="2"
                                                                    {{ old('regulasi_pemerintah') == 2 ? 'selected' : '' }}>
                                                                    Mendukung</option>
                                                                <option value="3"
                                                                    {{ old('regulasi_pemerintah') == 3 ? 'selected' : '' }}>
                                                                    Tidak Mendukung</option>
                                                            </select>
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
