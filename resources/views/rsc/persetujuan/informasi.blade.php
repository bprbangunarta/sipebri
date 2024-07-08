@extends('theme.app')
@section('title', 'Informasi RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-persetujuan', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active text-bold">
                                <a href="#informasi" data-toggle="tab">INFORMASI</a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <div id="informasi" class="tab-pane fade in active">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                    <div class="div-left">
                                        <div style="margin-top:5px;width: 100%;float:left;">
                                            <label>ALAMAT</label>
                                            <input type="text" class="form-control text-uppercase"
                                                style="font-size: 12px;" name="posisi_agunan" id="posisi_agunan"
                                                placeholder="ENTRI" value="{{ old('posisi_agunan', $data->alamat_ktp) }}"
                                                readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <label>TUNGGAKAN POKOK</label>
                                            <input type="text" class="form-control text-uppercase" name="kondisi_agunan"
                                                id="kondisi_agunan" placeholder="ENTRI"
                                                value="{{ old('kondisi_agunan', number_format($data->tunggakan_poko, '0', ',', '.')) }}"
                                                readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <label>TUNGGAKAN BUNGA</label>
                                            <input type="text" class="form-control text-uppercase" name="nilai_agunan"
                                                id="nilai_agunan" placeholder="ENTRI"
                                                value="{{ old('nilai_agunan', number_format($data->tunggakan_bunga, '0', ',', '.')) }}"
                                                readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <label>POSISI AGUNAN</label>
                                            <input type="text" class="form-control text-uppercase" name="nilai_agunan"
                                                id="nilai_agunan" placeholder="ENTRI"
                                                value="{{ old('nilai_agunan', $data->posisi_agunan) }}" readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <label>KONDISI AGUNAN</label>
                                            <input type="text" class="form-control text-uppercase" name="nilai_agunan"
                                                id="nilai_agunan" placeholder="ENTRI"
                                                value="{{ old('nilai_agunan', $data->kondisi_agunan) }}" readonly>
                                        </div>

                                    </div>

                                    <div class="div-right">
                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <label>KASI</label>
                                            <input type="text" class="form-control text-uppercase" name="kondisi_agunan"
                                                id="kondisi_agunan" placeholder="ENTRI"
                                                value="{{ old('kondisi_agunan', $data->kasi) }}" readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <label>SURVEYOR</label>
                                            <input type="text" class="form-control text-uppercase" name="nilai_agunan"
                                                id="nilai_agunan" placeholder="ENTRI"
                                                value="{{ old('nilai_agunan', $data->surveyor) }}" readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <label>TUNGGAKAN DENDA</label>
                                            <input type="text" class="form-control text-uppercase" name="nilai_agunan"
                                                id="nilai_agunan" placeholder="ENTRI"
                                                value="{{ old('nilai_agunan', number_format($data->tunggakan_denda, '0', ',', '.')) }}"
                                                readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <label>TOTAL TUNGGAKAN</label>
                                            <input type="text" class="form-control text-uppercase" name="kondisi_agunan"
                                                id="kondisi_agunan" placeholder="ENTRI"
                                                value="{{ old('kondisi_agunan', number_format($data->total_tunggakan, '0', ',', '.')) }}"
                                                readonly>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <label>NILAI TAKSASI</label>
                                            <input type="text" class="form-control text-uppercase" name="kondisi_agunan"
                                                id="kondisi_agunan" placeholder="ENTRI"
                                                value="{{ old('kondisi_agunan', number_format($data->nilai_taksasi, '0', ',', '.')) }}"
                                                readonly>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
