@extends('templates.app')
@section('title', 'Data Pengajuan')

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
                                            Data Pengajuan
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pendamping.edit') }}" class="btn btn-primary">
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
                                        <form action="#">
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Plafon</div>
                                                        <input type="text" class="form-control" name="plafon"
                                                            id="plafon" placeholder="10.000.000">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Suku Bunga</div>
                                                        <input type="text" class="form-control" name="suku_bunga"
                                                            id="suku_bunga" placeholder="Suku Bunga">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Produk</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Produk" name="produk_kode"
                                                            id="select-produk">
                                                            <option value="">Pilih Produk</option>
                                                            <option value="KRU">KRU - Kredit Multiguna</option>
                                                            <option value="KBT">KBT - Kredit Budidaya Tani
                                                            </option>
                                                            <option value="KPS">KPS - Kredit Kepesta Raja</option>
                                                            <option value="KTA">KTA - Kredit Tanpa Agunan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">JK Kredit</div>
                                                        <input type="number" class="form-control" name="jangka_waktu"
                                                            id="jangka_waktu" placeholder="Jangka Waktu">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">JK Pokok</div>
                                                        <input type="number" class="form-control" name="jangka_pokok"
                                                            id="jangka_pokok" placeholder="Jangka Pokok">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">JK Bunga</div>
                                                        <input type="number" class="form-control" name="jangka_bunga"
                                                            id="jangka_bunga" placeholder="Jangka Bunga">
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Metode RPS</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Metode" name="metode_rps" id="select-metode">
                                                            <option value="">Metode RPS</option>
                                                            <option value="Flat">Flat</option>
                                                            <option value="PRK">PRK</option>
                                                            <option value="Efektif">Efektif</option>
                                                            <option value="Efektif Anuitas">Efektif Anuitas</option>
                                                            <option value="Efektif Musiman">Efektif Musiman</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Penggunaan</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Penggunaan" name="penggunaan"
                                                            id="select-penggunaan">
                                                            <option value="">Pilih Penggunaan</option>
                                                            <option value="Modal Usaha">Modal Usaha</option>
                                                            <option value="Investasi">Investasi</option>
                                                            <option value="Konsumtif">Konsumtif</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Keterangan</div>
                                                        <input type="text" class="form-control" name="keterangan"
                                                            name="keterangan" placeholder="Keterangan">
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

                                        </form>

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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-produk'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-metode'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-penggunaan'), {
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
