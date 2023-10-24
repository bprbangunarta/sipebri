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
                                <a href="#" class="text-bold">
                                    DATA NASABAH
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                <form action="{{ route('nasabah.update', ['nasabah' => $nasabah->kode_nasabah]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">NO CIF</span>
                                                <input type="hidden" value="{{ $nasabah->kode_user }}" name="input_user">
                                                <input type="text" class="form-control" name="no_cif" id="no_cif"
                                                    value="{{ $nasabah->nocif }}" readonly>
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">JENIS ID</span>
                                                <select type="text" class="form-select ktp" style="width: 100%;"
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
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="tanggal_lahir" id="tlln" />
                                                @else
                                                    <input class="form-control" placeholder="YYYY-DD-MM"
                                                        name="tanggal_lahir" id="ttlo"
                                                        value="{{ $nasabah->tanggal_lahir }}" />
                                                @endif
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KABUPATEN</span>
                                                <select class="form-select kab" style="width: 100%;" name="kode_dati"
                                                    id="select-kabupaten" required>
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
                                                <select class="form-select kec kecamatan" style="width: 100%;"
                                                    name="kecamatan" id="select-kecamatan" required>
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
                                                <select type="text" class="form-select kel kelurahan"
                                                    style="width: 100%;" placeholder="Pilih Kelurahan" name="kelurahan"
                                                    id="select-kelurahan">
                                                    @if (is_null($nasabah->kelurahan))
                                                        <option value="">Pilih Kecamatan</option>
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
                                        </div>


                                        <div class="div-right">
                                            <div style="width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KONSUMSI POKOK" name="nama1" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="" id="">
                                            </div>

                                            <div style="width: 49.5%;float:right;">
                                                <span class="fw-bold">NAMA ITEM</span>
                                                <input value="KESEHATAN" name="nama2" hidden>
                                                <input class="form-control input-sm form-border" type="text"
                                                    name="" id="">
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

{{-- @section('scr')

@endsection --}}
