@extends('theme.app')
@section('title', 'Data Role')
@yield('jquery')
@section('content')
    {{-- <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="container-xl">
                                <div class="row g-2 align-items-center">
                                    <div class="col">
                                        <div class="page-pretitle">
                                            Master
                                        </div>
                                        <h2 class="page-title">
                                            Data Permissions
                                        </h2>
                                    </div>

                                    @can('permission tambah')
                                        <div class="col-auto ms-auto d-print-none">
                                            <div class="btn-list">
                                                <a href="#" class="btn btn-primary" id="btnCreate">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 5l0 14"></path>
                                                        <path d="M5 12l14 0"></path>
                                                    </svg>
                                                    Create
                                                </a>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                            <form action="{{ route('permission.index') }}" method="GET">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Permission Name" value="{{ Request('name') }}">
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
                                            <th class="text-center">Permission Name</th>
                                            <th class="text-center" width="20%">Created At</th>
                                            <th class="text-center" width="20%">Update At</th>
                                            <th class="text-center" width="5%">Ubah</th>
                                            <th class="text-center" width="5%">Hapus</th>
                                        </tr>
                                    </thead>
                                    @foreach ($permission as $data)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $permission->firstItem() - 1 }}
                                            </td>
                                            <td>{{ $data->name }}</td>
                                            <td class="text-center">{{ $data->created_at }}</td>
                                            <td class="text-center">{{ $data->updated_at }}</td>

                                            <td class="text-center">
                                                <a href="#" class="edit" data-bs-toggle="modal"
                                                    data-bs-target="#modaledit" data-id="{{ $data->id }}"
                                                    title="Edit Permission">
                                                    <span class="badge bg-warning">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-edit" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
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
                                                <form action="/admin/permission/{{ $data->id }}/delete" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <button style="border: none; background: transparent;"
                                                        class="confirmdelete" class="delete"
                                                        permission_id="{{ $data->id }}">
                                                        <span class="badge bg-danger">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-trash" width="24"
                                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path d="M4 7l16 0"></path>
                                                                <path d="M10 11l0 6"></path>
                                                                <path d="M14 11l0 6"></path>
                                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                </path>
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
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

                                {{ $permission->links('vendor.pagination.bootstrap-5') }}

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Permission</h5>
                </div>

                <form action="{{ route('permission.create') }}" method="POST" id="formCreate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <label class="form-label">Permission Name</label>
                                <div class="input-icon mb-3">
                                    <input type="text"
                                        class="form-control @error('name') 
                              is-invalid
                              @enderror"
                                        name="name" id="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-primary ms-auto">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modaledit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Permission</h5>
                </div>

                @foreach ($permission as $data)
                    <form action="{{ route('permission.update', ['id' => $data->id]) }}" method="POST" id="formCreate">
                @endforeach
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <label class="form-label">Permission Name</label>
                            <div class="input-icon mb-3">

                                <input type="text"
                                    class="form-control @error('name') 
                              is-invalid
                              @enderror"
                                    name="name" id="namepermission" value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary ms-auto">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PERMISSION</h3>

                            <div class="box-tools">
                                <form action="{{ route('admin.permission.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama Permission">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">No</th>
                                        <th class="text-center">Permission Name</th>
                                        <th class="text-center" width="20%">Created At</th>
                                        <th class="text-center" width="20%">Update At</th>
                                        <th class="text-center" width="5%">Ubah</th>
                                        <th class="text-center" width="5%">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permission as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $permission->firstItem() - 1 }}
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">{{ $item->created_at }}</td>
                                            <td class="text-center">{{ $item->updated_at }}</td>
                                            <td class="text-center">
                                                <a data-bs-toggle="modal" data-bs-target="#modal-tambah"
                                                    data-id="{{ $item->id }}" title="Edit Permission"
                                                    class="btn-circle btn-sm bg-yellow">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <form action="/admin/permission/{{ $item->id }}/delete" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <button style="border: none; background:red;" class="confirmdelete"
                                                        class="delete" permission_id="{{ $item->id }}">
                                                        <i class="fa fa-trash" style="font-size: 12pt; color:white;"
                                                            title="Delete Permission"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modalCreate" class="btn bg-blue btn-sm">
                                    <i class="fa fa-plus"></i>&nbsp; TAMBAH
                                </button>
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $permission->firstItem() }} to {{ $permission->lastItem() }} of
                                    {{ $permission->total() }}
                                    entries
                                </button>
                            </div>

                            {{ $permission->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- <div class="modal modal-blur fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Permission</h5>
                </div>

                <form action="{{ route('permission.create') }}" method="POST" id="formCreate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <label class="form-label">Permission Name</label>
                                <div class="input-icon mb-3">
                                    <input type="text"
                                        class="form-control @error('name') 
                              is-invalid
                              @enderror"
                                        name="name" id="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-primary ms-auto">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAMBAH RESCHEDULLING</h4>
                </div>
                <form action="{{ route('rsc.tambah.rsc') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <label class="form-label">Permission Name</label>
                                <div class="input-icon mb-3">
                                    <input type="text"
                                        class="form-control @error('name') 
                              is-invalid
                              @enderror"
                                        name="name" id="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/permission.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
    <script>
        $(function() {
            $("#btnCreate").click(function() {
                $("#modalCreate").modal("show");
            });

        });
    </script>
@endpush
