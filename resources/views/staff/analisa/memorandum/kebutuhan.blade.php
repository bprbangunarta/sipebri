@extends('staff.analisa.memorandum.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Memorandum')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('memorandum.simpankebutuhan', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">MODAL KERJA</span>
                            <input type="text" class="form-control text-uppercase" name="modal_kerja" id="modal"
                                placeholder="Rp.">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">INVESTASI</span>
                            <input type="text" class="form-control text-uppercase" name="investasi" id="inves"
                                placeholder="Rp.">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">KONSUMTIF</span>
                            <input type="text" class="form-control text-uppercase" name="konsumtif" id="konsumtif"
                                placeholder="Rp.">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">PELUNASAN KREDIT</span>
                            <input type="text" class="form-control text-uppercase" name="pelunasan_kredit" id="pelunasan"
                                placeholder="Rp.">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">TAKE OVER</span>
                            <input type="text" class="form-control text-uppercase" name="take_over" id="take_over"
                                placeholder="Rp.">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">JUMLAH KEBUTUHAN DANA</span>
                            <input type="text" class="form-control text-uppercase bg-blue" name="kebutuhan_dana"
                                id="kebutuhan_dana" value="Rp." readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/memorandum_kebutuhan_dana.js') }}"></script>
@endpush
