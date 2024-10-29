@extends('templates.app')
@section('title', 'Give Permission To')

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
                                            Give Permission To
                                        </h2>
                                    </div>

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="#" class="btn btn-primary" id="btnCreateRole">
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
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                            <form action="{{ route('role.index') }}" method="GET">
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
                                            <th class="text-center" width="5%">ID</th>
                                            <th class="text-center" width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permission as $data)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $loop->iteration + $permission->firstItem() - 1 }}
                                                </td>
                                                <td style="text-transform: capitalize;">{{ $data->name }}</td>
                                                <td class="text-center">{{ $data->id }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="give-permission" id="give-permission"
                                                        class="give-permission" data-id1="{{ $data->name }}"
                                                        data-id2="{{ $datas }}"
                                                        @foreach ($role as $item)
                                                            @if ($item->permission_id == $data->id && $item->role_id == $datas) checked @break @endif @endforeach>
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

    <script type="text/javascript">
        $(document).ready(function() {

            $('.give-permission').change(function() {
                if ($(this).is(':checked')) {
                    const value1 = $(this).data("id1");
                    const value2 = $(this).data("id2");

                    $.ajax({
                        url: "{{ route('permission.postpermission') }}",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        data: {
                            id1: value1,
                            id2: value2,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Panggil SweetAlert setelah permintaan AJAX berhasil
                            var da = JSON.stringify(response);
                            var data = JSON.parse(da);
                            var convert = data.split(' ').map(function(word) {
                                return word.charAt(0).toUpperCase() + word.slice(1);
                            }).join(' ');
                            var pesan = '<span style="color: blue;">' + convert +
                                '</span> berhasil diterapkan.';

                            Swal.fire({
                                position: 'top-end',
                                title: 'Success!',
                                html: pesan,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1100,
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                        }
                    });
                } else {
                    const value1 = $(this).data("id1");
                    const value2 = $(this).data("id2");

                    $.ajax({
                        url: "{{ route('permission.destroypermission') }}",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        data: {
                            id1: value1,
                            id2: value2,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Panggil SweetAlert setelah permintaan AJAX berhasil
                            var da = JSON.stringify(response);
                            var data = JSON.parse(da);

                            var convert = data.split(' ').map(function(word) {
                                return word.charAt(0).toUpperCase() + word.slice(1);
                            }).join(' ');
                            var pesan = '<span style="color: blue;">' + convert +
                                '</span> berhasil tidak dipasang.';

                            Swal.fire({
                                position: 'top-end',
                                title: 'Success!',
                                html: pesan,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1100,
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                        }
                    });
                }
            });
        });
    </script>

@endsection
