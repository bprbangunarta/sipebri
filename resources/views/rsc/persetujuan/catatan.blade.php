@extends('theme.app')
@section('title', 'Catatan RSC')

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
                                <a href="#staff_analisa" data-toggle="tab">STAFF ANALISA</a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <div id="staff_analisa" class="tab-pane fade in active" style="display:flexbox;">
                                <label>CATATAN STAFF ANALIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['catatan1']['catatan_staff_analisa'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN KASI ANALIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                {{ $catatan['catatan2']['catatan_kasi_analisa'] }}
                            </textarea>

                                &nbsp;

                                <label>CATATAN KOMITE I</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['catatan3']['catatan_kabag_analisa'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN KOMITE II</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['catatan4']['catatan_direksi'] }}
                                </textarea>
                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
