@extends('theme.app')
@section('title', 'Data Nasabah')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#nasabah" data-toggle="tab">DATA NASABAH</a>
                            </li>

                            <li>
                                <a href="#foto" data-toggle="tab">LAMPIRAN FOTO</a>
                            </li>

                            <li>
                                <a href="#pekerjaan" data-toggle="tab">PEKERJAAN</a>
                            </li>
                        </ul>

                        <form action="{{ route('nasabah.update', ['nasabah' => $nasabah->kode_nasabah]) }}" method="POST"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="tab-content">
                                <div class="tab-pane active" id="nasabah">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NO CIF</span>
                                                <input type="hidden" value="{{ $nasabah->kode_user }}" name="input_user">
                                                <input type="text" class="form-control" name="no_cif" id="no_cif"
                                                    value="{{ $nasabah->nocif }}" readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">JENIS ID</span>
                                                <select class="form-control text-uppercase ktp" style="width: 100%;"
                                                    name="identitas" required>
                                                    <option value="{{ $nasabah->identitas }}" selected>
                                                        {{ $nasabah->iden }}</option>
                                                    <option value="1">KTP</option>
                                                    <option value="2">SIM</option>
                                                    <option value="3">PASPORT</option>
                                                    <option value="9">LAINNYA</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NO IDENTITAS</span>
                                                <input type="text" class="form-control" name="no_identitas"
                                                    id="no_identitas" placeholder="ENTRI"
                                                    value="{{ old('no_identitas', $nasabah->no_identitas) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">MASA IDENTITAS</span>
                                                @if (is_null($nasabah->masa_identitas))
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="masa_identitas" id="min"
                                                        data-inputmask="'alias': 'YYYY-DD-MM'" data-mask />
                                                @else
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="masa_identitas" id="mio"
                                                        data-inputmask="'alias': 'YYYY-DD-MM'" data-mask
                                                        value="{{ $nasabah->masa_identitas }}" />
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA PANGGILAN</span>
                                                <input type="text" class="form-control" name="nama_panggilan"
                                                    id="nama_panggilan" placeholder="ENTRI"
                                                    value="{{ old('nama_panggilan', $nasabah->sname) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NAMA LENGKAP</span>
                                                <input type="text" class="form-control" name="nama_nasabah"
                                                    id="nama_nasabah" placeholder="ENTRI"
                                                    value="{{ old('nama_nasabah', $nasabah->nama_nasabah) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TEMPAT LAHIR</span>
                                                <input type="text" class="form-control" name="tempat_lahir"
                                                    id="tempat_lahir" placeholder="ENTRI"
                                                    value="{{ old('tempat_lahir', $nasabah->tempat_lahir) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TANGGAL LAHIR</span>
                                                @if (is_null($nasabah->tanggal_lahir))
                                                    <input class="form-control" placeholder="YYYY-MM-DD"
                                                        name="tanggal_lahir" id="tlln" />
                                                @else
                                                    <input class="form-control" placeholder="YYYY-MM-DD"
                                                        name="tanggal_lahir" id="ttlo"
                                                        value="{{ $nasabah->tanggal_lahir }}" />
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KABUPATEN</span>
                                                <select class="form-control text-uppercase kab" style="width: 100%;"
                                                    name="kode_dati" id="select-kabupaten" required>
                                                    @if (is_null($nasabah->kode_dati))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option class="text-uppercase" value="{{ $nasabah->kode_dati }}">
                                                            {{ $nasabah->nm_dati }}
                                                        </option>
                                                    @endif

                                                    @foreach ($kab as $item)
                                                        <option class="text-uppercase" value="{{ $item->kode_dati }}">
                                                            {{ $item->nama_dati }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KECAMATAN</span>
                                                <select class="form-control text-uppercase kec kecamatan"
                                                    style="width: 100%;" name="kecamatan" id="select-kecamatan" required>
                                                    @if (is_null($nasabah->kecamatan))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->kecamatan }}">
                                                            {{ $nasabah->kecamatan }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KELURAHAN</span>
                                                <select class="form-control text-uppercase kel kelurahan"
                                                    style="width: 100%;" placeholder="Pilih Kelurahan" name="kelurahan"
                                                    id="select-kelurahan" required>
                                                    @if (is_null($nasabah->kelurahan))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->kelurahan }}">
                                                            {{ $nasabah->kelurahan }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">WILAYAH</span>
                                                @if (is_null($nasabah->kota))
                                                    <input class="form-control dati2" type="text" name="kota"
                                                        id="kota" placeholder="Kota" value="{{ old('kota') }}">
                                                @else
                                                    <input class="form-control" type="text" name="kota"
                                                        id="kota" placeholder="Kota"
                                                        value="{{ old('kota', $nasabah->kota) }}">
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:right;">
                                                <span class="fw-bold">ALAMAT KTP</span>
                                                @if (is_null($nasabah->alamat_ktp))
                                                    <input type="text" class="form-control" name="alamat_ktp"
                                                        id="alamat_ktp" value="{{ old('alamat_ktp') }}"
                                                        placeholder="ENTRI" required>
                                                @else
                                                    <input type="text" class="form-control" name="alamat_ktp"
                                                        id="alamat_ktp"
                                                        value="{{ old('alamat_ktp', $nasabah->alamat_ktp) }}"
                                                        placeholder="ENTRI" required>
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:right;">
                                                <span class="fw-bold">ALAMAT SEKARANG</span>
                                                @if (is_null($nasabah->alamat_sekarang))
                                                    <input type="text" class="form-control" name="alamat_sekarang"
                                                        value="{{ old('alamat_sekarang') }}" placeholder="ENTRI"
                                                        required>
                                                @else
                                                    <input type="text" class="form-control" name="alamat_sekarang"
                                                        id="alamat_sekarang"
                                                        value="{{ old('alamat_sekarang', $nasabah->alamat_sekarang) }}"
                                                        placeholder="ENTRI" required>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">AGAMA</span>
                                                <select class="form-control text-uppercase agama" style="width: 100%;"
                                                    name="agama" required>
                                                    @if (is_null($nasabah->religi))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->agama }}">
                                                            {{ $nasabah->religi }}
                                                        </option>
                                                    @endif

                                                    <option value="1">ISLAM</option>
                                                    <option value="2">KATOLIK</option>
                                                    <option value="3">KRISTEN</option>
                                                    <option value="4">HINDU</option>
                                                    <option value="5">BUDHA</option>
                                                    <option value="6">KONG HU CU</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KELAMIN</span>
                                                <select class="form-control text-uppercase kelamin" style="width: 100%;"
                                                    name="jenis_kelamin" required>
                                                    @if (is_null($nasabah->jk))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->jenis_kelamin }}">
                                                            {{ $nasabah->jk }}</option>
                                                    @endif

                                                    <option value="1">PRIA</option>
                                                    <option value="2">WANITA</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KEWARGANEGARAAN</span>
                                                <select type="text" class="form-control negara" style="width: 100%;"
                                                    name="kewarganegaraan">
                                                    @if (is_null($nasabah->kn))
                                                        <option value="WNI">Warga Negara Indonesia</option>
                                                        <option value="WNA">Warga Negara Asing</option>
                                                    @else
                                                        <option value="{{ $nasabah->kewarganegaraan }}">
                                                            {{ $nasabah->kn }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">GELAR</span>
                                                <select type="text" class="form-control text-uppercase gelar"
                                                    name="pendidikan_kode">
                                                    @if (is_null($nasabah->pendidikan_kode))
                                                        <option value=""></option>
                                                    @else
                                                        <option value="{{ $nasabah->pendidikan_kode }}">
                                                            {{ $nasabah->std }}
                                                        </option>
                                                    @endif

                                                    @foreach ($pend as $item)
                                                        <option value="{{ $item->kode_pendidikan }}">
                                                            {{ $item->nama_pendidikan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">STATUS</span>
                                                <select class="form-control text-uppercase status" style="width: 100%;"
                                                    name="status_pernikahan" required>
                                                    @if (is_null($nasabah->st))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->status_pernikahan }}">
                                                            {{ $nasabah->st }}</option>
                                                    @endif

                                                    <option value="M">MENIKAH</option>
                                                    <option value="L">LAJANG</option>
                                                    <option value="D">DUDA</option>
                                                    <option value="J">JANDA</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PEKERJAAN</span>
                                                <select type="text" class="form-control text-uppercase pekerjaan"
                                                    style="width: 100%;" name="pekerjaan_kode">
                                                    @if (is_null($nasabah->jo))
                                                        <option value="" selected>--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->pekerjaan_kode }}" selected>
                                                            {{ $nasabah->jo }}
                                                        </option>
                                                    @endif

                                                    @foreach ($job as $item)
                                                        <option value="{{ $item->kode_pekerjaan }}">
                                                            {{ $item->nama_pekerjaan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA IBU KANDUNG</span>
                                                <input type="text" class="form-control" name="nama_ibu_kandung"
                                                    id="nama_ibu_kandung" placeholder="ENTRI"
                                                    value="{{ old('nama_ibu_kandung', $nasabah->nama_ibu_kandung) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">REKENING BANK UMUM</span>
                                                <input type="number" class="form-control" name="no_rekening"
                                                    id="no_rekening" placeholder="ENTRI"
                                                    value="{{ old('no_rekening', $nasabah->no_rekening) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NO TELP UTAMA</span>
                                                <input type="number" class="form-control" name="no_telp" id="no_telp"
                                                    placeholder="ENTRI" value="{{ old('no_telp', $nasabah->no_telp) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NO TELP DARURAT</span>
                                                <input type="text" class="form-control" name="no_telp_darurat"
                                                    id="no_telp_darurat" placeholder="ENTRI"
                                                    value="{{ old('no_telp_darurat', $nasabah->no_telp_darurat) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NOMOR NPWP</span>
                                                <input type="text" class="form-control" name="no_npwp" id="no_npwp"
                                                    placeholder="ENTRI" value="{{ old('no_npwp', $nasabah->no_npwp) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">EMAIL ADDRES</span>
                                                <input type="email" class="form-control" name="email" id="email"
                                                    placeholder="ENTRI" value="{{ old('email', $nasabah->email) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">SUMBER DANA</span>
                                                <select type="text" class="form-control text-uppercase sumber_dana"
                                                    style="width: 100%;" name="sumber_dana" required>
                                                    @if (is_null($nasabah->sumber_dana))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->sumber_dana }}">
                                                            {{ $nasabah->dana }}</option>
                                                    @endif

                                                    <option value="1">Hibah</option>
                                                    <option value="2">Lain2</option>
                                                    <option value="3">Penghasilan</option>
                                                    <option value="4">Warisan</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PENGHASILAN UTAMA</span>
                                                <select type="text" class="form-control text-uppercase penghasilan_u"
                                                    style="width: 100%;" name="penghasilan_utama" required>
                                                    @if (is_null($nasabah->penghasilan_utama))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->penghasilan_utama }}">
                                                            {{ $nasabah->hasil }}</option>
                                                    @endif

                                                    <option value="1">s/d 2,5 jt</option>
                                                    <option value="2">s/d 2,5 - 5 jt</option>
                                                    <option value="3">s/d 5 - 7,5 jt</option>
                                                    <option value="4">s/d 7,5 - 10 jt</option>
                                                    <option value="5"> 10 jt</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PENGHASILAN LAINNYA</span>
                                                <select type="text" class="form-control text-uppercase penghasilan_l"
                                                    style="width: 100%;" name="penghasilan_lainnya" required>
                                                    @if (is_null($nasabah->penghasilan_utama))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $nasabah->penghasilan_lainnya }}">
                                                            {{ $nasabah->lain }}</option>
                                                    @endif

                                                    <option value="1">s/d 2,5 jt</option>
                                                    <option value="2">s/d 2,5 - 5 jt</option>
                                                    <option value="3">s/d 5 - 7,5 jt</option>
                                                    <option value="4">s/d 7,5 - 10 jt</option>
                                                    <option value="5">> 10 jt</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="foto">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">FOTO KTP</span>
                                                <input type="hidden" name="oldphotoktp"
                                                    value="{{ $nasabah->photo_ktp }}">
                                                <input type="file" class="form-control" class="photo_ktp"
                                                    name="photo_ktp" id="photo_ktp" onchange="previewPhotoKtp()">

                                                <div class="box box-primary" id="accordion-ktp" style="margin-top:5px;">
                                                    <div class="box-header">
                                                        <span class="fw-bold">PREVIEW</span>
                                                        <div class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip"
                                                                title="" data-original-title="Collapse">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad" style="">
                                                        <img class="img-responsive img-preview-ktp"
                                                            src="{{ asset('storage/image/photo_ktp/' . $nasabah->photo_ktp) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="margin-top:-10px;width: 100%;float:right;">
                                                <span class="fw-bold">FOTO FORMAL</span>
                                                <input type="hidden" name="oldphoto" value="{{ $nasabah->photo }}">

                                                <input type="file" class="form-control" class="photo" name="photo"
                                                    id="photo" onchange="previewPhoto()">

                                                <div class="box box-primary" id="accordion-photo"
                                                    style="margin-top:5px;">
                                                    <div class="box-header">
                                                        <span class="fw-bold">PREVIEW</span>
                                                        <div class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip"
                                                                title="" data-original-title="Collapse">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad" style="">
                                                        <img class="img-responsive img-preview"
                                                            src="{{ asset('storage/image/photo/' . $nasabah->photo) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">FOTO KK</span>
                                                <input type="hidden" name="oldphotokk"
                                                    value="{{ $nasabah->photo_kk }}">

                                                <input type="file" class="form-control" class="photo_kk"
                                                    name="photo_kk" id="photo_kk" onchange="previewPhotoKk()">

                                                <div class="box box-primary" id="accordion-kk" style="margin-top:5px;">
                                                    <div class="box-header">
                                                        <span class="fw-bold">PREVIEW</span>
                                                        <div class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip"
                                                                title="" data-original-title="Collapse">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad" style="">
                                                        <img class="img-responsive img-preview-kk"
                                                            src="{{ asset('storage/image/photo_kk/' . $nasabah->photo_kk) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="margin-top:-10px;width: 100%;float:right;">
                                                <span class="fw-bold">FOTO SELFIE</span>
                                                <input type="hidden" name="oldphotoselfie"
                                                    value="{{ $nasabah->photo_selfie }}">
                                                <input type="file" class="form-control" class="photo_selfie"
                                                    name="photo_selfie" id="photo_selfie" onchange="previewPhotoSelfi()">

                                                <div class="box box-primary" id="accordion-selfie"
                                                    style="margin-top:5px;">
                                                    <div class="box-header">
                                                        <span class="fw-bold">PREVIEW</span>
                                                        <div class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip"
                                                                title="" data-original-title="Collapse">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad" style="">
                                                        <img class="img-responsive img-preview2"
                                                            src="{{ asset('storage/image/photo_selfie/' . $nasabah->photo_selfie) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="pekerjaan">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="row" style="margin-top:5px;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>TEMPAT KERJA</label>
                                                    <input type="text" class="form-control" name="tempat_kerja"
                                                        id="tempat_kerja" placeholder="ENTRI"
                                                        value="{{ old('tempat_kerja', $nasabah->tempat_kerja) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NO TELP KANTOR</label>
                                                    <input type="text" class="form-control" name="no_telp_kantor"
                                                        id="no_telp_kantor" placeholder="ENTRI"
                                                        value="{{ old('no_telp_kantor', $nasabah->no_telp_kantor) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NO INDUK KARYAWAN</label>
                                                    <input type="text" class="form-control" name="no_karyawan"
                                                        id="no_karyawan" placeholder="NIK Karyawan"
                                                        value="{{ old('no_karyawan', $nasabah->no_karyawan) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @can('edit pengajuan kredit')
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                @endcan
                            </div>
                        </form>

                        @can('otorisasi pengajuan kredit')
                            <form action="{{ route('otornasabah', ['otorisasi' => $nasabah->kd_nasabah]) }}" method="POST">
                                @csrf
                                <div class="box-body" style="margin-top:-20px;">
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:10px;width:100%">OTORISASI</button>
                                </div>
                            </form>
                        @endcan

                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        // Select2
        $('.ktp').select2()
        $('.kab').select2()
        $('.kec').select2()
        $('.kel').select2()
        $('.agama').select2()
        $('.kelamin').select2()
        $('.negara').select2()
        $('.gelar').select2()
        $('.status').select2()
        $('.pekerjaan').select2()
        $('.sumber_dana').select2()
        $('.penghasilan_u').select2()
        $('.penghasilan_l').select2()

        // Datemask
        $('#min').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })
        $('#mio').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })

        $('#ttln').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })
        $('#ttlo').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })
    </script>

    <script>
        // JS Image Preview
        function previewPhotoKtp() {
            const image = document.getElementById('photo_ktp');
            const imgPreview = document.querySelector('.img-preview-ktp');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }

        function previewPhotoKk() {
            const image = document.getElementById('photo_kk');
            const imgPreview = document.querySelector('.img-preview-kk');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }

        function previewPhoto() {
            const image = document.getElementById('photo');
            const imgPreview = document.querySelector('.img-preview');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }

        function previewPhotoSelfi() {
            const image = document.getElementById('photo_selfie');
            const imgPreview = document.querySelector('.img-preview2');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }
    </script>


    <script>
        $(document).ready(function() {
            //Ambil data kabupaten
            $("#select-kabupaten").on('change', function() {
                $('#select-kecamatan').empty()
                // $('#select-kelurahan').empty()
                var nama = $("#select-kabupaten").val();
                $.ajax({
                    url: "/nasabah/kabupaten",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: nama
                    },
                    success: function(response) {
                        let st = JSON.stringify(response);
                        let obj = Object.values(response);
                        var ch = []
                        for (var i = 0; i < obj.length; i++) {
                            if (ch.indexOf(obj[i].kecamatan) === -1) {
                                ch.push(obj[i].kecamatan)

                            }
                        }

                        ch.sort()

                        ch.forEach(data => {
                            $('#select-kecamatan').append($('<option>', {
                                value: data,
                                text: data
                            }));
                        });

                    },
                    error: function(xhr) {
                        // Tindakan jika terjadi error
                        console.log(xhr.responseText);
                    }
                });
            });

            //Ambil data kecamatan
            $("#select-kecamatan").on('change', function() {
                $('#select-kelurahan').empty()
                // $('#select-kelurahan').empty()
                var nama = $("#select-kecamatan").val();
                $.ajax({
                    url: "/nasabah/kecamatan",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: nama
                    },
                    success: function(response) {
                        let st = JSON.stringify(response);
                        let obj = Object.values(response);
                        var ch = []
                        for (var i = 0; i < obj.length; i++) {
                            if (ch.indexOf(obj[i].kelurahan) === -1) {
                                ch.push(obj[i].kelurahan)

                            }
                        }

                        ch.sort()

                        ch.forEach(data => {
                            $('#select-kelurahan').append($('<option>', {
                                value: data,
                                text: data
                            }));
                        });

                    },
                    error: function(xhr) {
                        // Tindakan jika terjadi error
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
