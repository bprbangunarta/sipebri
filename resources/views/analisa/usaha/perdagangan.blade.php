@extends('templates.app')
@section('title', 'Analisa Usaha Pergagangan')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">

                            <div class="container-xl">
                                <div class="row g-2 align-items-center">
                                    @include('templates.header-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('analisa.proses') }}" class="btn btn-primary">
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
                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="6">Informasi Usaha Perdagangan
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Nama Usaha</th>
                                                        <th class="text-center">Pendapatan</th>
                                                        <th class="text-center">Pengeluaran</th>
                                                        <th class="text-center">Laba Bersih</th>
                                                        <th class="text-center" width="5%">Edit</th>
                                                        <th class="text-center" width="5%">Hapus</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($perdagangan as $item)
                                                        <tr>
                                                            <td>{{ $item->nama_usaha }}</td>
                                                            <td>{{ $item->pendapatan }}</td>
                                                            <td>{{ $item->pengeluaran }}</td>
                                                            <td>{{ $item->laba_bersih }}</td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('analisa.usaha.perdagangan.detail', ['usaha' => $item->kd_usaha, 'pengajuan' => $item->kd_pengajuan]) }}">
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
                                                                <form action="#" method="POST">
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

    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Usaha Perdagangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('tambah.store', ['pengajuan' => $data->kd_pengajuan]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Usaha</label>
                                    <input type="text" class="form-control" name="nama_usaha" id="nama_usaha"
                                        placeholder="Nama Usaha" value="{{ old('nama_usaha') }}">
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
