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

                        <form action="{{ route('admin.nasabah.update', ['id' => $data->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>NO. CIF</label>
                                            <input type="text" class="form-control" name="no_cif" id="no_cif"
                                                value="{{ $data->no_cif }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. IDENTITAS</label>
                                            <input type="text" class="form-control" name="no_identitas" id="no_identitas"
                                                value="{{ $data->no_identitas }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>MASA IDENTITAS</label>
                                            <input type="text" class="form-control" name="masa_identitas"
                                                id="masa_identitas" value="{{ $data->masa_identitas }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NAMA NASABAH</label>
                                            <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah"
                                                value="{{ $data->nama_nasabah }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TEMPAT LAHIR</label>
                                            <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir"
                                                value="{{ $data->tempat_lahir }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TANGGAL LAHIR</label>
                                            <input type="text" class="form-control" name="tanggal_lahir"
                                                id="tanggal_lahir" value="{{ $data->tanggal_lahir }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE POS</label>
                                            <input type="text" class="form-control" name="kode_pos" id="kode_pos"
                                                value="{{ $data->kode_pos }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>KECAMATAN</label>
                                            <input type="text" class="form-control" name="kecamatan" id="kecamatan"
                                                value="{{ $data->kecamatan }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KELURAHAN</label>
                                            <input type="text" class="form-control" name="kelurahan" id="kelurahan"
                                                value="{{ $data->kelurahan }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KOTA</label>
                                            <input type="text" class="form-control" name="kota" id="kota"
                                                value="{{ $data->kota }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ALAMAT KTP</label>
                                            <input type="text" class="form-control" name="alamat_ktp" id="alamat_ktp"
                                                value="{{ $data->alamat_ktp }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ALAMAT SEKARANG</label>
                                            <input type="text" class="form-control" name="alamat_sekarang"
                                                id="alamat_sekarang" value="{{ $data->alamat_sekarang }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>IBU KANDUNG</label>
                                            <input type="text" class="form-control" name="nama_ibu_kandung"
                                                id="nama_ibu_kandung" value="{{ $data->nama_ibu_kandung }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. REKENING</label>
                                            <input type="text" class="form-control" name="no_rekening"
                                                id="no_rekening" value="{{ $data->no_rekening }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>NO. NPWP</label>
                                            <input type="text" class="form-control" name="no_npwp" id="no_npwp"
                                                value="{{ $data->no_npwp }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. TELEPON</label>
                                            <input type="text" class="form-control" name="no_telp" id="no_telp"
                                                value="{{ $data->no_telp }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. TELEPON DARURAT</label>
                                            <input type="text" class="form-control" name="no_telp_darurat"
                                                id="no_telp_darurat" value="{{ $data->no_telp_darurat }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>Alamat Email</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="{{ $data->email }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TEMPAT KERJA</label>
                                            <input type="text" class="form-control" name="tempat_kerja"
                                                id="tempat_kerja" value="{{ $data->tempat_kerja }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. TELEPON KANTOR</label>
                                            <input type="text" class="form-control" name="no_telp_kantor"
                                                id="no_telp_kantor" value="{{ $data->no_telp_kantor }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>NO. KARYAWAN</label>
                                            <input type="text" class="form-control" name="no_karyawan"
                                                id="no_karyawan" value="{{ $data->no_karyawan }}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="box-footer" style="margin-top: -10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>

                                <a href="{{ route('admin.nasabah.index') }}" class="btn btn-default pull-right">
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
