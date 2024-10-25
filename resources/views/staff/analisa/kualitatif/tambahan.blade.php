@extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Tambahan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('kualitatif.simpan.tambahan', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @method('post')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div style="margin-top:5px;width: 100%;float:left;">
                        <span class="fw-bold">TRADE CHECKING USAHA</span>
                        <textarea class="form-control form-border text-uppercase" name="trade_checking_usaha" id="" rows="10">{{ $trade->checking_usaha }}</textarea>
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
