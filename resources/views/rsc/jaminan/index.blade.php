@extends('theme.app')
@section('title', 'Data Jaminan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-rsc', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#data_jaminan" data-toggle="tab">DATA JAMINAN</a>
                            </li>

                            <li>
                                <a href="#cari_jaminan" data-toggle="tab">CARI JAMINAN</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div id="data_jaminan" class="tab-pane fade in active">

                            </div>

                            <div id="cari_jaminan" class="tab-pane fade">

                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
@endpush
