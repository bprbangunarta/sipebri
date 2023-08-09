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
                                        <div class="page-pretitle">
                                            Pendaftaran
                                        </div>
                                        <h2 class="page-title">
                                            Data Pendamping
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('nasabah.edit') }}" class="btn btn-primary">
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
                                                'nasabah' => $nasabah->kode_nasabah,
                                            ])
                                        </div>
                                    </div>
                                    <div class="col d-flex flex-column">
                                        <div class="card-body">

                                            <div class="row g-3">
                                                <div class="col-md">
                                                    <div class="form-label">Jenis ID</div>
                                                    <select type="text" class="form-select" placeholder="Pilih Identitas"
                                                        name="identitas" id="select-identitas">
                                                        <option value="">Pilih Identitas</option>
                                                        <option value="1">KTP</option>
                                                        <option value="2">SIM</option>
                                                        <option value="3">Pasport</option>
                                                        <option value="9">Lainnya</option>
                                                    </select>
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">No Identitas</div>
                                                    <input type="text" class="form-control" name="no_identitas"
                                                        id="no_identitas" placeholder="3213XXXXX">
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Masa Identitas</div>
                                                    <input type="date" class="form-control" name="masa_identitas"
                                                        id="masa_identitas">
                                                </div>
                                            </div>
                                            <p></p>
                                            <div class="row g-3">
                                                <div class="col-md">
                                                    <div class="form-label">Nama Lengkap</div>
                                                    <input type="text" class="form-control" name="nama_pendamping"
                                                        id="nama_pendamping" placeholder="Nama Lengkap">
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Tempat Lahir</div>
                                                    <input type="text" class="form-control" name="tempat_lahir"
                                                        id="tempat_lahir" placeholder="Tempat Lahir">
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Tanggal Lahir</div>
                                                    <input type="date" class="form-control" name="tempat_lahir"
                                                        id="tempat_lahir">
                                                </div>
                                            </div>
                                            <p></p>
                                            <div class="row g-3">
                                                <div class="col-md">
                                                    <div class="form-label">Status</div>
                                                    <select type="text" class="form-select" placeholder="Pilih Status"
                                                        name="status" id="select-status">
                                                        <option value="">Pilih Status</option>
                                                        <option value="Istri">Istri</option>
                                                        <option value="Suami">Suami</option>
                                                        <option value="Orang Tua">Orang Tua</option>
                                                        <option value="Saudara">Saudara</option>
                                                    </select>
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Tanggungan</div>
                                                    <select type="text" class="form-select" placeholder="Tanggungan"
                                                        name="tanggungan" id="select-tanggungan">
                                                        <option value="">Pilih Tanggungan</option>
                                                        <option value="0">Tidak Ada</option>
                                                        <option value="1">1 Orang</option>
                                                        <option value="2">2 Orang</option>
                                                        <option value="3">3 Orang</option>
                                                        <option value="4">4 Orang</option>
                                                        <option value="5">5 Orang</option>
                                                    </select>
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Pisah Harta</div>
                                                    <select type="text" class="form-select" placeholder="Pisah Harta"
                                                        name="pisah_harta" id="select-pisah-harta">
                                                        <option value="">Pisah Harta</option>
                                                        <option value="Y">Iya</option>
                                                        <option value="T">Tidak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <p></p>
                                            <div class="row g-3">
                                                <div class="col-md">
                                                    <div class="form-label">Photo Formal</div>
                                                    <input type="file" class="form-control" class="photo"
                                                        id="photo">
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-label">Photo Selfie</div>
                                                    <input type="file" class="form-control" class="photo_selfie"
                                                        id="photo_selfie">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent mt-auto">
                                            <div class="btn-list justify-content-end">
                                                <a href="#" class="btn btn-primary">
                                                    Simpan
                                                </a>
                                            </div>
                                        </div>
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
@endpush
