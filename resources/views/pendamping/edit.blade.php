@extends('templates.app')
@section('title', 'Data Pendamping')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="container-xl">
                                <div class="row g-2 align-items-center">
                                    <div class="col">
                                        <!-- Page pre-title -->
                                        @include('templates.header-analisa', [
                                            'pengajuan' => $data->kd_pengajuan,
                                        ])
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('nasabah.edit', [
                                                'nasabah' => $nasabah->kd_pengajuan,
                                            ]) }}"
                                                class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-left" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l14 0"></path>
                                                    <path d="M5 12l6 6"></path>
                                                    <path d="M5 12l6 -6"></path>
                                                </svg>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">

                            <div class="card">
                                <div class="row g-0">
                                    <div class="col-3 d-none d-md-block border-end">
                                        <div class="card-body">
                                            @include('templates.menu-pendaftaran', [
                                                'nasabah' => $nasabah->kd_pengajuan,
                                            ])
                                        </div>
                                    </div>
                                    <div class="col d-flex flex-column">
                                        <form
                                            action="{{ route('pendamping.update', ['pendamping' => $pendamping[0]->pengajuan_kode]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">

                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Jenis ID</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Identitas" name="identitas"
                                                            id="select-identitas">
                                                            @if (is_null($pendamping[0]->identitas))
                                                                <option value="">Pilih Identitas</option>
                                                            @else
                                                                <option value="{{ $pendamping[0]->identitas }}" selected>
                                                                    {{ $pendamping[0]->iden }}</option>
                                                            @endif
                                                            <option value="1">KTP</option>
                                                            <option value="2">SIM</option>
                                                            <option value="3">Pasport</option>
                                                            <option value="9">Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">No Identitas</div>
                                                        <input type="text" value="{{ $nasabah->auth }}" name="input_user"
                                                            hidden>
                                                        <input type="text" class="form-control" name="no_identitas"
                                                            id="no_identitas" placeholder="3213XXXXX"
                                                            value="{{ old('no_identitas', $pendamping[0]->no_identitas) }}">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Masa Identitas</div>
                                                        @if (is_null($pendamping[0]->masa_identitas))
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="masa_identitas" id="datepicker-masa-identitas" />
                                                        @else
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="masa_identitas" id="datepicker-masa-identitas-old"
                                                                value="{{ $pendamping[0]->masa_identitas }}">
                                                        @endif
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Nama Lengkap</div>
                                                        @if (is_null($pendamping[0]->nama_pendamping))
                                                            <input type="text" class="form-control"
                                                                name="nama_pendamping" id="nama_pendamping"
                                                                placeholder="Nama Lengkap"
                                                                value="{{ old('nama_pendamping') }}">
                                                        @else
                                                            <input type="text" class="form-control"
                                                                name="nama_pendamping" id="nama_pendamping"
                                                                placeholder="Nama Lengkap"
                                                                value="{{ old('nama_pendamping', $pendamping[0]->nama_pendamping) }}">
                                                        @endif

                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Tempat Lahir</div>
                                                        @if (is_null($pendamping[0]->tempat_lahir))
                                                            <input type="text" class="form-control" name="tempat_lahir"
                                                                id="tempat_lahir" placeholder="Tempat Lahir"
                                                                value="{{ old('tempat_lahir') }}">
                                                        @else
                                                            <input type="text" class="form-control" name="tempat_lahir"
                                                                id="tempat_lahir" placeholder="Tempat Lahir"
                                                                value="{{ old('tempat_lahir', $pendamping[0]->tempat_lahir) }}">
                                                        @endif

                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Tanggal Lahir</div>
                                                        @if (is_null($pendamping[0]->tanggal_lahir))
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="tanggal_lahir" id="datepicker-tanggal-lahir" />
                                                        @else
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="tanggal_lahir" id="datepicker-tanggal-lahir-old"
                                                                value="{{ $pendamping[0]->tanggal_lahir }}">
                                                        @endif
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Status</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Status" name="status" id="select-status">
                                                            @if (is_null($pendamping[0]->status))
                                                                <option value="">Pilih Status</option>
                                                            @else
                                                                <option value="{{ $pendamping[0]->status }}">
                                                                    {{ $pendamping[0]->status }}</option>
                                                            @endif
                                                            <option value="Istri">Istri</option>
                                                            <option value="Suami">Suami</option>
                                                            <option value="Orang Tua">Orang Tua</option>
                                                            <option value="Saudara">Saudara</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Tanggungan</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Tanggungan" name="tanggungan"
                                                            id="select-tanggungan">
                                                            @if (is_null($pendamping[0]->tanggungan))
                                                                <option value="">Pilih Tanggungan</option>
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
                                                    <div class="col-md">
                                                        <div class="form-label">Pisah Harta</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pisah Harta" name="pisah_harta"
                                                            id="select-pisah-harta">
                                                            @if (is_null($pendamping[0]->pisah_harta))
                                                                <option value="">Pisah Harta</option>
                                                            @else
                                                                <option value="{{ $pendamping[0]->pisah_harta }}">
                                                                    {{ $pendamping[0]['pisah'] }}</option>
                                                            @endif

                                                            <option value="Y">Ya</option>
                                                            <option value="T">Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Photo Formal</div>
                                                        <input type="text" name="oldphoto"
                                                            value="{{ $pendamping[0]->photo }}" hidden>

                                                        <input type="file" class="form-control" class="photo"
                                                            name="photo" id="photo" onchange="previewPhoto()">

                                                        <div class="accordion mt-2" id="accordion-ktp">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-photo">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview"
                                                                            src="{{ asset('storage/image/photo/' . $pendamping[0]->photo) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Photo KTP</div>
                                                        <input type="text" name="oldphotoktp"
                                                            value="{{ $pendamping[0]->photo_ktp }}" hidden>

                                                        <input type="file" class="form-control" class="photo_ktp"
                                                            name="photo_ktp" id="photo_ktp" onchange="previewPhotoKtp()">

                                                        <div class="accordion mt-2" id="accordion-ktp">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-ktp">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview-ktp"
                                                                            src="{{ asset('storage/image/photo_ktp/' . $pendamping[0]->photo_ktp) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @can('pendamping edit')
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <button type="submit" class="btn btn-primary text-white ms-auto">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            @endcan
                                        </form>

                                        @can('pendamping otorisasi')
                                            <form
                                                action="{{ route('otorpendamping', ['otorisasi' => $nasabah->kd_pengajuan]) }}"
                                                method="POST">
                                                @csrf
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <input type="text" name="otorisasi" id="otorisasi" value="A"
                                                            hidden>
                                                        <button type="submit" class="btn btn-primary text-white ms-auto">
                                                            Otorisasi
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endcan

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // JS Darepicker
        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-masa-identitas'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-masa-identitas-old'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-tanggal-lahir'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-tanggal-lahir-old'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-identitas'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-tanggungan'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-pisah-harta'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
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
