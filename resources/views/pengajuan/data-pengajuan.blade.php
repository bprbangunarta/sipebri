@extends('theme.app')
@section('title', 'Data Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan,])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#" class="text-bold">
                                    DATA PENGAJUAN
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form action="{{ route('keuangan.simpan') }}"
                                    method="post">
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KONSUMSI POKOK" name="nama1" hidden>
                                                <input class="form-control input-sm form-border" type="text" name="" id="">
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KESEHATAN" name="nama2" hidden>
                                                <input class="form-control input-sm form-border" type="text" name="" id="">
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KONSUMSI POKOK" name="nama1" hidden>
                                                <input class="form-control input-sm form-border" type="text" name="" id="">
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KESEHATAN" name="nama2" hidden>
                                                <input class="form-control input-sm form-border" type="text" name="" id="">
                                            </div>
                                        </div>

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

@push('myscript')
@endpush
