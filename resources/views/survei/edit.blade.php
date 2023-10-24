@extends('templates.app')
@section('title', 'Data Survayor')
@yield('jquery')
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
                                        @include('templates.header-analisa', [$data])
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pengajuan.agunan', [
                                                'nasabah' => $data->kd_pengajuan,
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
                                                'nasabah' => $data->kd_pengajuan,
                                            ])
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <form action="{{ route('survei.update', ['survei' => $data->kode_pengajuan]) }}"
                                            method="POST">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">

                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Wilayah</div>
                                                        <input type="text" value="{{ $data->auth }}" name="input_user"
                                                            hidden>
                                                        <input type="text" value="{{ $data->kode_pengajuan }}"
                                                            name="pengajuan_kode" hidden>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Wilayah" name="kantor_kode"
                                                            id="select-wilayah">
                                                            @if (is_null($survey->kantor_kode))
                                                                <option value="">Pilih Wilayah</option>
                                                            @else
                                                                <option value="{{ $survey->kantor_kode }}">
                                                                    {{ $survey->nama_kantor }}</option>
                                                            @endif

                                                            @foreach ($kantor->sortBy('nama_kantor') as $item)
                                                                <option value="{{ $item->kode_kantor }}">
                                                                    {{ $item->nama_kantor }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kasi Analis</div>
                                                        <select type="text" class="form-select" placeholder="Pilih Kasi"
                                                            name="kasi_kode" id="select-kasi">
                                                            @if (is_null($survey->kasi_kode))
                                                                <option value="">Pilih Kasi</option>
                                                            @else
                                                                <option value="{{ $survey->kasi_kode }}">
                                                                    {{ $survey->nama_kasi }}</option>
                                                            @endif

                                                            @foreach ($kasi as $item)
                                                                <option value="{{ $item->code_user }}">{{ $item->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Surveyor</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Survayor" name="surveyor_kode"
                                                            id="select-survayor">
                                                            @if (is_null($survey->surveyor_kode))
                                                                <option value="">Pilih Surveyor</option>
                                                            @else
                                                                <option value="{{ $survey->surveyor_kode }}">
                                                                    {{ $survey->nama_surveyor }}</option>
                                                            @endif

                                                            @foreach ($staff as $item)
                                                                <option value="{{ $item->code_user }}">{{ $item->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md">

                                                    </div>
                                                </div>
                                            </div>

                                            @can('edit pengajuan kredit')
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <button type="submit" class="btn btn-primary text-white ms-auto">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            @endcan
                                        </form>

                                        @can('otorisasi pengajuan kredit')
                                            <form action="{{ route('otorsurvei', ['otorisasi' => $data->kd_pengajuan]) }}"
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

@push('myscript')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-kasi'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-wilayah'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-survayor'), {
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
