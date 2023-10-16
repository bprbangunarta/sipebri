@extends('theme.app')
@section('title', 'Kemampuan Keuangan')

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
                            <li class="{{ request()->is('themes/analisa/keuangan') ? 'active' : '' }}">
                                <a href="{{ route('keuangan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
                                    class="{{ request()->is('themes/analisa/keuangan') ? 'text-bold' : '' }}">
                                    KEMAMPUAN KEUANGAN
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form
                                    action="{{ route('keuangan.update', ['pengajuan' => $data->kd_pengajuan, 'keuangan' => $biaya[0]->keuangan_kode]) }}"
                                    method="post">
                                    @method('put')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">KONSUMSI POKOK</span>
                                                <input value="{{ $biaya[0]->keuangan_kode }}" name="keuangan_kode" hidden>
                                                <input value="{{ $biaya[0]->kode_biaya }}" name="kode1" hidden>
                                                <input value="{{ $biaya[0]->pengeluaran }}" name="nama1" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya1" id="konsumsi"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[0]->nominal, 0, ',', '.') }}"
                                                    required>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">KESEHATAN</span>
                                                <input value="{{ $biaya[1]->kode_biaya }}" name="kode2" hidden>
                                                <input value="{{ $biaya[1]->pengeluaran }}" name="nama2" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya2"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[1]->nominal, 0, ',', '.') }}"
                                                    id="kesehatan">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PENDIDIKAN</span>
                                                <input value="{{ $biaya[2]->kode_biaya }}" name="kode3" hidden>
                                                <input value="{{ $biaya[2]->pengeluaran }}" name="nama3" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya3" id="pendidikan"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[2]->nominal, 0, ',', '.') }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">GATEL</span>
                                                <input value="{{ $biaya[3]->kode_biaya }}" name="kode4" hidden>
                                                <input value="{{ $biaya[3]->pengeluaran }}" name="nama4" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya4" id="gatel"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[3]->nominal, 0, ',', '.') }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JAJAN ANAK</span>
                                                <input value="{{ $biaya[4]->kode_biaya }}" name="kode5" hidden>
                                                <input value="{{ $biaya[4]->pengeluaran }}" name="nama5" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya5" id="jajan"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[4]->nominal, 0, ',', '.') }}">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">SUMBANGAN SOSIAL</span>
                                                <input value="{{ $biaya[5]->kode_biaya }}" name="kode6" hidden>
                                                <input value="{{ $biaya[5]->pengeluaran }}" name="nama6" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya6" id="sumbangan"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[5]->nominal, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">ROKOK</span>
                                                <input value="{{ $biaya[6]->kode_biaya }}" name="kode7" hidden>
                                                <input value="{{ $biaya[6]->pengeluaran }}" name="nama7" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="biaya7" id="roko"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[6]->nominal, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN UNTUK</span>
                                                <input value="{{ $biaya[7]->kode_biaya }}" name="kd1" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="ENTRI" value="{{ $biaya[7]->pengeluaran }}"
                                                    name="data1">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban1" id="kewajiban1"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[7]->nominal, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN UNTUK</span>
                                                <input value="{{ $biaya[8]->kode_biaya }}" name="kd2" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="ENTRI" value="{{ $biaya[8]->pengeluaran }}"
                                                    name="data2">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban2" id="kewajiban2"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[8]->nominal, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWAJIBAN UNTUK</span>
                                                <input value="{{ $biaya[9]->kode_biaya }}" name="kd3" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="ENTRI" value="{{ $biaya[9]->pengeluaran }}"
                                                    name="data3">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NOMINAL PENGELUARAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    placeholder="Rp." name="kewajiban3" id="kewajiban3"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[9]->nominal, 0, ',', '.') }}">
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">USAHA PERDAGANGAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($kemampuan['perdagangan'], 0, ',', '.') }}"
                                                    readonly>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">USAHA PERTANIAN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($kemampuan['pertanian'], 0, ',', '.') }}"
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">USAHA JASA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($kemampuan['jasa'], 0, ',', '.') }}"
                                                    readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">USAHA LAINNYA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($kemampuan['lain'], 0, ',', '.') }}"
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">PENDAPATAN USAHA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="p_usaha"
                                                    value="{{ 'Rp. ' . ' ' . number_format($kemampuan['total'], 0, ',', '.') }}"
                                                    id="pendapatan" readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">BIAYA RUMAH TANGGA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[0]->b_rumah_tangga, 0, ',', '.') }}"
                                                    name="b_rumah_tangga" id="biaya" readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KEWAJIBAN LAINNYA</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[0]->b_kewajiban_lainya, 0, ',', '.') }}"
                                                    name="b_kewajiban_lainya" id="kewajiban_lain" readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">KEUANGAN PERBULAN</span>
                                                <input class="form-control input-sm form-border bg-blue" type="text"
                                                    value="{{ 'Rp. ' . ' ' . number_format($biaya[0]->keuangan_perbulan, 0, ',', '.') }}"
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
