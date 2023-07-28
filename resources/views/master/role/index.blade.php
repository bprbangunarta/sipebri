@extends('templates.app')
@section('title', 'Role Management')

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
                                    <div class="page-pretitle">
                                        Master
                                    </div>
                                    <h2 class="page-title">
                                        Role Management
                                    </h2>
                                </div>

                                @can('create role')
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <a href="#" class="btn btn-primary" id="btnCreateRole">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
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

                        @if (Session::get('warning'))
                        <div class="alert alert-warning">
                            {{ Session::get('warning')}}
                        </div>
                        @endif

                        <form action="{{ route('role.index') }}" method="GET">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Role Name"
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
                                        <th class="text-center">Role Name</th>
                                        <th class="text-center">Guard Name</th>
                                        <th class="text-center" width="20%">Created At</th>
                                        <th class="text-center" width="20%">Update At</th>
                                        <th class="text-center" width="5%">Ubah</th>
                                        <th class="text-center" width="5%">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration + $roles->firstItem() -1}}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->guard_name }}</td>
                                        <td class="text-center">{{ $role->created_at }}</td>
                                        <td class="text-center">{{ $role->updated_at }}</td>
                                        <td class="text-center">
                                            <a href="#" class="edit" role_id="{{ $role->id }}">
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
                                            <form action="/admin/role/{{ $role->id }}/delete" method="POST">
                                                @csrf
                                                <a href="#" class="delete" role_id="{{ $role->id }}">
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

                            {{ $roles->links('vendor.pagination.bootstrap-5') }}

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal modal-blur fade" id="modalCreateRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Role</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>

            <form action="{{ route('role.create') }}" method="POST" id="formCreateRole">
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <label class="form-label">Role Name</label>
                            <div class="input-icon mb-3">
                                <input type="text" class="form-control @error('name') 
                                is-invalid
                                @enderror" name="name" id="name" value="{{ old('name') }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <label class="form-label">Guard Name</label>
                            <div class="input-icon mb-3">
                                <input type="text" class="form-control @error('guard_name') 
                                is-invalid
                                @enderror" name="guard_name" id="guard_name" value="{{ old('guard_name') }}">
                                @error('guard_name')
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

<div class="modal modal-blur fade" id="modalEditRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div id="loadeditform"></div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modalDeleteRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="modal-status bg-danger"></div>
            <div id="loaddeleteform"></div>

        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnCreateRole").click(function() {
            $("#modalCreateRole").modal("show");
        });

        $(".edit").click(function() {
            var role_id = $(this).attr('role_id');
            $.ajax({
                type: 'POST',
                url: '/admin/role/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token(); }}",
                    role_id: role_id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                }
            });
            $("#modalEditRole").modal("show");
        });

        $(".delete").click(function() {
            var role_id = $(this).attr('role_id');
            $.ajax({
                type: 'POST',
                url: '/admin/role/delete',
                cache: false,
                data: {
                    _token: "{{ csrf_token(); }}",
                    role_id: role_id
                },
                success: function(respond) {
                    $("#loaddeleteform").html(respond);
                }
            });
            $("#modalDeleteRole").modal("show");
        });

        $("#formCreateRole").submit(function() {
            var name       = $("#name").val();
            var guard_name = $("#guard_name").val();

            // if (name == "") {
            //     $("#name").focus();
            //     return false;
            // }
        });

    });
</script>
@endpush