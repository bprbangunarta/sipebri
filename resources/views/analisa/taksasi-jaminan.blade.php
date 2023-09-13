@extends('templates.app')
@section('title', 'Taksasi Jaminan')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class=" card-header">

                            <div iv class="container-xl">
                                <div div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('kepemilikan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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

                                    @include('templates.menu-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col d-flex flex-column">
                                        <div class="card-header bg-transparent mt-auto">
                                            <div class="btn-list justify-content">
                                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modal-tambah">Tambah</a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Agunan</th>
                                                            <th class="text-center">No Dokumen</th>
                                                            <th class="text-center">Taksasi</th>
                                                            <th class="text-center" width="5%">Ubah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($taksasi as $item)
                                                            <tr>
                                                                <td>
                                                                    <b>Jenis</b> : {{ $item->jenis_agunan }} <br>
                                                                    <b>Dokumen</b> : {{ $item->jenis_dokumen }}
                                                                </td>
                                                                <td>
                                                                    <b>No Doukumen</b> : {{ $item->no_dokumen }} <br>
                                                                    <b>Atas Nama</b> : {{ $item->atas_nama }}
                                                                </td>
                                                                <td>{{ $item->nilai_taksasi = 'RP ' . number_format($item->nilai_taksasi, 0, ',', '.') }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#modal-edit"
                                                                        data-id="{{ $item->id }}">
                                                                        <span class="badge bg-warning">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="icon icon-tabler icon-tabler-edit"
                                                                                width="24" height="24"
                                                                                viewBox="0 0 24 24" stroke-width="2"
                                                                                stroke="currentColor" fill="none"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round">
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Taksasi Jaminan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @foreach ($taksasi as $item)
                    <form action="{{ route('jaminan.update', ['jaminan' => $item->id]) }}" method="POST">
                @endforeach
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Agunan</label>
                                <input type="text" name="id" id="data" hidden>
                                <input type="text" class="form-control" name="data" id="jenis" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Dokumen</label>
                                <input type="text" class="form-control" name="jenis_dokumen_kode" id="dokumen"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" name="no_dokumen" id="no_dok"
                                    placeholder="No Dokumen" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Atas Nama</label>
                                <input type="text" class="form-control" name="atas_nama" id="atas_nama"
                                    placeholder="Nama Lengkap" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Taksasi</label>
                                <input type="text" class="form-control" name="nilai_taksasi" id="taksasi"
                                    placeholder="Rp ">
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
        <script src="{{ asset('assets/js/myscript/taksasi.js') }}"></script>
    @endsection
