@extends('theme.app')
@section('title', 'Harta Kepemilikan')

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
                            <li class="{{ request()->is('themes/analisa/kepemilikan') ? 'active' : '' }}">
                                <a href="{{ route('kepemilikan.index', ['pengajuan' => $data->kd_pengajuan, 'kode_kepemilikan' => $milik->kode_kepemilikan]) }}"
                                    class="{{ request()->is('theme/analisa/kepemilikan') ? 'text-bold' : '' }}">
                                    HARTA KEPEMILIKAN
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form action="{{ route('kepemilikan.update', ['pengajuan' => $data->kd_pengajuan]) }}"
                                    method="post">
                                    @method('put')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        {{-- <input name="kode_pengajuan" value="{{ $data->kd_pengajuan }}" hidden> --}}

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">RUMAH</span>
                                                <input type="text" value="{{ $milik->kode_kepemilikan }}"
                                                    name="kode_kepemilikan" hidden>
                                                <select class="form-control input-sm form-border" name="rumah"
                                                    id="">
                                                    <option value="">--PILIH--</option>
                                                    <option value="PERMANEN"
                                                        {{ old('rumah') == 'PERMANEN' || $milik->rumah == 'PERMANEN' ? 'selected' : '' }}>
                                                        PERMANEN
                                                    </option>
                                                    <option value="SEDERHANA"
                                                        {{ old('rumah') == 'SEDERHANA' || $milik->rumah == 'SEDERHANA' ? 'selected' : '' }}>
                                                        SEDERHANA
                                                    </option>
                                                    <option value="SEMI PERMANEN"
                                                        {{ old('rumah') == 'SEMI PERMANEN' || $milik->rumah == 'SEMI PERMANEN' ? 'selected' : '' }}>
                                                        SEMI PERMANEN
                                                    </option>
                                                </select>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">MOBIL</span>
                                                <select class="form-control input-sm form-border" name="mobil"
                                                    id="">
                                                    <option value="1 UNIT"
                                                        {{ old('mobil') == '1 UNIT' || $milik->mobil == '1 UNIT' ? 'selected' : '' }}>
                                                        1 UNIT</option>
                                                    <option value="2 UNIT"
                                                        {{ old('mobil') == '2 UNIT' || $milik->mobil == '2 UNIT' ? 'selected' : '' }}>
                                                        2 UNIT</option>
                                                    <option value="3 UNIT"
                                                        {{ old('mobil') == '3 UNIT' || $milik->mobil == '3 UNIT' ? 'selected' : '' }}>
                                                        3 UNIT</option>
                                                    <option value="4 UNIT"
                                                        {{ old('mobil') == '4 UNIT' || $milik->mobil == '4 UNIT' ? 'selected' : '' }}>
                                                        4 UNIT</option>
                                                    <option value="5 UNIT"
                                                        {{ old('mobil') == '5 UNIT' || $milik->mobil == '5 UNIT' ? 'selected' : '' }}>
                                                        5 UNIT</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">MOTOR</span>
                                                <select class="form-control input-sm form-border" name="motor"
                                                    id="">
                                                    <option value="1 UNIT"
                                                        {{ old('motor') == '1 UNIT' || $milik->motor == '1 UNIT' ? 'selected' : '' }}>
                                                        1 UNIT</option>
                                                    <option value="2 UNIT"
                                                        {{ old('motor') == '2 UNIT' || $milik->motor == '2 UNIT' ? 'selected' : '' }}>
                                                        2 UNIT</option>
                                                    <option value="3 UNIT"
                                                        {{ old('motor') == '3 UNIT' || $milik->motor == '3 UNIT' ? 'selected' : '' }}>
                                                        3 UNIT</option>
                                                    <option value="4 UNIT"
                                                        {{ old('motor') == '4 UNIT' || $milik->motor == '4 UNIT' ? 'selected' : '' }}>
                                                        4 UNIT</option>
                                                    <option value="5 UNIT"
                                                        {{ old('motor') == '5 UNIT' || $milik->motor == '5 UNIT' ? 'selected' : '' }}>
                                                        5 UNIT</option>
                                                </select>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TELEVISI</span>
                                                <select class="form-control input-sm form-border" name="tv"
                                                    id="">
                                                    @if (!is_null($milik->televisi))
                                                        <option value="{{ $milik->televisi }}">{{ $milik->televisi }}
                                                        </option>
                                                    @else
                                                        <option value="">--PILIH--</option>
                                                    @endif
                                                    <option value="LCD"
                                                        {{ old('tv') == 'LCD' || $milik->televisi == 'LCD' ? 'selected' : '' }}>
                                                        LCD
                                                    </option>
                                                    <option value="LED"
                                                        {{ old('tv') == 'LED' || $milik->televisi == 'LED' ? 'selected' : '' }}>
                                                        LED
                                                    </option>
                                                    <option value="CRT FLAT"
                                                        {{ old('tv') == 'CRT FLAT' || $milik->televisi == 'CRT FLAT' ? 'selected' : '' }}>
                                                        CRT
                                                        FLAT</option>
                                                    <option value="CRT CEMBUNG"
                                                        {{ old('tv') == 'CRT CEMBUNG' || $milik->televisi == 'CRT CEMBUNG' ? 'selected' : '' }}>
                                                        CRT CEMBUNG</option>
                                                    <option value="TIDAK ADA"
                                                        {{ old('tv') == 'TIDAK ADA' || $milik->televisi == 'TIDAK ADA' ? 'selected' : '' }}>
                                                        TIDAK ADA</option>
                                                </select>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KOMPUTER</span>
                                                <select class="form-control input-sm form-border" name="komputer"
                                                    id="">
                                                    <option value="ADA"
                                                        {{ old('komputer') == 'ADA' || $milik->komputer == 'ADA' ? 'selected' : '' }}>
                                                        ADA</option>
                                                    <option value="TIDAK ADA"
                                                        {{ old('komputer') == 'TIDAK ADA' || $milik->komputer == 'TIDAK ADA' ? 'selected' : '' }}>
                                                        TIDAK ADA
                                                    </option>
                                                </select>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">MESIN CUCI</span>
                                                <select class="form-control input-sm form-border" name="mesin_cuci"
                                                    id="">
                                                    <option value="ADA"
                                                        {{ old('mesin_cuci') == 'ADA' || $milik->mesin_cuci == 'ADA' ? 'selected' : '' }}>
                                                        ADA</option>
                                                    <option value="TIDAK ADA"
                                                        {{ old('mesin_cuci') == 'TIDAK ADA' || $milik->mesin_cuci == 'TIDAK ADA' ? 'selected' : '' }}>
                                                        TIDAK ADA
                                                    </option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">KURSI TAMU</span>
                                                <select class="form-control input-sm form-border" name="kursi"
                                                    id="">
                                                    <option value="ADA"
                                                        {{ old('kursi') == 'ADA' || $milik->kursi_tamu == 'ADA' ? 'selected' : '' }}>
                                                        ADA</option>
                                                    <option value="TIDAK ADA"
                                                        {{ old('kursi') == 'TIDAK ADA' || $milik->kursi_tamu == 'TIDAK ADA' ? 'selected' : '' }}>
                                                        TIDAK ADA
                                                    </option>
                                                </select>
                                            </div>
                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">LEMARI PANJANG</span>
                                                <select class="form-control input-sm form-border" name="lemari"
                                                    id="">
                                                    <option value="ADA"
                                                        {{ old('lemari') == 'ADA' || $milik->lemari_panjang == 'ADA' ? 'selected' : '' }}>
                                                        ADA</option>
                                                    <option value="TIDAK ADA"
                                                        {{ old('lemari') == 'TIDAK ADA' || $milik->lemari_panjang == 'TIDAK ADA' ? 'selected' : '' }}>
                                                        TIDAK ADA
                                                    </option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">HARTA LAIN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="nama_lain1" placeholder="ENTRI">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">HARTA LAIN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="nama_lain2" placeholder="ENTRI">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">HARTA LAIN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="nama_lain3" placeholder="ENTRI">
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">HARTA LAIN</span>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="nama_lain4" placeholder="ENTRI">
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
