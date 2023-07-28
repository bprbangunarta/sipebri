@extends('templates.app')
@section('title', 'Data Users')

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
                                        User Management
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

                    <div class="card-body border-bottom py-3">

                        <form action="{{ route('user.index') }}" method="GET">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Nama Pengguna" value="{{ Request('name') }}">
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
                                        <th class="text-center">Nama User</th>
                                        <th class="text-center">Wilayah</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">Username</th>
                                        <th class="text-center">Hak Akses</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="6%">Edit</th>
                                        <th class="text-center" width="6%">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $data)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration + $users->firstItem() -1}}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->region }}</td>
                                        <td class="text-center">{{ $data->code_user }}</td>
                                        <td>{{ $data->username }}</td>
                                        <td>
                                            @if (empty($data->position))
                                            <font>-</font>
                                            @else
                                            {{ $data->position }}
                                            @endif

                                        </td>
                                        <td class="text-center">
                                            @if ($data->is_active == 1)
                                            <span class="badge bg-success-lt">Aktif</span>
                                            @else
                                            <span class="badge bg-danger-lt">Tidak Aktif</span>
                                            @endif

                                        </td>
                                        <td class="text-center">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal-edit">
                                                <span class="badge bg-warning">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-edit" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
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
                                                @csrf
                                                <a href="#">
                                                    <span class="badge bg-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-trash" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M4 7l16 0"></path>
                                                            <path d="M10 11l0 6"></path>
                                                            <path d="M14 11l0 6"></path>
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                            </path>
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p></p>

                            {{ $users->links('vendor.pagination.bootstrap-5') }}

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
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap"
                                    placeholder="Nama Lengkap">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Kode User</label>
                                <input type="text" class="form-control" name="kode_user" id="kode_user"
                                    placeholder="ZFR">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="Username">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Wilayah</label>
                                <select class="form-control" name="wilayah" id="wilayah">
                                    <option value="">--Pilih Wilayah--</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Hak Akses</label>
                                <select class="form-control" name="hak_akses" id="hak_akses">
                                    <option value="">--Pilih Akses--</option>
                                    <option value="1">Super Admin</option>
                                    <option value="2">Direktur</option>
                                    <option value="3">Kabag Administrasi</option>
                                    <option value="4">Kabag Operasional</option>
                                    <option value="5">Kepala Kantor</option>
                                    <option value="6">Kasi Analis</option>
                                    <option value="7">Head Teller</option>
                                    <option value="8">Customer Service</option>
                                    <option value="9">Staff Analis</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status_user" id="status_user">
                                    <option value="">--Pilih Status--</option>
                                    <option value="1">Aktif</option>
                                    <option value="2">Tidak Aktif</option>
                                </select>
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

<div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap"
                                    placeholder="Nama Lengkap" value="Zulfadli Rizal">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Kode User</label>
                                <input type="text" class="form-control" name="kode_user" id="kode_user"
                                    placeholder="ZFR" value="ZFR">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="Username" value="zulfadli">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="********">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Wilayah</label>
                                <select class="form-control" name="wilayah" id="wilayah">
                                    <option value="Pusat">Pusat</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Hak Akses</label>
                                <select class="form-control" name="hak_akses" id="hak_akses">
                                    <option value="1">Super Admin</option>
                                    <option value="2">Direktur</option>
                                    <option value="3">Kabag Administrasi</option>
                                    <option value="4">Kabag Operasional</option>
                                    <option value="5">Kepala Kantor</option>
                                    <option value="6">Kasi Analis</option>
                                    <option value="7">Head Teller</option>
                                    <option value="8">Customer Service</option>
                                    <option value="9">Staff Analis</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status_user" id="status_user">
                                    <option value="1">Aktif</option>
                                    <option value="2">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Created At</label>
                                <input type="text" class="form-control" value="2023-07-14 07:56:39" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                    <a href="#" class="btn btn-warning ms-auto">Simpan</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection