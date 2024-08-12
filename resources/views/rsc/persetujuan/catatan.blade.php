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
                                <a href="#staff_analisa" data-toggle="tab">CATATAN</a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <div id="staff_analisa" class="tab-pane fade in active" style="display:flexbox;">

                                <label>CATATAN STAFF ANALIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['Staff Analis'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN KASI ANALIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                {{ $catatan['Kasi Analis'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN KABAG ANALIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['Kabag Analis'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN DIREKTUR BISNIS</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['Direktur Bisnis'] }}
                                </textarea>

                                &nbsp;

                                <label>CATATAN DIREKTUR UTAMA</label>
                                <textarea class="form-control text-uppercase" style="padding-left:10px; background: white; resize:none;" rows="4"
                                    name="catatan" id="catatan" required="" readonly>
                                    {{ $catatan['Direksi'] }}
                                </textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
