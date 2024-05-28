@extends('theme.app')
@section('title', 'Data Pengusulan')

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
                                <a href="#pengajuan" data-toggle="tab">DATA PENGUSULAN</a>
                            </li>
                        </ul>

                        <form action="{{ route('rsc.data.pengusulan.simpan', ['rsc' => $data->rsc]) }}" method="POST">
                            @method('post')
                            @csrf
                            <div class="tab-content">

                                <div class="tab-pane active" id="pengajuan">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PLAFON</span>
                                                <input type="hidden" id='keuangan'
                                                    value="{{ $keuangan->keuangan_perbulan }}">
                                                <input type="text" class="form-control" name="plafon" id="plafon"
                                                    placeholder="10.000.000"
                                                    value="{{ number_format($pengusulan->penentuan_plafon, '0', ',', '.') ?? 0 }}"
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">JK KREDIT (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_waktu"
                                                    id="jangka_waktu" placeholder="ENTRI"
                                                    value="{{ old('jangka_waktu', $pengusulan->jangka_waktu) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">METODE RPS</span>
                                                <select class="form-control text-uppercase metode" name="metode_rps"
                                                    id="select-metodes" required>
                                                    <option value="">-- PILIH -- </option>
                                                    <option value="FLAT"
                                                        {{ old('metode_rps') == 'FLAT' || $pengusulan->metode_rps == 'FLAT' ? 'selected' : '' }}>
                                                        FLAT
                                                    </option>
                                                    <option value="PRK"
                                                        {{ old('metode_rps') == 'PRK' || $pengusulan->metode_rps == 'PRK' ? 'selected' : '' }}>
                                                        PRK
                                                    </option>
                                                    <option value="EFEKTIF"
                                                        {{ old('metode_rps') == 'EFEKTIF' || $pengusulan->metode_rps == 'EFEKTIF' ? 'selected' : '' }}>
                                                        EFEKTIF
                                                    </option>
                                                    <option value="EFEKTIF ANUITAS"
                                                        {{ old('metode_rps') == 'EFEKTIF ANUITAS' || $pengusulan->metode_rps == 'EFEKTIF ANUITAS' ? 'selected' : '' }}>
                                                        EFEKTIF ANUITAS
                                                    </option>
                                                    <option value="EFEKTIF MUSIMAN"
                                                        {{ old('metode_rps') == 'EFEKTIF MUSIMAN' || $pengusulan->metode_rps == 'EFEKTIF MUSIMAN' ? 'selected' : '' }}>
                                                        EFEKTIF MUSIMAN
                                                    </option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">SUKU BUNGA</span>
                                                <input type="text" class="form-control" name="suku_bunga" id="suku_bunga"
                                                    placeholder="ENTRI"
                                                    value="{{ old('suku_bunga', $pengusulan->suku_bunga) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">ANGSURAN BUNGA</span>
                                                <input type="text" class="form-control" name="angsuran_bunga"
                                                    id="angsuran_bunga" placeholder="ENTRI"
                                                    value="{{ old('angsuran_bunga', number_format($pengusulan->angsuran_bunga, '0', ',', '.')) }}"
                                                    required readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TOTAL ANGSURAN</span>
                                                <input type="text" class="form-control" name="total_angsuran"
                                                    id="total_angsuran" placeholder="ENTRI"
                                                    value="{{ old('total_angsuran', number_format($pengusulan->total_angsuran, '0', ',', '.')) }}"
                                                    required readonly>
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JK BUNGA (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_bunga"
                                                    id="jangka_bunga" placeholder="ENTRI"
                                                    value="{{ old('jangka_bunga', $pengusulan->jangka_bunga) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">JK POKOK (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_pokok"
                                                    id="jangka_pokok" placeholder="ENTRI"
                                                    value="{{ old('jangka_pokok', $pengusulan->jangka_pokok) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">RC (%)</span>
                                                <input type="text" class="form-control" name="rc" id="rc"
                                                    placeholder="ENTRI" value="{{ old('rc', $pengusulan->rc) }}" required
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ANGSURAN POKOK</span>
                                                <input type="text" class="form-control" name="angsuran_pokok"
                                                    id="angsuran_pokok" placeholder="ENTRI"
                                                    value="{{ old('angsuran_pokok', number_format($pengusulan->angsuran_pokok, '0', ',', '.')) }}"
                                                    required readonly>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="box-body" style="margin-top:-20px;">
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:10px;width:100%">SIMPAN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        $('.metode').select2()
    </script>
    <script src="{{ asset('assets/js/myscript/rsc_usulan_plafon.js') }}"></script>
@endpush
