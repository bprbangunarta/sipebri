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
                                            <select type="text" class="form-control kode_kantor" name="kantor_kode"
                                                required>
                                                <option value="PMK" {{ $data->kantor_kode == 'PMK' ? 'selected' : '' }}>
                                                    KANTOR PUSAT PAMANUKAN</option>
                                                <option value="PSK" {{ $data->kantor_kode == 'PSK' ? 'selected' : '' }}>
                                                    KANTOR KAS PUSAKAJAYA</option>
                                                <option value="SKM" {{ $data->kantor_kode == 'SKM' ? 'selected' : '' }}>
                                                    KANTOR KAS SUKAMANDI</option>
                                                <option value="KJT" {{ $data->kantor_kode == 'KJT' ? 'selected' : '' }}>
                                                    KANTOR KAS KALIJATI</option>
                                                <option value="SBG" {{ $data->kantor_kode == 'SBG' ? 'selected' : '' }}>
                                                    KANTOR KAS SUBANG</option>
                                                <option value="CGK" {{ $data->kantor_kode == 'CGK' ? 'selected' : '' }}>
                                                    KANTOR KAS JALANCAGAK</option>
                                                <option value="PGD" {{ $data->kantor_kode == 'PGD' ? 'selected' : '' }}>
                                                    KANTOR KAS PAGADEN</option>
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
                                            <select type="text" class="form-control surveyor_kode" name="surveyor_kode"
                                                required>
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

                                <a href="{{ route('admin.survei.index') }}" class="btn btn-default pull-right">
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

@push('myscript')
    <script>
        $('.surveyor_kode').select2()
        $('.kode_kantor').select2()
    </script>
@endpush
