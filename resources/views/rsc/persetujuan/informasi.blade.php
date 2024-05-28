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
                            <li class="active">
                                <a href="#informasi" data-toggle="tab">INFORMASI</a>
                            </li>

                            <li>
                                <a href="#data_lanjutan" data-toggle="tab">DATA LANJUTAN</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div id="informasi" class="tab-pane fade in active">
                                <form action="" method="POST">
                                    @method('post')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="row" style="margin-top:5px;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>POSISI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="posisi_agunan" id="posisi_agunan" placeholder="ENTRI"
                                                        value="{{ old('posisi_agunan') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>KONDISI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="kondisi_agunan" id="kondisi_agunan" placeholder="ENTRI"
                                                        value="{{ old('kondisi_agunan') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NILAI TAKSASI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="nilai_agunan" id="nilai_agunan" placeholder="ENTRI"
                                                        value="{{ old('nilai_agunan') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                </form>
                            </div>

                            <div id="data_lanjutan" class="tab-pane fade">
                                <form action="" method="POST">
                                    @method('post')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="row" style="margin-top:5px;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>POSISI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="posisi_agunan" id="posisi_agunan" placeholder="ENTRI"
                                                        value="{{ old('posisi_agunan') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>KONDISI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="kondisi_agunan" id="kondisi_agunan" placeholder="ENTRI"
                                                        value="{{ old('kondisi_agunan') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NILAI TAKSASI AGUNAN</label>
                                                    <input type="text" class="form-control text-uppercase"
                                                        name="nilai_agunan" id="nilai_agunan" placeholder="ENTRI"
                                                        value="{{ old('nilai_agunan') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
