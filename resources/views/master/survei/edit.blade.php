@extends('theme.app')
@section('title', 'Edit Survei')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA SURVEI</h3>
                        </div>

                        <form action="{{ route('admin.survei.update', ['survei' => $data->pengajuan_kode]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE PENGAJUAN</label>
                                            <input type="text" class="form-control" name="" id=""
                                                value="{{ $data->pengajuan_kode }}" readonly>
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE KANTOR</label>
                                            <select type="text" class="form-control" name="kantor_kode" required>
                                                @foreach ($kantor as $item)
                                                    @if ($item->kode_kantor == $data->kantor_kode)
                                                        <option value="{{ $item->kode_kantor }}">
                                                            {{ $item->nama_kantor }}</option>
                                                    @endif
                                                    <option value="{{ $item->kode_kantor }}">
                                                        {{ $item->nama_kantor }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TANGGAL SURVEI 3 <i style="color: rgb(110, 110, 110)">( YYYY-MM-DD
                                                    )</i></label>
                                            <input type="text" class="form-control" name="tgl_jadul_2" id=""
                                                value="{{ $data->tgl_jadul_2 }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KASI ANALIS</label>
                                            <select type="text" class="form-control" name="kasi_kode" required>
                                                @foreach ($kasi as $item)
                                                    @if ($item->code_user == $data->kasi_kode)
                                                        <option value="{{ $item->code_user }}" selected>
                                                            {{ $item->nama_user }}</option>
                                                    @else
                                                        <option value="{{ $item->code_user }}">
                                                            {{ $item->nama_user }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TANGGAL SURVEI 1 <i style="color: rgb(110, 110, 110)">( YYYY-MM-DD
                                                    )</i></label>
                                            <input type="text" class="form-control" name="tgl_survei" id=""
                                                value="{{ $data->tgl_survei }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>STAFF ANALIS</label>
                                            <select type="text" class="form-control" name="surveyor_kode" required>
                                                @foreach ($analis as $item)
                                                    @if ($item->code_user == $data->surveyor_kode)
                                                        <option value="{{ $item->code_user }}" selected>
                                                            {{ $item->nama_user }}</option>
                                                    @else
                                                        <option value="{{ $item->code_user }}">
                                                            {{ $item->nama_user }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TANGGAL SURVEI 2 <i style="color: rgb(110, 110, 110)">( YYYY-MM-DD
                                                    )</i></label>
                                            <input type="text" class="form-control" name="tgl_jadul_1" id=""
                                                value="{{ $data->tgl_jadul_1 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="box-footer" style="margin-top: -10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>

                                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-default pull-right">
                                    <i class="fa fa-arrow-left"></i> KEMBALI
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
