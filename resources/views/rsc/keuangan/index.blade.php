@extends('theme.app')
@section('title', 'Kemampuan Keuangan RSC')

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
                            <li class="{{ request()->is('themes/rsc/keuangan') ? 'active' : '' }}">
                                <a href="{{ route('keuangan.index') }}"
                                    class="{{ request()->is('themes/rsc/keuangan') ? 'text-bold' : '' }}">
                                    KEMAMPUAN KEUANGAN
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form action="{{ route('rsc.keuangan.simpan', ['rsc' => $data->rsc]) }}" method="post">
                                    @method('post')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">KONSUMSI POKOK</span>
                                                <input value="KONSUMSI POKOK" name="nama1" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya1" id="konsumsi"
                                                    value="{{ old('biaya1', 'Rp. ' . number_format($keuangan->konsumsi, '0', ',', '.')) }}"
                                                    required>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">KESEHATAN</span>
                                                <input value="KESEHATAN" name="nama2" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya2"
                                                    value="{{ old('biaya2', 'Rp. ' . number_format($keuangan->kesehatan, '0', ',', '.')) }}"
                                                    id="kesehatan">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PENDIDIKAN</span>
                                                <input value="PENDIDIKAN" name="nama3" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya3" id="pendidikan"
                                                    value="{{ old('biaya3', 'Rp. ' . number_format($keuangan->pendidikan, '0', ',', '.')) }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">GATEL</span>
                                                <input value="GAS TELEPON LISTRIK" name="nama4" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya4" id="gatel"
                                                    value="{{ old('biaya4', 'Rp. ' . number_format($keuangan->gatel, '0', ',', '.')) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JAJAN ANAK</span>
                                                <input value="JAJAN ANAK" name="nama5" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya5" id="jajan"
                                                    value="{{ old('biaya5', 'Rp. ' . number_format($keuangan->jajan_anak, '0', ',', '.')) }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">SUMBANGAN SOSIAL</span>
                                                <input value="SUMBANGAN SOSIAL" name="nama6" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya6" id="sumbangan"
                                                    value="{{ old('biaya6', 'Rp. ' . number_format($keuangan->sumbangan, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">ROKOK</span>
                                                <input value="ROKOK" name="nama7" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya7" id="roko"
                                                    value="{{ old('biaya7', 'Rp. ' . number_format($keuangan->roko, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN LAINNYA</span>
                                                <input class="form-control input-sm form-border text-uppercase"
                                                    type="text" placeholder="ENTRI" name="data1"
                                                    value="{{ $keuangan->kewajiban1 }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban1" id="kewajiban1"
                                                    value="{{ old('kewajiban1', 'Rp. ' . number_format($keuangan->nominal_kewajiban1, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN LAINNYA</span>
                                                <input class="form-control input-sm form-border text-uppercase"
                                                    type="text" placeholder="ENTRI" name="data2"
                                                    value="{{ $keuangan->kewajiban2 }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban2" id="kewajiban2"
                                                    value="{{ old('kewajiban2', 'Rp. ' . number_format($keuangan->nominal_kewajiban2, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN LAINNYA</span>
                                                <input class="form-control input-sm form-border text-uppercase"
                                                    type="text" placeholder="ENTRI" name="data3"
                                                    value="{{ $keuangan->kewajiban3 }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban3" id="kewajiban3"
                                                    value="{{ old('kewajiban3', 'Rp. ' . number_format($keuangan->nominal_kewajiban3, '0', ',', '.')) }}">
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">USAHA PERDAGANGAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ number_format($kemampuan['perdagangan'], '0', ',', '.') }}"
                                                    readonly>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">USAHA PERTANIAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ number_format($kemampuan['pertanian'], '0', ',', '.') }}"
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">USAHA JASA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ number_format($kemampuan['jasa'], '0', ',', '.') }}"
                                                    readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">USAHA LAINNYA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ number_format($kemampuan['lain'], '0', ',', '.') }}"
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">PENDAPATAN USAHA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="p_usaha"
                                                    value="{{ number_format($kemampuan['total'], '0', ',', '.') }}"
                                                    id="pendapatan" readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">BIAYA RUMAH TANGGA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . number_format($keuangan->b_rumah_tangga, '0', ',', '.') }}"
                                                    name="b_rumah_tangga" id="biaya" readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KEWAJIBAN LAINNYA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . number_format($keuangan->b_kewajiban_lainnya, '0', ',', '.') }}"
                                                    name="b_kewajiban_lainya" id="kewajiban_lain" readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">KEUANGAN PERBULAN</span>
                                                <input class="form-control input-sm form-border bg-blue" type="text"
                                                    value="{{ 'Rp. ' . number_format($keuangan->keuangan_perbulan, '0', ',', '.') }}"
                                                    name="keuangan_perbulan" id="hasilbersih" readonly>
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
    <script src="{{ asset('assets/js/myscript/keuangan.js') }}"></script>
@endpush
