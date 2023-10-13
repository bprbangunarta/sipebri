@extends('theme.app')
@section('title', 'Harta Kepemilikan')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                @include('theme.menu-analisa')
            </div>

            <div class="col-xs-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="{{ request()->is('theme/analisa/kepemilikan') ? 'active' : '' }}">
                            <a href="/theme/analisa/usaha/kepemilikan" class="{{ request()->is('theme/analisa/kepemilikan') ? 'text-bold' : '' }}">
                                HARTA KEPEMILIKAN
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active">
                    
                            <form action="">
                                @csrf
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                    
                                    {{-- <input name="kode_pengajuan" value="{{ $data->kd_pengajuan }}" hidden> --}}

                                    <div class="div-left">
                                        <div style="width: 49.5%;float:left;">
                                            <span class="fw-bold">RUMAH</span>
                                            <select class="form-control input-sm form-border" name="rumah" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="PERMANEN">PERMANEN</option>
                                                <option value="SEDERHANA">SEDERHANA</option>
                                                <option value="SEMI PERMANEN">SEMI PERMANEN</option>
                                            </select>
                                        </div>
                                        <div style="width: 49.5%;float:right;">
                                            <span class="fw-bold">MOBIL</span>
                                            <select class="form-control input-sm form-border" name="mobil" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="1 UNIT">1 UNIT</option>
                                                <option value="2 UNIT">2 UNIT</option>
                                                <option value="3 UNIT">3 UNIT</option>
                                                <option value="4 UNIT">4 UNIT</option>
                                                <option value="5 UNIT">5 UNIT</option>
                                            </select>
                                        </div>
                                        
                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <span class="fw-bold">MOTOR</span>
                                            <select class="form-control input-sm form-border" name="motor" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="1 UNIT">1 UNIT</option>
                                                <option value="2 UNIT">2 UNIT</option>
                                                <option value="3 UNIT">3 UNIT</option>
                                                <option value="4 UNIT">4 UNIT</option>
                                                <option value="5 UNIT">5 UNIT</option>
                                            </select>
                                        </div>
                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <span class="fw-bold">TELEVISI</span>
                                            <select class="form-control input-sm form-border" name="tv" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="LCD">LCD</option>
                                                <option value="LED">LED</option>
                                                <option value="CRT FLAT">CRT FLAT</option>
                                                <option value="CRT CEMBUNG">CRT CEMBUNG</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <span class="fw-bold">KOMPUTER</span>
                                            <select class="form-control input-sm form-border" name="komputer" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>
                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <span class="fw-bold">MESIN CUCI</span>
                                            <select class="form-control input-sm form-border" name="mesin_cuci" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>
                                    </div>
                    

                                    <div class="div-right">
                                        <div style="width: 49.5%;float:left;">
                                            <span class="fw-bold">KURSI TAMU</span>
                                            <select class="form-control input-sm form-border" name="kursi" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>
                                        <div style="width: 49.5%;float:right;">
                                            <span class="fw-bold">LEMARI PANJANG</span>
                                            <select class="form-control input-sm form-border" name="lemari" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>
                                        
                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <span class="fw-bold">HARTA LAIN</span>
                                            <input class="form-control input-sm form-border" type="text" name="nama_lain1" placeholder="ENTRI">
                                        </div>
                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <span class="fw-bold">KEPEMILIKAN</span>
                                            <select class="form-control input-sm form-border" name="lainnya1" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>

                                        <div style="margin-top:5px;width: 49.5%;float:left;">
                                            <span class="fw-bold">HARTA LAIN</span>
                                            <input class="form-control input-sm form-border" type="text" name="nama_lain2" placeholder="ENTRI">
                                        </div>
                                        <div style="margin-top:5px;width: 49.5%;float:right;">
                                            <span class="fw-bold">KEPEMILIKAN</span>
                                            <select class="form-control input-sm form-border" name="lainnya2" id="">
                                                <option value="">--PILIH--</option>
                                                <option value="ADA">ADA</option>
                                                <option value="TIDAK ADA">TIDAK ADA</option>
                                            </select>
                                        </div>
                                    </div>
                    
                                    <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
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