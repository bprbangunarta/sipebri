@extends('staff.analisa.5c.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Kondisi')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.updatecondition', ['pengajuan' => $data->kd_pengajuan]) }}" method="POST">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">KONDISI ALAM</span>
                            <input type="text" name="kode_analisa" value="{{ $condition->kode_analisa }}" hidden>
                            <select class="form-control input-sm form-border text-uppercase" name="kondisi_alam"
                                id="condition1" required>
                                <option value="">--Pilih--</option>
                                <option value="5"
                                    {{ old('kondisi_alam') == 5 || $condition->kondisi_alam == '5' ? 'selected' : '' }}>
                                    Resiko Sangat Rendah
                                </option>
                                <option value="4"
                                    {{ old('kondisi_alam') == 4 || $condition->kondisi_alam == '4' ? 'selected' : '' }}>
                                    Resiko Rendah
                                </option>
                                <option value="3"
                                    {{ old('kondisi_alam') == 3 || $condition->kondisi_alam == '3' ? 'selected' : '' }}>
                                    Resiko Sedang
                                </option>
                                <option value="2"
                                    {{ old('kondisi_alam') == 2 || $condition->kondisi_alam == '2' ? 'selected' : '' }}>
                                    Resiko Tinggi
                                </option>
                                <option value="1"
                                    {{ old('kondisi_alam') == 1 || $condition->kondisi_alam == '1' ? 'selected' : '' }}>
                                    Resiko Sangat Tinggi
                                </option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">PERSAINGAN USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="persaingan_usaha"
                                id="condition2" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('persaingan_usaha') == 3 || $condition->persaingan_usaha == '3' ? 'selected' : '' }}>
                                    Persaingan Usaha
                                    Tidak Ketat</option>
                                <option value="2"
                                    {{ old('persaingan_usaha') == 2 || $condition->persaingan_usaha == '2' ? 'selected' : '' }}>
                                    Persaingan Usaha
                                    Kurang Ketat</option>
                                <option value="1"
                                    {{ old('persaingan_usaha') == 1 || $condition->persaingan_usaha == '1' ? 'selected' : '' }}>
                                    Persaingan Usaha
                                    Ketat</option>
                            </select>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">REGULASI PEMERINTAH</span>
                            <select class="form-control input-sm form-border text-uppercase" name="regulasi_pemerintah"
                                id="condition3">
                                <option value="">--PILIH--</option>
                                <option value="3" {{ $condition->regulasi_pemerintah == '3' ? 'selected' : '' }}>BAIK
                                </option>
                                <option value="2" {{ $condition->regulasi_pemerintah == '2' ? 'selected' : '' }}>CUKUP
                                    BAIK</option>
                                <option value="1" {{ $condition->regulasi_pemerintah == '1' ? 'selected' : '' }}>
                                    KURANG BAIK</option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">EVALUASI</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                name="evaluasi_condition" id="evaluasi_condition"
                                value="{{ $condition->evaluasi_condition ?? 'KOSONG' }}" readonly required>
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
        document.addEventListener("DOMContentLoaded", function() {
            //====ANALISA CONDITION====//
            var condition1 = document.getElementById("condition1");
            var condition2 = document.getElementById("condition2");
            var condition3 = document.getElementById("condition3");
            var analisa5 = document.getElementById("evaluasi_condition");

            condition1.addEventListener("change", condition);
            condition2.addEventListener("change", condition);
            condition3.addEventListener("change", condition);

            function condition() {
                var selectedcondition1 = parseInt(condition1.value) || 0;
                var selectedcondition2 = parseInt(condition2.value) || 0;
                var selectedcondition3 = parseInt(condition3.value) || 0;

                var jml = selectedcondition1 + selectedcondition2 + selectedcondition3;

                if (jml === 0 || jml <= 4) {
                    var nilai = "Kurang Baik";
                } else if (jml === 5 || jml <= 8) {
                    var nilai = "Cukup Baik";
                } else if (jml === 9 || jml <= 12) {
                    var nilai = "Baik";
                }
                analisa5.value = nilai;
            }
            //====ANALISA CONDITION====//
        });
    </script>
@endpush
