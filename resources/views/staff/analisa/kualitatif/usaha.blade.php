{{-- @extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan]) --}}
@extends('staff.analisa.kualitatif.menu')
@section('title', 'Analisa Kualitatif')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.simpancharacter') }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SUMBER BAHAN BAKU</span>
                            <input class="form-control input-sm form-border text-uppercase" name="bahan_baku" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">PROSES PENGOLAHAN</span>
                            <input class="form-control input-sm form-border text-uppercase" name="proses_olah" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">MARKET WILAYAH</span>
                            <input class="form-control input-sm form-border text-uppercase" name="target_market" id="" placeholder="ENTRI">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SISTEM PEMBAYARAN</span>
                            <input class="form-control input-sm form-border text-uppercase" name="pembayaran" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">FAKTOR PENDUKUNG USAHA</span>
                            <input class="form-control input-sm form-border text-uppercase" name="pendukung_usaha" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">FAKTOR PENGURANG USAHA</span>
                            <input class="form-control input-sm form-border text-uppercase" name="pengurang_usaha" id="" placeholder="ENTRI">
                        </div>
                    </div>

                    <div style="margin-top:5px;width: 100%;float:left;">
                        <span class="fw-bold">TRADE CHECKING</span>
                        <input class="form-control input-sm form-border text-uppercase" name="trade_checking" id="" placeholder="ENTRI">
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
