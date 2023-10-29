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
                                <a href="#pendamping" data-toggle="tab">DATA PENDAMPING</a>
                            </li>

                            <li>
                                <a href="#foto" data-toggle="tab">LAMPIRAN FOTO</a>
                            </li>
                        </ul>

                        <form action="{{ route('pendamping.update', ['pendamping' => $pendamping[0]->pengajuan_kode]) }}"
                            method="POST" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="tab-content">
                                <div class="tab-pane active" id="pendamping">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JENIS ID</span>
                                                <select type="text" class="form-control ktp" style="width: 100%;"
                                                    name="identitas" required>
                                                    @if (is_null($pendamping[0]->identitas))
                                                        <option value="1">KTP</option>
                                                    @else
                                                        <option value="{{ $pendamping[0]->identitas }}" selected>
                                                            {{ $pendamping[0]->iden }}</option>
                                                    @endif
                                                    <option value="2">SIM</option>
                                                    <option value="3">PASPORT</option>
                                                    <option value="9">LAINNYA</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NO IDENTITAS</span>
                                                <input type="hidden" value="{{ $nasabah->auth }}" name="input_user"
                                                    required>
                                                <input type="text" class="form-control" name="no_identitas"
                                                    id="no_identitas" placeholder="ENTRI"
                                                    value="{{ old('no_identitas', $pendamping[0]->no_identitas) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA LENGKAP</span>
                                                @if (is_null($pendamping[0]->nama_pendamping))
                                                    <input type="text" class="form-control" name="nama_pendamping"
                                                        id="nama_pendamping" placeholder="ENTRI"
                                                        value="{{ old('nama_pendamping') }}" required>
                                                @else
                                                    <input type="text" class="form-control" name="nama_pendamping"
                                                        id="nama_pendamping" placeholder="ENTRI"
                                                        value="{{ old('nama_pendamping', $pendamping[0]->nama_pendamping) }}"
                                                        required>
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TEMPAT LAHIR</span>
                                                @if (is_null($pendamping[0]->tempat_lahir))
                                                    <input type="text" class="form-control" name="tempat_lahir"
                                                        id="tempat_lahir" placeholder="ENTRI"
                                                        value="{{ old('tempat_lahir') }}" required>
                                                @else
                                                    <input type="text" class="form-control" name="tempat_lahir"
                                                        id="tempat_lahir" placeholder="ENTRI"
                                                        value="{{ old('tempat_lahir', $pendamping[0]->tempat_lahir) }}"
                                                        required>
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TANGGAL LAHIR</span>
                                                @if (is_null($pendamping[0]->tanggal_lahir))
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="tanggal_lahir" id="ttln" required
                                                        data-inputmask="'alias': 'YYYY-DD-MM'" data-mask>
                                                @else
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="tanggal_lahir" id="ttlo"
                                                        data-inputmask="'alias': 'YYYY-DD-MM'" data-mask
                                                        value="{{ $pendamping[0]->tanggal_lahir }}" required>
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">MASA IDENTITAS</span>
                                                @if (is_null($pendamping[0]->masa_identitas))
                                                    <input class="form-control" placeholder="YYYY-MM-DD"
                                                        name="masa_identitas" id="min" required
                                                        data-inputmask="'alias': 'YYYY-MM-DD'" data-mask>
                                                @else
                                                    <input class="form-control" placeholder="YYYY-MM-DD"
                                                        name="masa_identitas" id="mio"
                                                        data-inputmask="'alias': 'YYYY-MM-DD'" data-mask
                                                        value="{{ $pendamping[0]->masa_identitas }}" required>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">STATUS</span>
                                                <select type="text" class="form-control" name="status" required>
                                                    @if (is_null($pendamping[0]->status))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pendamping[0]->status }}">
                                                            {{ $pendamping[0]->status }}
                                                        </option>
                                                    @endif
                                                    <option value="ISTRI">ISTRI</option>
                                                    <option value="SUAMI">SUAMI</option>
                                                    <option value="ORANG TUA">ORANG TUA</option>
                                                    <option value="SAUDARA">SAUDARA</option>
                                                    <option value="SAUDARA">LAINNYA</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NO TELP</span>
                                                @if (is_null($pendamping[0]->no_hp))
                                                    <input type="text" class="form-control" name="no_hp"
                                                        id="no_hp" placeholder="ENTRI" value="{{ old('no_hp') }}"
                                                        required>
                                                @else
                                                    <input type="text" class="form-control" name="no_hp"
                                                        id="no_hp" placeholder="ENTRI"
                                                        value="{{ old('no_hp', $pendamping[0]->no_hp) }}" required>
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">TANGGUNGAN</span>
                                                <select class="form-control text-uppercase" name="tanggungan" required>
                                                    @if (is_null($pendamping[0]->tanggungan))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pendamping[0]->tanggungan }}">
                                                            {{ $pendamping[0]['tgn'] }}</option>
                                                    @endif

                                                    <option value="0">Tidak Ada</option>
                                                    <option value="1">1 Orang</option>
                                                    <option value="2">2 Orang</option>
                                                    <option value="3">3 Orang</option>
                                                    <option value="4">4 Orang</option>
                                                    <option value="5">5 Orang</option>
                                                    <option value="6">6 Orang</option>
                                                    <option value="7">7 Orang</option>
                                                    <option value="8">8 Orang</option>
                                                    <option value="9">9 Orang</option>
                                                    <option value="10">10 Orang</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 100%;float:right;">
                                                <span class="fw-bold">PISAH HARTA</span>
                                                <select class="form-control text-uppercase" placeholder="--PILIH--"
                                                    name="pisah_harta" required>
                                                    @if (is_null($pendamping[0]->pisah_harta))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pendamping[0]->pisah_harta }}">
                                                            {{ $pendamping[0]['pisah'] }}</option>
                                                    @endif

                                                    <option value="Y">YA</option>
                                                    <option value="T" selected>TIDAK</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="foto">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">FOTO FORMAL</span>
                                                <input type="hidden" name="oldphoto"
                                                    value="{{ $pendamping[0]->photo }}">

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
                                                            src="{{ asset('storage/image/photo/' . $pendamping[0]->photo) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">FOTO KTP</span>
                                                <input type="hidden" name="oldphotoktp"
                                                    value="{{ $pendamping[0]->photo_ktp }}">

                                                <input type="file" class="form-control" class="photo_ktp"
                                                    name="photo_ktp" id="photo_ktp" onchange="previewPhotoKtp()">

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
                                                        <img class="img-responsive img-preview-ktp"
                                                            src="{{ asset('storage/image/photo_ktp/' . $pendamping[0]->photo_ktp) }}">
                                                    </div>
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
                            <form action="{{ route('otorpendamping', ['otorisasi' => $nasabah->kd_pengajuan]) }}"
                                method="POST">
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

        // Datemask
        $('#min').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })
        $('#mio').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })

        $('#ttln').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-MM-DD'
        })
        $('#ttlo').inputmask('yyyy-mm-dd', {
            'placeholder': 'YYYY-DD-MM'
        })
    </script>

    <script>
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
    </script>
@endpush
