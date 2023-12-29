@extends('staff.analisa.5c.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Karakter')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.updatecharacter', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">GAYA HIDUP</span>
                            <select class="form-control input-sm form-border text-uppercase" name="gaya_hidup" id="select1"
                                required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('gaya_hidup') == 3 || $character->gaya_hidup == '3' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="2"
                                    {{ old('gaya_hidup') == 2 || $character->gaya_hidup == '2' ? 'selected' : '' }}>Cukup
                                    Baik</option>
                                <option value="1"
                                    {{ old('gaya_hidup') == 1 || $character->gaya_hidup == '1' ? 'selected' : '' }}>Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">PENGENDALIAN EMOSI</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pengendalian_emosi"
                                id="select3" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('pengendalian_emosi') == 3 || $character->pengendalian_emosi == '3' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="2"
                                    {{ old('pengendalian_emosi') == 2 || $character->pengendalian_emosi == '2' ? 'selected' : '' }}>
                                    Cukup Baik
                                </option>
                                <option value="1"
                                    {{ old('pengendalian_emosi') == 1 || $character->pengendalian_emosi == '1' ? 'selected' : '' }}>
                                    Kurang Baik
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">MELAKUKAN TINDAKAN TERCELA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="perbuatan_tercela"
                                id="select5" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('perbuatan_tercela') == 3 || $character->perbuatan_tercela == '3' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="2"
                                    {{ old('perbuatan_tercela') == 2 || $character->perbuatan_tercela == '2' ? 'selected' : '' }}>
                                    Cukup Baik
                                </option>
                                <option value="1"
                                    {{ old('perbuatan_tercela') == 1 || $character->perbuatan_tercela == '1' ? 'selected' : '' }}>
                                    Kurang Baik
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KEHARMONISAN KELUARGA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="harmonis" id="select7"
                                required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('harmonis') == 3 || $character->harmonis == '3' ? 'selected' : '' }}>Baik
                                </option>
                                <option value="2"
                                    {{ old('harmonis') == 2 || $character->harmonis == '2' ? 'selected' : '' }}>Cukup Baik
                                </option>
                                <option value="1"
                                    {{ old('harmonis') == 1 || $character->harmonis == '1' ? 'selected' : '' }}>Kurang Baik
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">TERBUKA DAN KONSISTEN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="konsisten" id="select2"
                                required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('konsisten') == 3 || $character->konsisten == '3' ? 'selected' : '' }}>Baik
                                </option>
                                <option value="2"
                                    {{ old('konsisten') == 2 || $character->konsisten == '2' ? 'selected' : '' }}>Cukup
                                    Baik</option>
                                <option value="1"
                                    {{ old('konsisten') == 1 || $character->konsisten == '1' ? 'selected' : '' }}>Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">KEPATUHAN KEWAJIBAN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kepatuhan" id="select4"
                                required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('kepatuhan') == 3 || $character->kepatuhan == '3' ? 'selected' : '' }}>Baik
                                </option>
                                <option value="2"
                                    {{ old('kepatuhan') == 2 || $character->kepatuhan == '2' ? 'selected' : '' }}>Cukup
                                    Baik</option>
                                <option value="1"
                                    {{ old('kepatuhan') == 1 || $character->kepatuhan == '1' ? 'selected' : '' }}>Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">HUBUNGAN SOSIAL</span>
                            <select class="form-control input-sm form-border text-uppercase" name="hubungan_sosial"
                                id="select6" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('hubungan_sosial') == 3 || $character->hubungan_sosial == '3' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="2"
                                    {{ old('hubungan_sosial') == 2 || $character->hubungan_sosial == '2' ? 'selected' : '' }}>
                                    Cukup Baik
                                </option>
                                <option value="1"
                                    {{ old('hubungan_sosial') == 1 || $character->hubungan_sosial == '1' ? 'selected' : '' }}>
                                    Kurang Baik
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">EVALUASI</span>
                            <input class="form-control input-sm form-border text-uppercase bg-blue" type="text"
                                name="nilai_karakter" id="n1"
                                value="{{ old('nilai_karakter', $character->nilai_karakter) }}" readonly required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endpush
