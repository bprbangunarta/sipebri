@extends('theme.app')
@section('title', 'Laporan Data Berkas')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">LAPORAN DATA BERKAS</h3>

                            <div class="box-tools">
                                <form action="{{ route('laporan.data.berkas') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

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
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">KODE PENGAJUAN</th>
                                        <th class="text-center">NAMA</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">PDK</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center">DARI</th>
                                        <th class="text-center">KE</th>
                                        <th class="text-center">PENGIRIM</th>
                                        <th class="text-center">PENERIMA</th>
                                        <th class="text-center">TGL KIRIM</th>
                                        <th class="text-center">TGL TERIMA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-center">{{ $item->produk_kode }}</td>
                                            <td class="text-center">{{ number_format($item->plafon, '0', ',', '.') }}</td>
                                            <td class="text-center">{{ $item->dari_kantor }}</td>
                                            <td class="text-center">{{ $item->ke_kantor }}</td>
                                            <td class="text-center">{{ $item->user_pengirim }}</td>
                                            <td class="text-center">{{ $item->user_penerima }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_kirim)->format('d-m-Y') }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_terima)->format('d-m-Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="15">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i>&nbsp; Export Data
                                </button>

                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>
                            </div>

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-export">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">EXPORT DATA</h4>
                </div>
                <form action="{{ route('export.data.berkas') }}" method="get">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TANGGAL KIRIM</label>
                                    <input type="date" class="form-control" name="tgl_kirim" id=""
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group">
                                    <label>TANGGAL TERIMA</label>
                                    <input type="date" class="form-control" name="tgl_terima" id=""
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>DARI KANTOR</label>
                                    <select class="form-control kantor" name="dari_kantor" id="kantor"
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $item)
                                            <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>PRODUK</label>
                                    <select class="form-control produk" name="kode_produk" id="produk"
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->kode_produk }}">{{ $item->kode_produk }} -
                                                {{ $item->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SAMPAI DENGAN</label>
                                    <input type="date" class="form-control" name="tgl_kirim_sampai" id=""
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group">
                                    <label>SAMPAI DENGAN</label>
                                    <input type="date" class="form-control" name="tgl_terima_sampai" id=""
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KE KANTOR</label>
                                    <select class="form-control kekantor" name="ke_kantor" id="kekantor"
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $item)
                                            <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="margin-top: -10px;">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                            <button type="submit" class="btn btn-success">EXPORT</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('myscript')
    <script>
        $('#produk').select2()
        $('#kantor').select2()
        $('#kekantor').select2()
    </script>
@endpush
