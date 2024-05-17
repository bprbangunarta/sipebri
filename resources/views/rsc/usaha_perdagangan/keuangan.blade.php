@extends('rsc.usaha_perdagangan.menu', [$data])
@section('title', 'Analisa Usaha Perdagangan RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            @if (is_null($biaya_perdagangan))
                <form action="{{ route('rsc.usaha.perdagangan.keuangan.simpan', ['kode_usaha' => $data->kode_usaha]) }}"
                    method="POST">
                    @method('post')
                    @csrf
                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                        <div class="div-left">
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold">BELANJA HARIAN</span>
                                <input type="text" id="tpersen" value="{{ $perdagangan->total_pl }}" hidden>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="brdg" name="belanja_harian" value="{{ old('belanja_harian') }}">
                            </div>
                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold">OMSET HARIAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="penhar" name="omset_harian" readonly>
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">POKOK PENJUALAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="popen" name="pokok_penjualan" readonly>
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">LABA BERSIH HARIAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="lahar" name="laba_harian" readonly>
                            </div>

                            <div style="margin-top:20px;width: 49.5%;float:left;">
                                <span class="fw-bold">TRANSPORTASI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="transport" name="transportasi" value="{{ old('transportasi') }}" required>
                            </div>
                            <div style="margin-top:20px;width: 49.5%;float:right;">
                                <span class="fw-bold">BONGKAR MUAT (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="bongkar" name="bongkar_muat" value="{{ old('bongkar_muat') }}">
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">PEGAWAI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="pegawai" name="pegawai" value="{{ old('pegawai') }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">GATEL (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="gatel" name="gatel" value="{{ old('gatel') }}">
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">RETRIBUSI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="retri" name="retribusi" value="{{ old('retribusi') }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">SEWA TEMPAT (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="sewa" name="sewa_tempat" value="{{ old('sewa_tempat') }}">
                            </div>
                        </div>

                        <div class="div-right">
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold">LABA BULANAN</span>
                                <input class="form-control input-sm form-border" type="text"
                                    value="Rp. {{ old('pendapatan') }}" id="lbulan" name="pendapatan" readonly>
                            </div>
                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold">BIAYA BULANAN</span>
                                <input class="form-control input-sm form-border" type="text"
                                    value="Rp. {{ old('pengeluaran') }}" id="bdagang" name="pengeluaran" readonly>
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">PROYEKSI PENAMBAHAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="penambahan" name="penambahan" value="Rp. {{ old('penambahan') }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">HASIL BERSIH USAHA</span>
                                <input class="form-control input-sm form-border bg-blue" type="text"
                                    value="Rp. {{ old('laba_bersih') }}" id="hasilbersih" name="laba_bersih" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary"
                            style="margin-top:10px;width:100%">SIMPAN</button>
                    </div>
                </form>
            @else
                <form action="{{ route('rsc.usaha.perdagangan.keuangan.update', ['kode_usaha' => $data->kode_usaha]) }}"
                    method="POST">
                    @method('post')
                    @csrf
                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                        <div class="div-left">
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold">BELANJA HARIAN</span>
                                <input type="text" id="tpersen" value="{{ $perdagangan->total_pl }}" hidden>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="brdg" name="belanja_harian"
                                    value="{{ old('belanja_harian', number_format($perdagangan->belanja_harian, '0', ',', '.')) }}">
                            </div>
                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold">OMSET HARIAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="penhar" name="omset_harian"
                                    value="{{ old('omset_harian', number_format($perdagangan->omset_harian, '0', ',', '.')) }}"
                                    readonly>
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">POKOK PENJUALAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="popen" name="pokok_penjualan"
                                    value="{{ old('pokok_penjualan', number_format($perdagangan->pokok_penjualan, '0', ',', '.')) }}"
                                    readonly>
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">LABA BERSIH HARIAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="lahar" name="laba_harian"
                                    value="{{ old('laba_harian', number_format($perdagangan->laba_harian, '0', ',', '.')) }}"
                                    readonly>
                            </div>

                            <div style="margin-top:20px;width: 49.5%;float:left;">
                                <span class="fw-bold">TRANSPORTASI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="transport" name="transportasi"
                                    value="{{ old('transportasi', number_format($biaya_perdagangan->transportasi, '0', ',', '.')) }}"
                                    required>
                            </div>
                            <div style="margin-top:20px;width: 49.5%;float:right;">
                                <span class="fw-bold">BONGKAR MUAT (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="bongkar" name="bongkar_muat"
                                    value="{{ old('bongkar_muat', number_format($biaya_perdagangan->bongkar_muat, '0', ',', '.')) }}">
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">PEGAWAI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="pegawai" name="pegawai"
                                    value="{{ old('pegawai', number_format($biaya_perdagangan->pegawai, '0', ',', '.')) }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">GATEL (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="gatel" name="gatel"
                                    value="{{ old('gatel', number_format($biaya_perdagangan->gatel, '0', ',', '.')) }}">
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">RETRIBUSI (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="retri" name="retribusi"
                                    value="{{ old('retribusi', number_format($biaya_perdagangan->retribusi, '0', ',', '.')) }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">SEWA TEMPAT (HARIAN)</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="sewa" name="sewa_tempat"
                                    value="{{ old('sewa_tempat', number_format($biaya_perdagangan->sewa_tempat, '0', ',', '.')) }}">
                            </div>
                        </div>

                        <div class="div-right">
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold">LABA BULANAN</span>
                                <input class="form-control input-sm form-border" type="text"
                                    value="Rp. {{ old('pendapatan', number_format($perdagangan->pendapatan, '0', ',', '.')) }}"
                                    id="lbulan" name="pendapatan" readonly>
                            </div>
                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold">BIAYA BULANAN</span>
                                <input class="form-control input-sm form-border" type="text"
                                    value="Rp. {{ old('pengeluaran', number_format($perdagangan->pengeluaran, '0', ',', '.')) }}"
                                    id="bdagang" name="pengeluaran" readonly>
                            </div>

                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                <span class="fw-bold">PROYEKSI PENAMBAHAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                    id="penambahan" name="penambahan"
                                    value="Rp. {{ old('penambahan', number_format($perdagangan->penambahan, '0', ',', '.')) }}">
                            </div>
                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                <span class="fw-bold">HASIL BERSIH USAHA</span>
                                <input class="form-control input-sm form-border bg-blue" type="text"
                                    value="Rp. {{ old('laba_bersih', number_format($perdagangan->laba_bersih, '0', ',', '.')) }}"
                                    id="hasilbersih" name="laba_bersih" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary"
                            style="margin-top:10px;width:100%">SIMPAN</button>
                    </div>
                </form>
            @endif



        </div>
    </div>
@endsection
@push('myscript')
    <script src="{{ asset('assets/js/myscript/keuangan_perdagangan.js') }}"></script>
@endpush
