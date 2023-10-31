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
                            <i class="fa fa-calendar"></i>
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
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">INFORMASI NASABAH</th>
                                        <th class="text-center"width="25%">PENGAJUAN</th>
                                        <th class="text-center" width="15%">JADWAL</th>
                                        <th class="text-center" width="10%">AKSI</th>
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
                                                <b>{{ $item->nama_nasabah }}</b> [ {{ $item->kategori }} ] <br>
                                                {{ $item->alamat_ktp }}<br>
                                                <b>DESA</b>: {{ $item->kelurahan }} | <b>KECAMATAN</b>: {{ $item->kecamatan }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE : </b>{{ $item->kode_pengajuan }} [ {{ $item->kode_kantor }} ] <br>
                                                <b>TANGGAL : </b> {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}<br>
                                                <b>PETUGAS : </b> {{ $item->name }} 
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>SURVEY : </b>
                                                @if (is_null($item->tgl_survei))
                                                    -
                                                @else
                                                    {{ $item->tgl_survei }}
                                                @endif
                                                <br>
                                                <b>JADUL 1 : </b>
                                                @if (is_null($item->tgl_jadul_1))
                                                    -
                                                @else
                                                    {{ $item->tgl_jadul_1 }}
                                                @endif
                                                <br>
                                                <b>JADUL 2 : </b>
                                                @if (is_null($item->tgl_jadul_2))
                                                    -
                                                @else
                                                    {{ $item->tgl_jadul_2 }}
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <span data-toggle="modal" data-target="#modal-penjadwalan" data-id="{{ $item->kode_pengajuan }}" class="btn bg-yellow" style="width: 120px;hight:100%;">Jadwalkan</span>
                                                <p style="margin-top:-5px;"></p>
                                                <span class="btn bg-blue" style="width: 120px;hight:100%;">Info Nasabah</span>
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

    <div class="modal fade" id="modal-penjadwalan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PENJADWALAN SURVEY</h4>
                </div>
                <form action="{{ route('analisa.updatepenjadwalan') }}" method="POST">
                    @method('put')
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <input type="text" name="alamat" id="alamat" hidden>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE PENGAJUAN</label>
                                    <input type="text" class="form-control" name="kode_pengajuan" id="kode_pengajuan"
                                        readonly>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>NAMA NASABAH</label>
                                    <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah"
                                        readonly>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>PETUGAS</label>
                                    <select class="form-control petugas" style="width: 100%;" name="kode_petugas"
                                        id="kode_petugas">
                                        <option value="">--PILIH--</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TGL. SURVEY</label>
                                    <input type="date" class="form-control" name="tgl_survei"
                                        id="datepicker-tanggal-survei" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>TGL. JADUL 1</label>
                                    <input type="date" class="form-control" name="tgl_jadul_1"
                                        id="datepicker-tanggal-survei1">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>TGL. JADUL 2</label>
                                    <input type="date" class="form-control" name="tgl_jadul_2"
                                        id="datepicker-tanggal-survei2">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:-10px;">
                                    <label>CATATAN</label>
                                    <div id="catatan"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-warning">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/penjadwalan.js') }}"></script>
    <script>
        //Initialize Select2 Elements
        $('.petugas').select2()
    </script>
@endpush
