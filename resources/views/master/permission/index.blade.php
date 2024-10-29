@extends('theme.app')
@section('title', 'Data Permission')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PERMISSION</h3>

                            <div class="box-tools">
                                <form action="{{ route('permission.index') }}" method="GET">
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
                                                {{-- <form action="/admin/permission/{{ $item->id }}/delete" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <button style="border: none; background:red;" class="confirmdelete"
                                                        class="delete" permission_id="{{ $item->id }}">
                                                        <i class="fa fa-trash" style="font-size: 12pt; color:white;"
                                                            title="Delete Permission"></i>
                                                    </button>
                                                </form> --}}
                                                <a data-bs-toggle="modal" data-bs-target="#modal-tambah"
                                                    data-id="{{ $item->id }}" title="Edit Permission"
                                                    class="btn-circle btn-sm bg-yellow">
                                                    <i class="fa fa-trash" style="font-size: 12pt; color:white;"
                                                        title="Delete Permission"></i>
                                                </a>
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
                                <button data-toggle="modal" data-target="#modal-tambah" class="btn bg-blue btn-sm">
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
                    <h4 class="modal-title">TAMBAH PERMISSION</h4>
                </div>
                <form action="" method="POST">
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
