@extends('staff.analisa.memorandum.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Memorandum')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('memorandum.updatekebutuhan', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">MODAL KERJA</span>
                            <input type="text" class="form-control text-uppercase" name="modal_kerja" id="modal"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->modal_kerja, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">INVESTASI</span>
                            <input type="text" class="form-control text-uppercase" name="investasi" id="inves"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->investasi, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">KONSUMTIF</span>
                            <input type="text" class="form-control text-uppercase" name="konsumtif" id="konsumtif"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->konsumtif, 0, ',', '.') }}">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">PELUNASAN KREDIT</span>
                            <input type="text" class="form-control text-uppercase" name="pelunasan_kredit" id="pelunasan"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->pelunasan_kredit, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">TAKE OVER</span>
                            <input type="text" class="form-control text-uppercase" name="take_over" id="take_over"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->take_over, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">JUMLAH KEBUTUHAN DANA</span>
                            <input type="text" class="form-control text-uppercase bg-blue" name="kebutuhan_dana"
                                id="kebutuhan_dana"
                                value="{{ 'Rp. ' . ' ' . number_format($kebutuhan->kebutuhan_dana, 0, ',', '.') }}"
                                readonly>
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
