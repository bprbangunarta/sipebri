@extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
{{-- @extends('staff.analisa.kualitatif.menu') --}}
@section('title', 'Analisa Kualitatif')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                    <div>
                        <span class="fw-bold">KEKUATAN (STRENGTH)</span>
                        <input class="form-control input-sm form-border" name="kekuatan" id="kekuatan" placeholder="ENTRI" >
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">KELEMAHAN (WEAKNESS)</span>
                        <input class="form-control input-sm form-border" name="kelemahan" id="kelemahan" placeholder="ENTRI" >
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">PELUANG (OPPORTUNITIES)</span>
                        <input class="form-control input-sm form-border" name="peluang" id="peluang" placeholder="ENTRI" >
                    </div>

                    <div style="margin-top: 5px;">
                        <span class="fw-bold">ANCAMAN (TREATS)</span>
                        <input class="form-control input-sm form-border" name="ancaman" id="ancaman" placeholder="ENTRI" >
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/kualitatif.js') }}"></script>
@endpush
