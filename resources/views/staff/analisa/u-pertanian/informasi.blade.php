@extends('staff.analisa.u-pertanian.menu')
@section('title', 'Analisa Usaha Pertanian')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="" method="POST">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                {{-- <input type="text" id="plafon" value="{{ $data->plafon }}" hidden>
                <input type="text" id="jangka_waktu" value="{{ $data->jangka_waktu }}" hidden> --}}

                <div class="div-left">
                    <div>
                        <span class="fw-bold">KODE USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="kode_usaha" value="AUPG00001" readonly>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">NAMA USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="nama_usaha" value="SAWAH PADI KETAN">
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">ALAMAT USAHA</span>
                        <input class="form-control input-sm form-border" type="text" name="lokasi_usaha" value="LOKASI USAHA">
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">SEKTOR EKONOMI</span>
                        <select  class="form-control input-sm form-border" name="jenis_usaha" id="">
                            <option value="">--PILIH--</option>
                            <option value="PERTANIAN">PERTANIAN</option>
                            <option value="PERKEBUNAN">PERKEBUNAN</option>
                        </select>
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">JENIS TANAMAN</span>
                        <select  class="form-control input-sm form-border" name="jenis_tanaman" id="">
                            <option value="">--PILIH--</option>
                            <option value="PADI KETAN">PADI KETAN</option>
                            <option value="PADI INPARI">PADI INPARI</option>
                        </select>
                    </div>
                </div>

                <div class="div-right">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">LUAS MILIK SENDIRI</span>
                        <input class="form-control input-sm form-border" type="text" name="luas_sendiri" id="lsendiri"
                        placeholder="0" value="{{ old('luas_sendiri') }}">
                    </div>
                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">LUAS HASIL SEWA</span>
                        <input class="form-control input-sm form-border" type="text" name="luas_sewa" id="lsewa"
                        placeholder="0" value="{{ old('luas_sewa') }}">
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">LUAS HASIL GADAI</span>
                        <input class="form-control input-sm form-border" type="text" name="luas_gadai" id="lgadai"
                        placeholder="0" value="{{ old('luas_gadai') }}">
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">TOTAL LUAS TANAH</span>
                        <input class="form-control input-sm form-border" type="text" name="total_tanah" id="total_tanah"
                        value="{{ old('lokasi_usaha') ?? 0 }}">
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">HASIL PANEN PER KW</span>
                        <input class="form-control input-sm form-border" type="text" name="hasil_panen" id="hpanen"
                        placeholder="0" value="{{ old('hasil_panen') }}">
                    </div>
                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">HARGA PER KWINTAN</span>
                        <input class="form-control input-sm form-border" type="text" name="harga"
                        id="hrg" placeholder="Rp." value="{{ old('harga') }}">
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