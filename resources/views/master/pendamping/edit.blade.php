@extends('theme.app')
@section('title', 'Edit Nasabah')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA NASABAH</h3>
                        </div>

                        <form action="{{ route('admin.pendamping.update', ['id' => $data->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. IDENTITAS</label>
                                            <input type="text" class="form-control" name="no_identitas" id="no_identitas"
                                                value="{{ $data->no_identitas }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TEMPAT LAHIR</label>
                                            <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir"
                                                value="{{ $data->tempat_lahir }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NAMA PENDAMPING</label>
                                            <input type="text" class="form-control" name="nama_pendamping"
                                                id="nama_pendamping" value="{{ $data->nama_pendamping }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>STATUS</label>
                                            <select type="text" class="form-control" name="status" required>
                                                <option value="ISTRI"
                                                    {{ $data->status == 'ISTRI' || old('status') == 'ISTRI' ? 'selected' : '' }}>
                                                    ISTRI</option>
                                                <option value="SUAMI"
                                                    {{ $data->status == 'SUAMI' || old('status') == 'SUAMI' ? 'selected' : '' }}>
                                                    SUAMI</option>
                                                <option value="ORANG TUA"
                                                    {{ $data->status == 'ORANG TUA' || old('status') == 'ORANG TUA' ? 'selected' : '' }}>
                                                    ORANG TUA</option>
                                                <option value="SAUDARA"
                                                    {{ $data->status == 'SAUDARA' || old('status') == 'SAUDARA' ? 'selected' : '' }}>
                                                    SAUDARA</option>
                                                <option value="LAINNYA"
                                                    {{ $data->status == 'LAINNYA' || old('status') == 'LAINNYA' ? 'selected' : '' }}>
                                                    LAINNYA</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TANGGAL LAHIR</label>
                                            <input type="text" class="form-control" name="tanggal_lahir"
                                                id="tanggal_lahir" value="{{ $data->tanggal_lahir }}">
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="box-footer" style="margin-top: -10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>

                                <a href="{{ route('admin.pendamping.index') }}" class="btn btn-default pull-right">
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
