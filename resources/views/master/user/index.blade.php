@extends('theme.app')
@section('title', 'Data User')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA USER</h3>

                            <div class="box-tools">
                                <form {{ route('user.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name" value="{{ Request('name') }}" placeholder="Search">
                    
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <tr>
                                            <th class="text-center" width="3%">NO</th>
                                            <th class="text-center">NAMA LENGKAP</th>
                                            <th class="text-center">EMAIL</th>
                                            <th class="text-center">USERNAME</th>
                                            <th class="text-center">KODE</th>
                                            <th class="text-center">KANTOR</th>
                                            <th class="text-center">HAK AKSES</th>
                                            <th class="text-center" width="5%">AKTIF</th>
                                            <th class="text-center" width="12%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($users as $data)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->email }}</td>
                                            <td>{{ $data->username }}</td>
                                            <td class="text-center">{{ $data->code_user }}</td>
                                            <td class="text-center">{{ $data->kode_kantor }}</td>
                                            <td>
                                                @if (empty($data->position))
                                                    -
                                                @else
                                                    {{ $data->position }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($data->is_active == 1)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a data-toggle="modal" data-target="#modal-edit" data-id="{{ $data->code_user }}" class="btn-circle btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                &nbsp;
                                                <a href="#" class="btn-circle btn-sm btn-danger">
                                                    <i class="fa fa-key"></i>
                                                </a>

                                                &nbsp;
                                                <a href="#" class="btn-circle btn-sm btn-primary">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <button data-toggle="modal" data-target="#modal-tambah" class="btn btn-primary btn-sm pull-left">TAMBAH</button>

                            {{ $users->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAMBAH USER</h4>
                </div>
                <form action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NAMA LENGKAP</label>
                                    <input class="form-control" type="text" name="name" id="name" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>EMAIL ADDRESS</label>
                                    <input class="form-control" type="email" name="email" id="email" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>USERNAME</label>
                                    <input class="form-control" type="text" name="username" id="username" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KANTOR</label>
                                    <select class="form-control" name="kode_kantor" id="kode_kantor" required>
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $data)
                                            <option value="{{ $data->kode_kantor }}">{{ $data->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE USER</label>
                                    <input class="form-control" type="text" name="user_kode" id="user_kode" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE SURVEYOR</label>
                                    <input class="form-control" type="text" name="kode_surveyor" id="kode_surveyor" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE KOLEKTOR</label>
                                    <input class="form-control" type="text" name="kode_kolektor" id="kode_kolektor" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>IS ACTIVE?</label>
                                    <select class="form-control" name="is_active" id="is_active" required>
                                        <option value="1">AKTIF</option>
                                        <option value="0">TIDAK AKTIF</option>
                                    </select>
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

    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">EDIT USER</h4>
                </div>
                <form action="{{ route('user.update', ['user' => $users[0]->code_user]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NAMA LENGKAP</label>
                                    <input class="form-control" type="text" name="name" id="name" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>EMAIL ADDRESS</label>
                                    <input class="form-control" type="email" name="email" id="email" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>USERNAME</label>
                                    <input class="form-control" type="text" name="username" id="username" placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KANTOR</label>
                                    <select class="form-control" name="kode_kantor" id="kode_kantor" required>
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $data)
                                            <option value="{{ $data->kode_kantor }}">{{ $data->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE USER</label>
                                    <input class="form-control" type="text" name="user_kode" id="user_kode" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE SURVEYOR</label>
                                    <input class="form-control" type="text" name="kode_surveyor" id="kode_surveyor" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE KOLEKTOR</label>
                                    <input class="form-control" type="text" name="kode_kolektor" id="kode_kolektor" minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>IS ACTIVE?</label>
                                    <select class="form-control" name="is_active" id="is_active" required>
                                        <option value="1">AKTIF</option>
                                        <option value="0">TIDAK AKTIF</option>
                                    </select>
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
    <script src="{{ asset('assets/js/myscript/user.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/update.js') }}"></script>
@endpush
