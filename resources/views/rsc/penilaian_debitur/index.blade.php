@extends('theme.app')
@section('title', 'Penilaian Debitur')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-rsc', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#faktor" data-toggle="tab">FAKTOR</a>
                            </li>

                            <li>
                                <a href="#kodisi_agunan" data-toggle="tab">KONDISI AGUNAN</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div id="faktor" class="tab-pane fade in active">
                                @if (is_null($penilaian))
                                    <form
                                        action="{{ route('rsc.simpan.kondisi.usaha', ['kode' => $data->kode, 'rsc' => $data->rsc]) }}"
                                        method="POST">
                                        @method('post')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                            <div class="div-left">
                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR TEKNIS I</span>
                                                    <select type="text" class="form-control text-uppercase faktor_teknis"
                                                        style="width: 100%;" name="faktor_teknis1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MASALAH"
                                                            {{ old('faktor_teknis1') == 'MENJADI MASALAH' ? 'selected' : '' }}>
                                                            MENJADI MASALAH</option>
                                                        <option value="LOKASI USAHA TIDAK MENJADI MASALAH"
                                                            {{ old('faktor_teknis1') == 'LOKASI USAHA TIDAK MENJADI MASALAH' ? 'selected' : '' }}>
                                                            LOKASI USAHA TIDAK
                                                            MENJADI MASALAH</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">FAKTOR TEKNIS II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_teknis2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MASALAH"
                                                            {{ old('faktor_teknis2') == 'MENJADI MASALAH' ? 'selected' : '' }}>
                                                            MENJADI MASALAH</option>
                                                        <option value="TRANSPORTASI USAHA TIDAK MENJADI MASALAH"
                                                            {{ old('faktor_teknis2') == 'TRANSPORTASI USAHA TIDAK MENJADI MASALAH' ? 'selected' : '' }}>
                                                            TRANSPORTASI
                                                            USAHA TIDAK
                                                            MENJADI MASALAH</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR EKONOMI I</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_ekonomi1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MACET"
                                                            {{ old('faktor_ekonomi1') == 'MENJADI MACET' ? 'selected' : '' }}>
                                                            MENJADI MACET</option>
                                                        <option value="PIUTANG USAHA MASIH LANCAR"
                                                            {{ old('faktor_ekonomi1') == 'PIUTANG USAHA MASIH LANCAR' ? 'selected' : '' }}>
                                                            PIUTANG USAHA MASIH
                                                            LANCAR</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">NOMINAL FAKTOR EKONOMI I</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="nominal_faktor_ekonomi1" id="nominal_faktor_ekonomi1"
                                                        value="{{ old('nominal_faktor_ekonomi1') }}" placeholder="ENTRI"
                                                        required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KETERANGAN FAKTOR EKONOMI</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_ekonomi" id="catatan_faktor_ekonomi"
                                                        value="{{ old('catatan_faktor_ekonomi') }}" placeholder="ENTRI">
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR MARKETING II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PEMASARAN MUDAH"
                                                            {{ old('faktor_marketing2') == 'PEMASARAN MUDAH' ? 'selected' : '' }}>
                                                            PEMASARAN MUDAH</option>
                                                        <option value="SULIT KARENA"
                                                            {{ old('faktor_marketing2') == 'SULIT KARENA' ? 'selected' : '' }}>
                                                            SULIT KARENA</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR MARKETING III</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing3" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PENJUALAN DIBAYAR KONTAN"
                                                            {{ old('faktor_marketing3') == 'PENJUALAN DIBAYAR KONTAN' ? 'selected' : '' }}>
                                                            PENJUALAN DIBAYAR KONTAN
                                                        </option>
                                                        <option value="JANGKA WAKTU"
                                                            {{ old('faktor_marketing3') == 'JANGKA WAKTU' ? 'selected' : '' }}>
                                                            JANGKA WAKTU</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">CATATAN FAKTOR MARKETING</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing" id="catatan_faktor_marketing"
                                                        value="{{ old('catatan_faktor_marketing') }}" placeholder="ENTRI">
                                                </div>


                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">CATATAN FAKTOR RUMAH TANGGA</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_rumah_tangga" id="catatan_faktor_rumah_tangga"
                                                        value="{{ old('catatan_faktor_rumah_tangga') }}"
                                                        placeholder="ENTRI">
                                                </div>

                                            </div>


                                            <div class="div-right">
                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KETERANGAN FAKTOR TEKNIS</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_teknis" id="catatan_faktor_teknis"
                                                        value="{{ old('catatan_faktor_teknis') }}" placeholder="ENTRI">
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR EKONOMI II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_ekonomi2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MACET"
                                                            {{ old('faktor_ekonomi2') == 'MENJADI MACET' ? 'selected' : '' }}>
                                                            MENJADI MACET</option>
                                                        <option value="HUTANG USAHA MASIH LANCAR"
                                                            {{ old('faktor_ekonomi2') == 'HUTANG USAHA MASIH LANCAR' ? 'selected' : '' }}>
                                                            HUTANG USAHA MASIH LANCAR
                                                        </option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">NOMINAL FAKTOR EKONOMI II</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="nominal_faktor_ekonomi2" id="nominal_faktor_ekonomi2"
                                                        value="{{ old('nominal_faktor_ekonomi2') }}" placeholder="ENTRI"
                                                        required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">FAKTOR MARKETING I</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PELANGGAN BERTAMBAH"
                                                            {{ old('faktor_marketing1') == 'PELANGGAN BERTAMBAH' ? 'selected' : '' }}>
                                                            PELANGGAN BERTAMBAH</option>
                                                        <option value="BERKURANG"
                                                            {{ old('faktor_marketing1') == 'BERKURANG' ? 'selected' : '' }}>
                                                            BERKURANG</option>
                                                        <option value="TETAP"
                                                            {{ old('faktor_marketing1') == 'TETAP' ? 'selected' : '' }}>
                                                            TETAP
                                                        </option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KET FAKTOR MARKETING II</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing2" id="catatan_faktor_marketing2"
                                                        value="{{ old('catatan_faktor_marketing2') }}"
                                                        placeholder="ENTRI">
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KET FAKTOR MARKETING III</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing3" id="catatan_faktor_marketing3"
                                                        value="{{ old('catatan_faktor_marketing3') }}"
                                                        placeholder="ENTRI">
                                                </div>


                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR RUMAH TANGGA</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="faktor_rumah_tangga" id="faktor_rumah_tangga"
                                                        value="{{ old('faktor_rumah_tangga') }}" placeholder="ENTRI"
                                                        required>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">BIAYA RUMAH TANGGA</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="biaya_rumah_tangga" id="biaya_rumah_tangga"
                                                        value="{{ old('biaya_rumah_tangga') }}" placeholder="ENTRI"
                                                        required>
                                                </div>


                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR LAIN</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="faktor_lain" id="faktor_lain"
                                                        value="{{ old('faktor_lain') }}" placeholder="ENTRI">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </form>
                                @else
                                    <form
                                        action="{{ route('rsc.update.kondisi.usaha', ['kode' => $data->kode, 'rsc' => $data->rsc]) }}"
                                        method="POST">
                                        @method('put')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                            <div class="div-left">
                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR TEKNIS I</span>
                                                    <select type="text"
                                                        class="form-control text-uppercase faktor_teknis"
                                                        style="width: 100%;" name="faktor_teknis1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MASALAH"
                                                            {{ old('faktor_teknis1') == 'MENJADI MASALAH' || $penilaian->faktor_teknis1 == 'MENJADI MASALAH' ? 'selected' : '' }}>
                                                            MENJADI MASALAH</option>
                                                        <option value="LOKASI USAHA TIDAK MENJADI MASALAH"
                                                            {{ old('faktor_teknis1') == 'LOKASI USAHA TIDAK MENJADI MASALAH' || $penilaian->faktor_teknis1 == 'LOKASI USAHA TIDAK MENJADI MASALAH' ? 'selected' : '' }}>
                                                            LOKASI USAHA TIDAK
                                                            MENJADI MASALAH</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">FAKTOR TEKNIS II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_teknis2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MASALAH"
                                                            {{ old('faktor_teknis2') == 'MENJADI MASALAH' || $penilaian->faktor_teknis2 == 'MENJADI MASALAH' ? 'selected' : '' }}>
                                                            MENJADI MASALAH</option>
                                                        <option value="TRANSPORTASI USAHA TIDAK MENJADI MASALAH"
                                                            {{ old('faktor_teknis2') == 'TRANSPORTASI USAHA TIDAK MENJADI MASALAH' || $penilaian->faktor_teknis2 == 'TRANSPORTASI USAHA TIDAK MENJADI MASALAH' ? 'selected' : '' }}>
                                                            TRANSPORTASI USAHA TIDAK MENJADI MASALAH</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR EKONOMI I</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_ekonomi1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MACET"
                                                            {{ old('faktor_ekonomi1') == 'MENJADI MACET' || $penilaian->faktor_ekonomi1 == 'MENJADI MACET' ? 'selected' : '' }}>
                                                            MENJADI MACET</option>
                                                        <option value="PIUTANG USAHA MASIH LANCAR"
                                                            {{ old('faktor_ekonomi1') == 'PIUTANG USAHA MASIH LANCAR' || $penilaian->faktor_ekonomi1 == 'PIUTANG USAHA MASIH LANCAR' ? 'selected' : '' }}>
                                                            PIUTANG USAHA MASIH
                                                            LANCAR</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">NOMINAL FAKTOR EKONOMI I</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="nominal_faktor_ekonomi1" id="nominal_faktor_ekonomi1"
                                                        value="{{ old('nominal_faktor_ekonomi1', number_format($penilaian->nominal_fe1, '0', ',', '.')) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KETERANGAN FAKTOR EKONOMI</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_ekonomi" id="catatan_faktor_ekonomi"
                                                        value="{{ old('catatan_faktor_ekonomi', $penilaian->catatan_faktor_ekonomi) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR MARKETING II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PEMASARAN MUDAH"
                                                            {{ old('faktor_marketing2') == 'PEMASARAN MUDAH' || $penilaian->faktor_marketing2 == 'PEMASARAN MUDAH' ? 'selected' : '' }}>
                                                            PEMASARAN MUDAH</option>
                                                        <option value="SULIT KARENA"
                                                            {{ old('faktor_marketing2') == 'SULIT KARENA' || $penilaian->faktor_marketing2 == 'SULIT KARENA' ? 'selected' : '' }}>
                                                            SULIT KARENA</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR MARKETING III</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing3" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PENJUALAN DIBAYAR KONTAN"
                                                            {{ old('faktor_marketing3') == 'PENJUALAN DIBAYAR KONTAN' || $penilaian->faktor_marketing3 == 'PENJUALAN DIBAYAR KONTAN' ? 'selected' : '' }}>
                                                            PENJUALAN DIBAYAR KONTAN
                                                        </option>
                                                        <option value="JANGKA WAKTU"
                                                            {{ old('faktor_marketing3') == 'JANGKA WAKTU' || $penilaian->faktor_marketing3 == 'JANGKA WAKTU' ? 'selected' : '' }}>
                                                            JANGKA WAKTU</option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">CATATAN FAKTOR MARKETING</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing" id="catatan_faktor_marketing"
                                                        value="{{ old('catatan_faktor_marketing', $penilaian->catatan_faktor_marketing_lain) }}"
                                                        placeholder="ENTRI" required>
                                                </div>


                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">CATATAN FAKTOR RUMAH TANGGA</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_rumah_tangga"
                                                        id="catatan_faktor_rumah_tangga"
                                                        value="{{ old('catatan_faktor_rumah_tangga', $penilaian->catatan_frt) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                            </div>


                                            <div class="div-right">
                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KETERANGAN FAKTOR TEKNIS</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_teknis" id="catatan_faktor_teknis"
                                                        value="{{ old('catatan_faktor_teknis', $penilaian->catatan_faktor_teknis) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR EKONOMI II</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_ekonomi2" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="MENJADI MACET"
                                                            {{ old('faktor_ekonomi2') == 'MENJADI MACET' || $penilaian->faktor_ekonomi2 == 'MENJADI MACET' ? 'selected' : '' }}>
                                                            MENJADI MACET</option>
                                                        <option value="HUTANG USAHA MASIH LANCAR"
                                                            {{ old('faktor_ekonomi2') == 'HUTANG USAHA MASIH LANCAR' || $penilaian->faktor_ekonomi2 == 'HUTANG USAHA MASIH LANCAR' ? 'selected' : '' }}>
                                                            HUTANG USAHA MASIH LANCAR
                                                        </option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">NOMINAL FAKTOR EKONOMI II</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="nominal_faktor_ekonomi2" id="nominal_faktor_ekonomi2"
                                                        value="{{ old('nominal_faktor_ekonomi2', number_format($penilaian->nominal_fe2, '0', ',', '.')) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">FAKTOR MARKETING I</span>
                                                    <select type="text" class="form-control faktor_teknis"
                                                        style="width: 100%;" name="faktor_marketing1" required>
                                                        <option value="">--Pilih--</option>
                                                        <option value="PELANGGAN BERTAMBAH"
                                                            {{ old('faktor_marketing1') == 'PELANGGAN BERTAMBAH' || $penilaian->faktor_marketing1 == 'PELANGGAN BERTAMBAH' ? 'selected' : '' }}>
                                                            PELANGGAN BERTAMBAH</option>
                                                        <option value="BERKURANG"
                                                            {{ old('faktor_marketing1') == 'BERKURANG' || $penilaian->faktor_marketing1 == 'BERKURANG' ? 'selected' : '' }}>
                                                            BERKURANG</option>
                                                        <option value="TETAP"
                                                            {{ old('faktor_marketing1') == 'TETAP' || $penilaian->faktor_marketing1 == 'TETAP' ? 'selected' : '' }}>
                                                            TETAP
                                                        </option>
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KET FAKTOR MARKETING II</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing2" id="catatan_faktor_marketing2"
                                                        value="{{ old('catatan_faktor_marketing2', $penilaian->catatan_faktor_marketing2) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 100%;float:left;">
                                                    <span class="fw-bold">KET FAKTOR MARKETING III</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="catatan_faktor_marketing3" id="catatan_faktor_marketing3"
                                                        value="{{ old('catatan_faktor_marketing3', $penilaian->catatan_faktor_marketing3) }}"
                                                        placeholder="ENTRI" required>
                                                </div>


                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <span class="fw-bold">FAKTOR RUMAH TANGGA</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="faktor_rumah_tangga" id="faktor_rumah_tangga"
                                                        value="{{ old('faktor_rumah_tangga', $penilaian->faktor_rumah_tangga) }}"
                                                        placeholder="ENTRI" required>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <span class="fw-bold">BIAYA RUMAH TANGGA</span>
                                                    <input type="text" class="form-control" style="font-size: 14px;"
                                                        name="biaya_rumah_tangga" id="biaya_rumah_tangga"
                                                        value="{{ old('biaya_rumah_tangga', number_format($penilaian->biaya_faktor_rumah_tangga, '0', ',', '.')) }}"
                                                        placeholder="ENTRI" required>
                                                </div>


                                                <div style="margin-top:5px;width: 100%;float:right;">
                                                    <span class="fw-bold">FAKTOR LAIN</span>
                                                    <input type="text" class="form-control"
                                                        style="font-size: 12px; text-transform: uppercase;"
                                                        name="faktor_lain" id="faktor_lain"
                                                        value="{{ old('faktor_lain', $penilaian->faktor_lainnya) }}"
                                                        placeholder="ENTRI" required>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </form>
                                @endif

                            </div>

                            <div id="kodisi_agunan" class="tab-pane fade">
                                @if (is_null($agunan))
                                    <form
                                        action="{{ route('rsc.simpan.kondisi.agunan', ['kode' => $data->kode, 'rsc' => $data->rsc]) }}"
                                        method="POST">
                                        @method('post')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                            <div class="row" style="margin-top:5px;">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>POSISI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="posisi_agunan" id="posisi_agunan" placeholder="ENTRI"
                                                            value="{{ old('posisi_agunan') }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>KONDISI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="kondisi_agunan" id="kondisi_agunan" placeholder="ENTRI"
                                                            value="{{ old('kondisi_agunan') }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>NILAI TAKSASI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="nilai_agunan" id="nilai_agunan" placeholder="ENTRI"
                                                            value="{{ old('nilai_agunan') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </form>
                                @else
                                    <form action="" method="POST">
                                        @method('post')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                            <div class="row" style="margin-top:5px;">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>POSISI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="posisi_agunan" id="posisi_agunan" placeholder="ENTRI"
                                                            value="{{ old('posisi_agunan', $agunan->posisi_agunan) }}"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>KONDISI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="kondisi_agunan" id="kondisi_agunan" placeholder="ENTRI"
                                                            value="{{ old('kondisi_agunan', $agunan->kondisi_agunan) }}"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>NILAI TAKSASI AGUNAN</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            name="nilai_agunan" id="nilai_agunan" placeholder="ENTRI"
                                                            value="{{ old('nilai_agunan', number_format($agunan->nilai_taksasi, '0', ',', '.')) }}"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        $('.jns_usaha').select2()
        $('.faktor_teknis').select2()

        var nom_fe1 = document.getElementById('nominal_faktor_ekonomi1')
        var nom_fe2 = document.getElementById('nominal_faktor_ekonomi2')
        var biaya_rt = document.getElementById('biaya_rumah_tangga')
        var nilai_agunan = document.getElementById('nilai_agunan')

        if (nom_fe1) {
            nom_fe1.addEventListener("keyup", function(e) {
                nom_fe1.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (nom_fe2) {
            nom_fe2.addEventListener("keyup", function(e) {
                nom_fe2.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (biaya_rt) {
            biaya_rt.addEventListener("keyup", function(e) {
                biaya_rt.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (nilai_agunan) {
            nilai_agunan.addEventListener("keyup", function(e) {
                nilai_agunan.value = formatRupiah(this.value, "Rp. ");
            });
        }


        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
        }
    </script>
@endpush
