@extends('theme.app')
@section('title', 'Biaya Administrasi')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-analisa', [$data, 'pengajuan' => $data->kd_pengajuan])
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

                                <form action="{{ route('administrasi.simpan', ['pengajuan' => $data->kd_pengajuan]) }}"
                                    method="post">
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">

                                                <span class="fw-bold">ADMINISTRASI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="administrasi" placeholder="Rp." id="administrasi"
                                                    value="{{ 'Rp. ' . ' ' . number_format($data->administrasi, 0, ',', '.') }}"
                                                    readonly>
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">PROVISI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase" name="provisi"
                                                    placeholder="Rp." id="provisi"
                                                    value="{{ 'Rp. ' . ' ' . number_format($data->provisi, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">MATERAI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase" name="materai"
                                                    placeholder="Rp." id="materai">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 1</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa_menurun1" placeholder="Rp."
                                                    id="asuransi_jiwa_menurun1">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 2</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa_menurun2" placeholder="Rp."
                                                    id="asuransi_jiwa_menurun2">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA MENURUN 3</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa_menurun3" placeholder="Rp."
                                                    id="asuransi_jiwa_menurun3">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">ASR. JIWA TETAP 1</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa_tetap1" placeholder="Rp." id="asuransi_jiwa_tetap1">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. JIWA TETAP 2</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa_tetap2" placeholder="Rp." id="asuransi_jiwa_tetap2">
                                            </div>
                                        </div>

                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">PERTANGGUNGAN ASR. JIWA</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_jiwa" placeholder="Rp." id="asuransi_jiwa">
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">ASR. KENDARAAN BERMOTOR</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="asuransi_kendaraan_motor" placeholder="Rp."
                                                    id="asuransi_kendaraan_motor">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TRANSAKSI KREDIT</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="transaksi_kredit" placeholder="Rp." id="transaksi_kredit">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PROSES SHM</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="proses_shm" placeholder="Rp." id="proses_shm">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">POLIS DAN MATERAI</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="polis_materai" placeholder="Rp." id="polis_materai">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PAJAK STNK</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="pajak_stnk" placeholder="Rp." id="pajak_stnk">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PROSES APHT</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="proses_apht" placeholder="Rp." id="proses_apht"
                                                    value="{{ 'Rp. ' . ' ' . number_format($data->apht, 0, ',', '.') }}"
                                                    @if ($data->apht == 0) @readonly(true) @endif>
                                            </div>

                                            <input type="hidden" class="form-control input-sm form-border text-uppercase"
                                                name="lainnya" placeholder="Rp." id="lainnya">

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">BIAYA FIDUCIA</span>
                                                <input type="text"
                                                    class="form-control input-sm form-border text-uppercase"
                                                    name="by_fiducia" placeholder="Rp." id="by_fiducia"
                                                    value="{{ 'Rp. ' . ' ' . number_format($data->fiducia, 0, ',', '.') }}"
                                                    @if ($data->fiducia == 0) @readonly(true) @endif>
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
    <script src="{{ asset('assets/js/myscript/biaya_administrasi.js') }}"></script>
@endpush
