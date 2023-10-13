@extends('staff.analisa.u-pertanian.menu')
@section('title', 'Analisa Usaha Pertanian')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">PENDAPATAN HASIL PANEN</span>
                        <input class="form-control input-sm form-border" type="text" placeholder="Rp." name="pengolahan_tanah"
                        id="pengolahan" value="{{ old('pengolahan_tanah') }}">
                    </div>
                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">PENGELUARAN BIAYA USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="bibit"
                        placeholder="Rp." id="bibit" value="{{ old('bibit') }}">
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">PENAMBAHAN HASIL USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="pupuk"
                        placeholder="Rp." id="pupuk" value="{{ old('pupuk') }}">
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">PINJAMAN BANK LAIN</span>
                        <input class="form-control input-sm form-border" type="text" placeholder="Rp." name="pestisida"
                        id="pestisida" value="{{ old('pestisida') }}">
                    </div>
                </div>

                <div class="div-right">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">HASIL BERSIH USAHA</span>
                        <input class="form-control input-sm form-border" type="text" value="Rp. {{ old('pendapatan') }}" name="pendapatan"
                        id="pendapatan" readonly>
                    </div>
                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">AMBIL 70%</span>
                        <input class="form-control input-sm form-border" type="text" value="Rp. " name="ambil_70"
                        id="ambil_70" readonly>
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">SAVING POKOK</span>
                        <input class="form-control input-sm form-border" type="text" value="Rp. " name="saving_pokok"
                        id="saving_pokok" readonly>
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">PENDAPATAN PERBULAN</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" value="Rp. {{ old('laba_perbulan') }}"
                        name="laba_perbulan" id="lb_perbulan" readonly>
                    </div>
                </div>

                <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
            </div>
        </form>


    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/pertanian.js') }}"></script>
@endpush