@extends('staff.analisa.5c.menu')
@section('title', 'Analisa Kondisi')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">KONDISI ALAM</span>
                        <select class="form-control input-sm form-border text-uppercase" name="kondisi_alam" id="condition1" required>
                            <option value="">--Pilih--</option>
                            <option value="5" {{ old('kondisi_alam') == 5 ? 'selected' : '' }}>Resiko Sangat Rendah</option>
                            <option value="4" {{ old('kondisi_alam') == 4 ? 'selected' : '' }}>Resiko Rendah</option>
                            <option value="3" {{ old('kondisi_alam') == 3 ? 'selected' : '' }}>Resiko Sedang</option>
                            <option value="2" {{ old('kondisi_alam') == 2 ? 'selected' : '' }}>Resiko Tinggi</option>
                            <option value="1" {{ old('kondisi_alam') == 1 ? 'selected' : '' }}>Resiko Sangat Tinggi</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">PERSAINGAN USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="persaingan_usaha" id="condition2" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('persaingan_usaha') == 3 ? 'selected' : '' }}>Persaingan Usaha Tidak Ketat</option>
                            <option value="2" {{ old('persaingan_usaha') == 2 ? 'selected' : '' }}>Persaingan Usaha Kurang Ketat</option>
                            <option value="1" {{ old('persaingan_usaha') == 1 ? 'selected' : '' }}>Persaingan Usaha Ketat</option>
                        </select>
                    </div>
                </div>


                <div class="div-right">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">REGULASI PEMERINTAH</span>
                        <select class="form-control input-sm form-border text-uppercase" name="kursi" id="">
                            <option value="">--PILIH--</option>
                            <option value="3">BAIK</option>
                            <option value="2">CUKUP BAIK</option>
                            <option value="1">KURANG BAIK</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">EVALUASI</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" name="evaluasi_condition" id="evaluasi_condition"
                        value="{{ old('evaluasi_condition') ?? 'KOSONG' }}" readonly required>
                    </div>
                </div>

                <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endpush