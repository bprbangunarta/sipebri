@extends('theme.app')
@section('title', 'Edit Jaminan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                            <h3 class="box-title">DATA JAMINAN</h3>
                        </div>

                        <form action="{{ route('admin.jaminan.update', ['data' => $data->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE PENGAJUAN</label>
                                            <input type="text" class="form-control" name="pengajuan_kode" id=""
                                                value="{{ $data->pengajuan_kode }}" readonly>
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>LOKASI</label>
                                            <input type="text" class="form-control" name="lokasi" id=""
                                                value="{{ $data->lokasi }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>STATUS BALIK NAMA</label>
                                            <select type="text" class="form-control" style="width: 100%;"
                                                name="status_bl_nama" required>
                                                <option value="" selected>--PILIH--</option>
                                                <option value="YES"
                                                    {{ old('status_bl_nama') == 'YES' || $data->status_bl_nama == 'YES' ? 'selected' : '' }}>
                                                    YES
                                                </option>
                                                <option value="NO"
                                                    {{ old('status_bl_nama') == 'NO' || $data->status_bl_nama == 'NO' ? 'selected' : '' }}>
                                                    NO
                                                </option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ATAS NAMA</label>
                                            <input type="text" class="form-control" name="atas_nama" id=""
                                                value="{{ $data->atas_nama }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>CATATAN</label>
                                            <input type="text" class="form-control" name="catatan" id=""
                                                value="{{ $data->catatan }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>STATUS BALIK NAMA</label>
                                            <select type="text" class="form-control" style="width: 100%;"
                                                name="jenis_bl_nama">
                                                <option value="" selected>--PILIH--</option>
                                                <option value="Balik Nama"
                                                    {{ old('jenis_bl_nama') == 'Balik Nama' || $data->jenis_bl_nama == 'Balik Nama' ? 'selected' : '' }}>
                                                    Balik Nama
                                                </option>
                                                <option value="Pensertipikatan"
                                                    {{ old('jenis_bl_nama') == 'Pensertipikatan' || $data->jenis_bl_nama == 'Pensertipikatan' ? 'selected' : '' }}>
                                                    Pensertipikatan
                                                </option>
                                                <option value="Permohonan Peningkatan Sertipikat Hak Milik"
                                                    {{ old('jenis_bl_nama') == 'Permohonan Peningkatan Sertipikat Hak Milik' || $data->jenis_bl_nama == 'Permohonan Peningkatan Sertipikat Hak Milik' ? 'selected' : '' }}>
                                                    Permohonan Peningkatan Sertipikat Hak Milik
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO DOKUMEN</label>
                                            <input type="text" class="form-control" name="no_dokumen" id=""
                                                value="{{ $data->no_dokumen }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ON CURRENT</label>
                                            <input type="text" class="form-control" name="on_current" id=""
                                                value="{{ $data->on_current }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ATAS NAMA BALIK NAMA</label>
                                            <input type="text" class="form-control" name="an_bl_nama" id=""
                                                value="{{ $data->an_bl_nama }}">
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="box-footer" style="margin-top: -10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>

                                <a href="{{ route('admin.jaminan.index') }}" class="btn btn-default pull-right">
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
