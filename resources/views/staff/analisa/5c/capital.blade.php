@extends('staff.analisa.5c.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Modal')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.simpancapital', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SUMBER MODAL</span>
                            <input type="text" value="{{ $capital->kode_analisa }}" name="kode_analisa" hidden>
                            <select class="form-control input-sm form-border text-uppercase" name="capital_sumber_modal"
                                id="capital" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('capital_sumber_modal') == 3 || $capital->capital_sumber_modal == '3' ? 'selected' : '' }}>
                                    Modal
                                    Sendiri</option>
                                <option value="2"
                                    {{ old('capital_sumber_modal') == 2 || $capital->capital_sumber_modal == '2' ? 'selected' : '' }}>
                                    Kerjasama
                                </option>
                                <option value="1"
                                    {{ old('capital_sumber_modal') == 1 || $capital->capital_sumber_modal == '1' ? 'selected' : '' }}>
                                    Pihak Lain
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:right;">
                            <span class="fw-bold">EVALUASI</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                name="capital_evaluasi_capital" id="evaluasi_capital"
                                value="{{ $capital->capital_evaluasi_capital ?? 'KOSONG' }}" readonly required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script>
        //====ANALISA CAPITAL====//
        var capital = document.getElementById("capital");
        var analisa3 = document.getElementById("evaluasi_capital");

        capital.addEventListener("change", capitals);

        function capitals() {
            var selectedcapital = parseInt(capital.value) || 0;

            if (selectedcapital === 0 || selectedcapital == 1) {
                var nilai = "Kurang Baik";
            } else if (selectedcapital === 9 || selectedcapital == 2) {
                var nilai = "Cukup Baik";
            } else if (selectedcapital === 17 || selectedcapital == 3) {
                var nilai = "Baik";
            }
            analisa3.value = nilai;
        }
        //====ANALISA CAPITAL====//
    </script>
@endpush
