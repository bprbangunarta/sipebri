@extends('templates.app')
@section('title', 'Pengajuan Kredit')

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
                                            Pengajuan Kredit
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->

                                    @can('tambah pendaftaran')
                                        <div class="col-auto ms-auto d-print-none">
                                            <div class="btn-list">
                                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal-tambah">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 5l0 14"></path>
                                                        <path d="M5 12l14 0"></path>
                                                    </svg>
                                                    Tambah
                                                </a>
                                            </div>
                                        </div>
                                    @endcan

                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                            <form action="{{ route('pengajuan.index') }}" method="GET">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Kode atau Nama Nasabah" value="{{ Request('name') }}">
                                    <button class="btn" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                                            </path>
                                        </svg>
                                        Filter
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-vcenter fs-5">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="3%">No</th>
                                            <th class="text-center" width="7%">Kode</th>
                                            <th class="text-center">Nama Debitur</th>
                                            <th class="text-center" width="40%">Alamat</th>
                                            <th class="text-center" width="10%">Plafon</th>
                                            <th class="text-center" width="3%">JK</th>
                                            <th class="text-center" width="10%">Status</th>
                                            @can('validasi pendaftaran')
                                                <th class="text-center" width="5%">Aksi</th>
                                            @endcan
                                            @can('edit pendaftaran')
                                                <th class="text-center" width="5%">Aksi</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($data as $item)
                                            <tr>
                                                <td class="text-center">{{ $no }}</td>
                                                <td>{{ $item->kode }}</td>
                                                <td>{{ strtoupper($item->nama) }}</td>
                                                @if (is_null($item->alamat))
                                                    <td class="text-center">-</td>
                                                @else
                                                    <td>{{ $item->alamat }}
                                                    </td>
                                                @endif
                                                @php
                                                    $item->plafon = 'Rp. ' . number_format($item->plafon, 2, ',', '.');
                                                @endphp
                                                <td class="text-start">{{ $item->plafon }}</td>
                                                <td class="text-center">{{ $item->jk }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-lt">Lengkapi Data</span>
                                                </td>
                                                @can('validasi pendaftaran')
                                                    <td class="text-center">
                                                        <a href="{{ route('nasabah.validasi') }}" title="Validasi Data">
                                                            <span class="badge bg-success">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-clipboard-check"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                    </path>
                                                                    <path
                                                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                                                    </path>
                                                                    <path
                                                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                                                    </path>
                                                                    <path d="M9 14l2 2l4 -4"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </td>
                                                @endcan
                                                @can('edit pendaftaran')
                                                    <td class="text-center">
                                                        <a href="{{ route('nasabah.edit', ['nasabah' => $item->kode, 'nama' => $item->nama]) }}"
                                                            title="Edit Data">
                                                            <span class="badge bg-warning">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-edit" width="24"
                                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                    stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                    </path>
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
                                                @endcan
                                                <input type="text" hidden value="{{ $no++ }}" readonly>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('nasabah.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis ID</label>
                                    <select type="text" class="form-select" placeholder="Pilih Identitas"
                                        name="identitas" id="select-identitas">
                                        <option value="">Pilih Identitas</option>
                                        <option value="1">KTP</option>
                                        <option value="2">SIM</option>
                                        <option value="3">Pasport</option>
                                        <option value="9">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">No. Indentias</label>
                                    <input type="text" class="form-control" name="no_identitas" id="no_identitas"
                                        placeholder="3213000000000000">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah"
                                        placeholder="Nama Lengkap">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Plafon</label>
                                    <input type="text" class="form-control" name="plafon" id="plafond"
                                        placeholder="10.000.000">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Jangka Waktu</label>
                                    <select type="text" class="form-select" placeholder="Jengka Waktu"
                                        name="jangka_waktu" id="select-jk">
                                        <option value="">Pilih JK</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12">12 Bulan</option>
                                        <option value="24">24 Bulan</option>
                                        <option value="36">36 Bulan</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                        <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
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
            window.TomSelect && (new TomSelect(el = document.getElementById('select-jk'), {
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

        //Format rupiah
        var rupiah = document.getElementById('plafond');
        rupiah.addEventListener('keyup', function(e) {
            rupiah.value = formatRupiah(this.value, 'Rp. ');
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
