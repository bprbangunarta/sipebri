@extends('staff.analisa.u-perdagangan.menu')
@section('title', 'Analisa Usaha Perdagangan')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div>
                        <span class="fw-bold">KODE USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="kode_usaha" value="AUPG00001" readonly>
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
                </div>

                <div class="div-right">
                    <div>
                        <span class="fw-bold">NAMA USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="nama_usaha" value="WARUNG MIRNA">
                    </div>
                    <div style="margin-top: 5px;">
                        <span class="fw-bold">ALAMAT USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="lokasi_usaha" value="LOKASI USAHA">
                    </div>
                </div>

                <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
            </div>
        </form>


    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/perdagangan.js') }}"></script>
@endpush