@extends('staff.analisa.u-perdagangan.menu', [$data, 'kode_usaha' => $perdagangan->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Perdagangan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('perdagangan.simpankeuangan', ['kode_usaha' => $perdagangan->kd_usaha]) }}" method="POST">
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
                                id="transport" name="transportasi" value="{{ old('transportasi') }}" required>
                        </div>
                        <div style="margin-top:20px;width: 49.5%;float:right;">
                            <span class="fw-bold">BONGKAR MUAT</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="bongkar" name="bongkar_muat" value="{{ old('bongkar_muat') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PEGAWAI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="pegawai" name="pegawai" value="{{ old('pegawai') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">GATEL</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="gatel" name="gatel" value="{{ old('gatel') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">RETRIBUSI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="retri" name="retribusi" value="{{ old('retribusi') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">SEWA TEMPAT</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp. "
                                id="sewa" name="sewa_tempat" value="{{ old('sewa_tempat') }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">LABA HARIAN</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="Rp. {{ old('pendapatan') }}" id="lbulan" name="pendapatan" readonly>
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA HARIAN</span>
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

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/keuangan_perdagangan.js') }}"></script>
@endpush
