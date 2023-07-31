@extends('templates.app')
@section('title', 'Data Pendidikan')
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
                                    <div class="page-pretitle">
                                        Master
                                    </div>
                                    <h2 class="page-title">
                                        Data Pendidikan
                                    </h2>
                                </div>
                                <!-- Page title actions -->
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#modal-tambah">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 5l0 14"></path>
                                                <path d="M5 12l14 0"></path>
                                            </svg>
                                            Tambah
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                        <form action="{{ route('pendidikan.index') }}" method="GET">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Nama Gelar"
                                    value="{{ Request('name') }}">
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
                            <table class="table table-bordered table-vcenter">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="3%">No</th>
                                        <th class="text-center" width="7%">Kode</th>
                                        <th class="text-center">Nama Pekerjaan</th>
                                        <th class="text-center" width="20%">Created At</th>
                                        <th class="text-center" width="20%">Updated At</th>
                                        <th class="text-center" width="5%">Ubah</th>
                                        <th class="text-center" width="5%">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendidikan as $data)
                                    <tr>
                                        <td class="text-center">
                                            {{ $loop->iteration + $pendidikan->firstItem() - 1 }}
                                        </td>
                                        <td class="text-center">{{ $data->kode_pendidikan }}</td>
                                        <td>{{ $data->nama_pendidikan }}</td>
                                        <td class="text-center">{{ $data->created_at }}</td>
                                        <td class="text-center">{{ $data->updated_at }}</td>
                                        <td class="text-center">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal-edit"
                                                data-id="{{ $data->kode_pendidikan }}">
                                                <span class="badge bg-warning">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-edit" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                                        <td class="text-center">
                                            <form
                                                action="{{ route('pendidikan.destroy', ['pendidikan' => $data->kode_pendidikan]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" style="border: none; background: transparent;">
                                                    <span class=" badge bg-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-trash" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                            </path>
                                                            <path d="M4 7l16 0"></path>
                                                            <path d="M10 11l0 6"></path>
                                                            <path d="M14 11l0 6"></path>
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                            </path>
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
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
                            <p></p>

                            {{ $pendidikan->links('vendor.pagination.bootstrap-5') }}

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
                <h5 class="modal-title">Tambah Pendidikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('pendidikan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Kode Pendidikan</label>
                                <input type="text" class="form-control" name="kode_pendidikan" id="kode_pendidikan">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Nama Pendidikan</label>
                                <input type="text" class="form-control" name="nama_pendidikan" id="nama_pendidikan">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                    <button type="submit" class="btn btn-primary text-white ms-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pendidikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('pendidikan.update', ['pendidikan' => $data->kode_pendidikan]) }}" method="POST">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Kode Pendidikan</label>
                                <input type="text" class="form-control" name="kode_pendidikan" id="kode_pend">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Nama Pendidikan</label>
                                <input type="text" class="form-control" name="nama_pendidikan" id="nama_pend">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                    <button type="submit" class="btn btn-primary text-white ms-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/myscript/pendidikan.js') }}"></script>
@endsection