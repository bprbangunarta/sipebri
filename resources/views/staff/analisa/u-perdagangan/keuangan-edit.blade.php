@extends('staff.analisa.u-perdagangan.menu', [$data, 'kode_usaha' => $perdagangan->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Perdagangan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('perdagangan.updatekeuangan', ['kode_usaha' => $perdagangan->kd_usaha]) }}" method="POST">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">BELANJA HARIAN</span>
                            <input type="text" id="tpersen" value="{{ $perdagangan->total_pl }}" hidden>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="brdg" name="belanja_harian"
                                value="{{ old('belanja_harian') ?? ($perdagangan->belanja_harian = 'Rp. ' . number_format($perdagangan->belanja_harian, 0, ',', '.')) }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">OMSET HARIAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="penhar" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">POKOK PENJUALAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="popen" readonly>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">LABA BERSIH HARIAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="lahar" readonly>
                        </div>

                        <div style="margin-top:20px;width: 49.5%;float:left;">
                            <span class="fw-bold">TRANSPORTASI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="transport" name="transportasi"
                                value="{{ old('transportasi') ?? ($keuangan->transportasi = 'Rp. ' . number_format($keuangan->transportasi, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:20px;width: 49.5%;float:right;">
                            <span class="fw-bold">BONGKAR MUAT</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="bongkar" name="bongkar_muat"
                                value="{{ old('bongkar_muat') ?? ($keuangan->bongkar_muat = 'Rp. ' . number_format($keuangan->bongkar_muat, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PEGAWAI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="pegawai" name="pegawai"
                                value="{{ old('pegawai') ?? ($keuangan->pegawai = 'Rp. ' . number_format($keuangan->pegawai, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">GATEL</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="gatel" name="gatel"
                                value="{{ old('gatel') ?? ($keuangan->gatel = 'Rp. ' . number_format($keuangan->gatel, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">RETRIBUSI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="retri" name="retribusi"
                                value="{{ old('retribusi') ?? ($keuangan->retribusi = 'Rp. ' . number_format($keuangan->retribusi, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">SEWA TEMPAT</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="sewa" name="sewa_tempat"
                                value="{{ old('sewa_tempat') ?? ($keuangan->sewa_tempat = 'Rp. ' . number_format($keuangan->sewa_tempat, 0, ',', '.')) }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">LABA PERBULAN</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ old('pendapatan') ?? ($perdagangan->pendapatan = 'Rp. ' . number_format($perdagangan->pendapatan, 0, ',', '.')) }}"
                                id="lbulan" name="pendapatan" readonly>
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA PERBULAN</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ old('pengeluaran') ?? ($perdagangan->pengeluaran = 'Rp. ' . number_format($perdagangan->pengeluaran, 0, ',', '.')) }}"
                                id="bdagang" name="pengeluaran" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PROYEKSI PENAMBAHAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="penambahan" name="penambahan"
                                value="{{ old('penambahan') ?? ($perdagangan->penambahan = 'Rp. ' . number_format($perdagangan->penambahan, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">HASIL BERSIH USAHA</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                value="{{ old('laba_bersih') ?? ($perdagangan->laba_bersih = 'Rp. ' . number_format($perdagangan->laba_bersih, 0, ',', '.')) }}"
                                id="hasilbersih" name="laba_bersih" readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/keuangan_perdagangan.js') }}"></script>
@endpush
