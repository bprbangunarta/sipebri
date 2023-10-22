@extends('theme.app')
@section('title', 'Penjadwalan Survey')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">PENJADWALAN SURVEY</h3>

                            <div class="box-tools">
                                <form {{ route('analisa.penjadwalan') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ Request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
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
                                        <th class="text-center">PENGAJUAN</th>
                                        <th class="text-center" width="35%">ALAMAT</th>
                                        <th class="text-center" width="15%">JADWAL SURVEY</th>
                                        <th class="text-center" width="8%">KANTOR</th>
                                        <th class="text-center" width="8%">PETUGAS</th>
                                        <th class="text-center" width="8%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE : </b>{{ $item->kode_pengajuan }} <br>
                                                <b>NAMA: </b>{{ $item->nama_nasabah }} <br>
                                                <b>TANGGAL : </b> 2023-12-12
                                            </td>

                                            <td style="vertical-align: middle;">
                                                {{ $item->alamat_ktp }}<br>
                                                <b>DESA</b>: CIGUGUR | <b>KECAMATAN</b>: PUSAKAJAYA
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>Tanggal : </b>@if (is_null($item->tgl_survei))- @else {{ $item->tgl_survei }} @endif <br>
                                                <b>Jadul 1 : </b>@if (is_null($item->tgl_jadul_1)) - @else {{ $item->tgl_jadul_1 }} @endif <br>
                                                <b>Jadul 2 : </b>@if (is_null($item->tgl_jadul_2)) - @else {{ $item->tgl_jadul_2 }} @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_kantor }}</td>

                                            <td class="text-center" style="vertical-align: middle;">{{ $item->code_user }}</td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="#" class="btn-circle btn-sm btn-primary" title="Info Nasabah">
                                                    <i class="fa fa-user"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#modal-edit" data-id="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm btn-warning" title="Jadwal Survey">
                                                    <i class="fa fa-calendar"></i>
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
                            {{ $data->links('vendor.pagination.adminlte') }}
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
                                    <input class="form-control" type="text" name="name" id="name"
                                        placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>EMAIL ADDRESS</label>
                                    <input class="form-control" type="email" name="email" id="email"
                                        placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>USERNAME</label>
                                    <input class="form-control" type="text" name="username" id="username"
                                        placeholder="ENTRI" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE USER</label>
                                    <input class="form-control" type="text" name="code_user" id="code_user"
                                        minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE SURVEYOR</label>
                                    <input class="form-control" type="text" name="kode_surveyor" id="kode_surveyor"
                                        minlength="3" maxlength="3" placeholder="ENTRI">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KODE KOLEKTOR</label>
                                    <input class="form-control" type="text" name="kode_kolektor" id="kode_kolektor"
                                        minlength="3" maxlength="3" placeholder="ENTRI">
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
