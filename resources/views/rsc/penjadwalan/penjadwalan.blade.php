@extends('theme.app')
@section('title', 'Penjadwalan RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('rsc.penjadwalan.menu', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active text-bold">
                                <a href="#informasi" data-toggle="tab">PENJADWALAN</a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <div id="informasi" class="tab-pane fade in active">

                                @if (!is_null($data_survei))
                                    <form action="{{ route('rsc.penjadwalan.update', ['rsc' => $data->rsc]) }}"
                                        method="post">
                                        @method('post')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px; font-size:12px;">

                                            <div class="div-left">

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>NAMA SURVEYOR</label>
                                                    <input type="hidden" name="kantor_kode"
                                                        value="{{ $data->kantor_kode }}">
                                                    <select class="form-control petugas" style="width: 100%;"
                                                        name="kode_petugas" id="kode_petugas">
                                                        <option value="">--PILIH--</option>
                                                        @forelse ($surveyor as $item)
                                                            @if ($item->code_user == $data->code_user)
                                                                <option value="{{ $item->code_user }}"
                                                                    style="font-size:100px;" selected>
                                                                    {{ $item->kantor_kode }} -
                                                                    {{ $item->nama_user }}
                                                                </option>
                                                            @endif
                                                            <option value="{{ $item->code_user }}" style="font-size:100px;">
                                                                {{ $item->kantor_kode }} -
                                                                {{ $item->nama_user }}
                                                            </option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>TGL. SURVEY</label>
                                                    <input type="date" class="form-control" name="tgl_survei"
                                                        id="datepicker-tanggal-survei"
                                                        value="{{ $data_survei->tgl_survei }}"
                                                        {{ $data_survei->tgl_survei ? 'readonly' : '' }} required>
                                                </div>

                                            </div>

                                            <div class="div-right">

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>TGL. JADUL 1</label>
                                                    <input type="date" class="form-control" name="tgl_jadul_1"
                                                        id="datepicker-tanggal-survei1"
                                                        value="{{ $data_survei->tgl_jadul_1 }}"
                                                        {{ $data_survei->tgl_jadul_1 ? 'readonly' : '' }}>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>TGL. JADUL 2</label>
                                                    <input type="date" class="form-control" name="tgl_jadul_2"
                                                        id="datepicker-tanggal-survei2"
                                                        value="{{ $data_survei->tgl_jadul_2 }}"
                                                        {{ $data_survei->tgl_jadul_2 ? 'readonly' : '' }}>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="box-body" style="margin-top:-5px; font-size:12px;">
                                            <label>CATATAN</label>
                                            <textarea name="" id="" cols="" rows=""
                                                style="width: 100%; font-size: 14px; border: 2px solid #ccc; resize: none; height: 120px; padding: 15px 0px 0px 0px;"
                                                readonly>
    {{ 'Survei 1 :  ' . $data_survei->catatan_survei }}
    {{ 'Survei 2 :  ' . $data_survei->catatan_jadul_1 }}
    {{ 'Survei 3 :  ' . $data_survei->catatan_jadul_2 }}
                                            </textarea>
                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>

                                    </form>
                                @else
                                    <form action="{{ route('rsc.penjadwalan.simpan', ['rsc' => $data->rsc]) }}"
                                        method="post">
                                        @method('post')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px; font-size:12px;">

                                            <div class="div-left">

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>NAMA SURVEYOR</label>
                                                    <input type="hidden" name="kantor_kode"
                                                        value="{{ $data->kantor_kode }}">
                                                    <select class="form-control petugas" style="width: 100%;"
                                                        name="kode_petugas" id="kode_petugas">
                                                        <option value="">--PILIH--</option>
                                                        @forelse ($surveyor as $item)
                                                            @if ($item->code_user == $data->code_user)
                                                                <option value="{{ $item->code_user }}"
                                                                    style="font-size:100px;" selected>
                                                                    {{ $item->kantor_kode }} -
                                                                    {{ $item->nama_user }}
                                                                </option>
                                                            @endif
                                                            <option value="{{ $item->code_user }}"
                                                                style="font-size:100px;">
                                                                {{ $item->kantor_kode }} -
                                                                {{ $item->nama_user }}
                                                            </option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>TGL. SURVEY</label>
                                                    <input type="date" class="form-control" name="tgl_survei"
                                                        id="datepicker-tanggal-survei" value="" required>
                                                </div>

                                            </div>

                                            <div class="div-right">

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>TGL. JADUL 1</label>
                                                    <input type="date" class="form-control" name="tgl_jadul_1"
                                                        id="datepicker-tanggal-survei1" disabled>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>TGL. JADUL 2</label>
                                                    <input type="date" class="form-control" name="tgl_jadul_2"
                                                        id="datepicker-tanggal-survei2" disabled>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="box-body" style="margin-top:-5px; font-size:12px;">
                                            <label>CATATAN</label>
                                            <textarea name="" id="" cols="30" rows="5" style="width: 100%;" readonly></textarea>
                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>

                                    </form>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        $('.petugas').select2()
    </script>
@endpush
