@extends('staff.analisa.5c.menu')
@section('title', 'Analisa Modal')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div style="width: 100%;float:left;">
                        <span class="fw-bold">SUMBER MODAL</span>
                        <select class="form-control input-sm form-border text-uppercase" name="capital_sumber_modal" id="capital" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('capital_sumber_modal') == 3 ? 'selected' : '' }}>Modal Sendiri</option>
                            <option value="2" {{ old('capital_sumber_modal') == 2 ? 'selected' : '' }}>Kerjasama</option>
                            <option value="1" {{ old('capital_sumber_modal') == 1 ? 'selected' : '' }}>Pihak Lain</option>
                        </select>
                    </div>
                </div>


                <div class="div-right">
                    <div style="width: 100%;float:right;">
                        <span class="fw-bold">EVALUASI</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" name="capital_evaluasi_capital" id="evaluasi_capital"
                        value="{{ old('capital_evaluasi_capital') ?? 'KOSONG' }}" readonly required>
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