@extends('templates.app')
@section('title', 'Data Agunan')

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
                                            Data Agunan
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pengajuan.edit') }}" class="btn btn-primary">
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
                                            @include('templates.menu-pendaftaran')
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        @can('tambah agunan')
                                            <div class="card-header bg-transparent mt-auto">
                                                <div class="btn-list justify-content">
                                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#modal-tambah">Tambah</a>
                                                </div>
                                            </div>
                                        @endcan

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Agunan</th>
                                                            <th class="text-center">No Dokumen</th>
                                                            <th class="text-center">Atas Nama</th>
                                                            <th class="text-center" width="10%">Status</th>
                                                            <th class="text-center" width="5%">Ubah</th>
                                                            <th class="text-center" width="5%">Hapus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <b>Jenis</b> : Kendaraan Bermotor Roda 2 <br>
                                                                <b>Dokument</b> : BPKB Motor Non Fiducia
                                                            </td>
                                                            <td>S-03222526</td>
                                                            <td>Zulfadli Rizal</td>
                                                            <td class="text-center">
                                                                <span class="badge bg-danger-lt">Minta Otorisasi</span>
                                                            </td>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="#" title="Edit Data">
                                                                    <span class="badge bg-warning">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon icon-tabler icon-tabler-edit"
                                                                            width="24" height="24"
                                                                            viewBox="0 0 24 24" stroke-width="2"
                                                                            stroke="currentColor" fill="none"
                                                                            stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none"></path>
                                                                            <path
                                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                                                            </path>
                                                                            <path
                                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                                                            </path>
                                                                            <path d="M16 5l3 3"></path>
                                                                        </svg>
                                                                    </span>
                                                                </a>
                                                            </td>

                                                            <td class="text-center">
                                                                <form action="" method="POST">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <button type="submit"
                                                                        style="border: none; background: transparent;"
                                                                        class="confirmdelete">
                                                                        <span class=" badge bg-danger">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="icon icon-tabler icon-tabler-trash"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" stroke-width="2"
                                                                                stroke="currentColor" fill="none"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none"></path>
                                                                                <path d="M4 7l16 0"></path>
                                                                                <path d="M10 11l0 6"></path>
                                                                                <path d="M14 11l0 6"></path>
                                                                                <path
                                                                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                                </path>
                                                                                <path
                                                                                    d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                                </path>
                                                                            </svg>
                                                                        </span>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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

    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agunan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Agunan</label>
                                    <select type="text" class="form-select" placeholder="Jenis Agunan"
                                        name="kode_jenis" id="select-jenis">
                                        <option value="" selected>Jenis Agunan</option>
                                        @foreach ($agunan as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Dokumen</label>
                                    <select type="text" class="form-select" placeholder="Jenis Dokumen"
                                        name="kode_dokumen" id="select-dokumen">
                                        <option value="" selected>Jenis Dokumen</option>
                                        @foreach ($dok as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">No Dokumen</label>
                                    <input type="text" class="form-control" name="no_dokumen" id="no_dokumen"
                                        placeholder="No Dokumen">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Atas Nama</label>
                                    <input type="text" class="form-control" name="atas_nama" id="atas_nama"
                                        placeholder="Nama Lengkap">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Masa Agunan</label>
                                    <input type="date" class="form-control" name="masa_agunan" id="masa_agunan">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Kode Dati</label>
                                    <select type="text" class="form-select" placeholder="Kode Dati" name="kode_dati"
                                        id="select-dati">
                                        <option value="">Kode Dati</option>
                                        <option value="0121">Subang, Kab.</option>
                                        <option value="0122">Bandung Barat, Kab.</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Lokasi</label>
                                    <textarea class="form-control" name="lokasi" id="lokasi"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="catatan" id="catatan"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                        <a href="#" class="btn btn-primary ms-auto">Simpan</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-jenis'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-dokumen'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-dati'), {
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