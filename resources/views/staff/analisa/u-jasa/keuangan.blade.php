@extends('staff.analisa.u-jasa.menu')
@section('title', 'Analisa Usaha Jasa')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    {{-- <input type="text" value="{{ $jasa->kd_usaha }}" name="kode_usaha" hidden> --}}

                    <div>
                        <span class="fw-bold">KODE USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="kode_usaha" value="AUPG00001" readonly>
                    </div>
                    <div style="margin-top: 5px;">
                        <span class="fw-bold">NAMA USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="nama_usaha" id="" placeholder="NAMA USAHA"
                        value="{{ old('nama_usaha') }}">
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">LAMA USAHA</span>
                        <select  class="form-control input-sm form-border" name="lama_usaha" id="">
                            <option value="">--PILIH--</option>
                            <option value="1 TAHUN">1 TAHUN</option>
                            <option value="2 TAHUN">2 TAHUN</option>
                            <option value="3 TAHUN">3 TAHUN</option>
                            <option value="4 TAHUN">4 TAHUN</option>
                            <option value=">5 TAHUN">>5 TAHUN</option>
                        </select>
                    </div>
                    <div style="margin-top: 5px;">
                        <span class="fw-bold">ALAMAT USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="lokasi_usaha" value="LOKASI USAHA">
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">PENDAPATAN USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="pendapatan"
                        id="pendapatan" placeholder="Rp. " value="{{ old('pendapatan') }}">
                    </div>
                </div>

                <div class="div-right">
                    <div>
                        <span class="fw-bold">PAJAK KENDARAAN</span>
                        <input class="form-control input-sm form-border" type="text" name="b_pajak" id="pajak" placeholder="Rp." value="{{ old('b_pajak') }}">
                    </div>
                    <div style="margin-top: 5px;">
                        <span class="fw-bold">PENGELUARAN LAINNYA</span>
                        <input class="form-control input-sm form-border" type="text" name="b_lainnya"
                        id="lainnya" placeholder="Rp. " value="{{ old('b_lainnya') }}">
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">TOTAL PENGHASILAN</span>
                        <input class="form-control input-sm form-border" type="text" name="totalpenghasilan"
                        id="tpenghasilan" value="Rp. " readonly>
                    </div>
                    <div style="margin-top: 5px;">
                        <span class="fw-bold">TOTAL PENGELUARAN</span>
                        <input class="form-control input-sm form-border" type="text" name="pengeluaran" id="tpengeluaran"
                        value="Rp." readonly>
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">HASIL USAHA BERSIH</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" value="Rp. " name="laba_bersih" id="laba">
                    </div>
                </div>

                <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
            </div>
        </form>
        
    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/jasa.js') }}"></script>
@endpush