@extends('theme.app')
@section('title', 'Biaya Administrasi')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-analisa', [$data, 'pengajuan' => $data->kd_pengajuan])
                    {{-- @include('theme.menu-static') --}}
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="{{ request()->is('themes/analisa/administrasi') ? 'active' : '' }}">
                                <a href="{{ route('administrasi.index') }}"
                                    class="{{ request()->is('themes/analisa/administrasi') ? 'text-bold' : '' }}">
                                    BIAYA ADMINISTRASI
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form method="post">
                                    @method('put')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">PROVISI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"" name="privisi"
                                                    placeholder="Rp." readonly>
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">ADMINISTRASI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="administrasi" placeholder="Rp." readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">MATERAI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"" name="materai"
                                                    placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 1</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa_menurun1" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 2</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa_menurun2" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 3</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa_menurun3" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">ASR. JIWA TETAP 1</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa_tetap1" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA TETAP 2</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa_tetap2" placeholder="Rp.">
                                            </div>
                                        </div>

                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">PERTANGGUNGAN ASR. JIWA</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_jiwa" placeholder="Rp.">
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. KENDARAAN BERMOTOR</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="asuransi_kendaraan_motor" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TRANSAKSI KREDIT</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="transaksi_kredit" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PROSES SHM</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="proses_shm" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">POLIS DAN MATERAI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="polis_materai" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PAJAK STNK</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="pajak_stnk" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PROSES APHT</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="proses_apht" placeholder="Rp.">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">LAINNYA</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase""
                                                    name="lainnya" placeholder="Rp.">
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
    <script src="{{ asset('assets/js/myscript/administrasi.js') }}"></script>
@endpush
