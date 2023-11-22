@extends('staff.analisa.u-lainnya.menu', [$data, 'pengajuan' => $data->kd_pengajuan, 'kode_usaha' => $lain->kd_usaha])
@section('title', 'Analisa Usaha Lainnya')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('lain.simpankeuangan', ['kode_usaha' => $lain->kd_usaha]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">NAMA PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nama1" value="{{ old('nama1') }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">NOMINAL PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="nominal1" id="nominal1" value="{{ old('nominal1') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">NAMA PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nama2" value="{{ old('nama2') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">NOMINAL PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="nominal2" id="nominal2" value="{{ old('nominal2') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">NAMA PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nama3" value="{{ old('nama3') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">NOMINAL PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="nominal3" id="nominal3" value="{{ old('nominal3') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">NAMA PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nama4" value="{{ old('nama4') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">NOMINAL PENDAPATAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="nominal4" id="nominal4" value="{{ old('nominal4') }}">
                        </div>


                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold text-red">PENGELUARAN UNTUK</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nampe1" value="{{ old('nampe1') }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold text-red">NOMINAL PENGELUARAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengeluaran1" id="pengeluaran1" value="{{ old('pengeluaran1') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold text-red">PENGELUARAN UNTUK</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nampe2" value="{{ old('nampe2') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold text-red">NOMINAL PENGELUARAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengeluaran2" id="pengeluaran2" value="{{ old('pengeluaran2') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold text-red">PENGELUARAN UNTUK</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nampe3" value="{{ old('nampe3') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold text-red">NOMINAL PENGELUARAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengeluaran3" id="pengeluaran3" value="{{ old('pengeluaran3') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold text-red">PENGELUARAN UNTUK</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                name="nampe4" value="{{ old('nampe4') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold text-red">NOMINAL PENGELUARAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengeluaran4" id="pengeluaran4" value="{{ old('pengeluaran4') }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div>
                            <span class="fw-bold">PENDAPATAN USAHA</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="Rp. {{ old('pendapatan') }}" name="pendapatan" id="penus" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA OPERASIONAL</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="Rp. {{ old('pengeluaran') }}" name="pengeluaran" id="biayaop" readonly>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA BAHAN BAKU</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="Rp. {{ old('bahan_baku') }}" name="bahan_baku" id="bahan_baku" readonly>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">PROYEKSI PENAMBAHAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="proyeksi" value="Rp. {{ old('proyeksi') }}" id="pph">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">HASIL USAHA BERSIH</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                value="Rp. {{ old('laba_bersih') }}" name="laba_bersih" id="hasilbersih">
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
    <script src="{{ asset('assets/js/myscript/keuangan_lain.js') }}"></script>
@endpush
