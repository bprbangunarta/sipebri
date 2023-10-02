@extends('templates.app')
@section('title', 'Data Pengajuan')
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
                                        @include('templates.header-analisa', [
                                            'pengajuan' => $data->kd_pengajuan,
                                        ])
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pendamping.edit', [
                                                'nasabah' => $pengajuan->kode_pengajuan,
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
                                                'nasabah' => $pengajuan->kode_pengajuan,
                                            ])
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <form
                                            action="{{ route('pengajuan.storepengajuan', ['pengajuan' => $pengajuan->nasabah_kode]) }}"
                                            method="POST">
                                            @method('put')
                                            @csrf
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Plafon</div>
                                                        <input type="text" name="kode_pengajuan"
                                                            value="{{ $pengajuan->kode_pengajuan }}" hidden>
                                                        <input type="text" value="{{ $pengajuan->auth }}"
                                                            name="input_user" hidden>
                                                        <input type="text" class="form-control" name="plafon"
                                                            id="plafon" placeholder="10.000.000"
                                                            value="{{ $pengajuan->plafon = 'Rp. ' . number_format($pengajuan->plafon, 0, ',', '.') }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Produk</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Produk" name="produk_kode" id="select-produk"
                                                            required>
                                                            @if (is_null($pengajuan->produk_kode))
                                                                <option value="">Pilih Produk</option>
                                                            @else
                                                                <option value="{{ $pengajuan->produk_kode }}">
                                                                    {{ $pengajuan->produk_nama }}
                                                                </option>
                                                            @endif
                                                            @foreach ($produk as $item)
                                                                <option value="{{ $item->kode_produk }}">
                                                                    {{ $item->nama_produk }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Suku Bunga % per tahun</div>
                                                        <input type="number" class="form-control" name="suku_bunga"
                                                            id="suku_bunga" placeholder="Suku Bunga"
                                                            value="{{ old('suku_bunga', $pengajuan->suku_bunga) }}">
                                                    </div>
                                                </div>
                                                <p></p>

                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">JK Kredit</div>
                                                        <input type="number" class="form-control" name="jangka_waktu"
                                                            id="jangka_waktu" placeholder="Jangka Waktu"
                                                            value="{{ $pengajuan->jangka_waktu }}">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">JK Pokok</div>
                                                        <input type="number" class="form-control" name="jangka_pokok"
                                                            id="jangka_pokok" placeholder="Jangka Pokok"
                                                            value="{{ old('jangka_pokok', $pengajuan->jangka_pokok) }}">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">JK Bunga</div>
                                                        <input type="number" class="form-control" name="jangka_bunga"
                                                            id="jangka_bunga" placeholder="Jangka Bunga"
                                                            value="{{ old('jangka_bunga', $pengajuan->jangka_bunga) }}">
                                                    </div>
                                                </div>
                                                <p></p>

                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Metode RPS</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Metode" name="metode_rps"
                                                            id="select-metode">
                                                            @if (is_null($pengajuan->metode_rps))
                                                                <option value="">Metode RPS</option>
                                                            @else
                                                                <option value="{{ $pengajuan->metode_rps }}">
                                                                    {{ $pengajuan->metode_rps }}</option>
                                                            @endif
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
                                                            @if (is_null($pengajuan->penggunaan))
                                                                <option value="">Pilih Penggunaan</option>
                                                            @else
                                                                <option value="{{ $pengajuan->penggunaan }}">
                                                                    {{ $pengajuan->penggunaan }}</option>
                                                            @endif
                                                            <option value="Modal Usaha">Modal Usaha</option>
                                                            <option value="Investasi">Investasi</option>
                                                            <option value="Konsumtif">Konsumtif</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md">
                                                        <div class="form-label">Resort</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Resort" name="resort_kode"
                                                            id="select-resort">
                                                            @if (is_null($pengajuan->resort_kode))
                                                                <option value="">Resort</option>
                                                            @else
                                                                <option value="{{ $pengajuan->resort_kode }}">
                                                                    {{ $pengajuan->nama_resort }}</option>
                                                            @endif

                                                            @foreach ($resort as $item)
                                                                <option value="{{ $item->kode }}">{{ $item->ket }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>

                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Nama CGC</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih CGC" name="tabungan_cgc" id="select-cgc"
                                                            value="">
                                                            @if (is_null($pengajuan->tabungan_cgc))
                                                                <option value="">Pilih CGC</option>
                                                            @else
                                                                <option value="{{ $pengajuan->tabungan_cgc }}">
                                                                    {{ $pengajuan->tabungan_cgc }}</option>
                                                            @endif

                                                            @foreach ($cgc as $item)
                                                                <option value="{{ $item->noacc }}">{{ $item->noacc }} -
                                                                    {{ $item->fnama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Keterangan</div>
                                                        <input type="text" class="form-control" name="keterangan"
                                                            name="keterangan" placeholder="Keterangan"
                                                            value="{{ old('keterangan', $pengajuan->keterangan) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            @can('pengajuan edit')
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <button type="submit" class="btn btn-primary text-white ms-auto">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            @endcan
                                        </form>

                                        @can('pengajuan otorisasi')
                                            <form
                                                action="{{ route('otorpengajuan', ['otorisasi' => $pengajuan->kode_pengajuan]) }}"
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
    <script>
        // Ketika pilihan sistem berubah
        $("#select-metode").change(function() {
            var selectedValue = $(this).val();

            if (selectedValue === "Flat") {
                // Jika dipilih "Sistem Flat Terelect"
                $("#jangka_pokok").val("1").prop("readonly", true);
                $("#jangka_bunga").val("1").prop("readonly", true);
            } else {
                // Jika dipilih "Sistem Lainnya"
                $("#jangka_pokok").val("").prop("readonly", false);
                $("#jangka_bunga").val("").prop("readonly", false);
            }
        });

        //Validasi jangka poko dan jangka bunga tidak boleh lebih besar dari jangka kredit
        $("#jangka_pokok, #jangka_bunga").on("input", function() {
            var jangkaPoko = parseInt($("#jangka_pokok").val()) || 0;
            var jangkabunga = parseInt($("#jangka_bunga").val()) || 0;
            var jangkaKredit = parseInt($("#jangka_waktu").val()) || 0;

            if (jangkaPoko > jangkaKredit) {
                // Jika jangkaPoko lebih besar dari jangkaKredit
                $("#jangka_pokok").val(jangkaKredit);
            }

            if (jangkabunga > jangkaKredit) {
                // Jika jangkaPoko lebih besar dari jangkaKredit
                $("#jangka_bunga").val(jangkaKredit);
            }
        });
    </script>
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

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-resort'), {
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-cgc'), {
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
