@extends('theme.app')
@section('title', 'Rekap Notifikasi')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>LAPORAN</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-laptop"></i> Dashboard</a></li>
                <li>Laporan</li>
                <li class="active">Rekap Notifikasi</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">REKAP NOTIFIKASI KREDIT</h3>

                            <div class="box-tools">
                                <form href="{{ route('laporan.notifikasi') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="10%">TANGGAL</th>
                                        <th class="text-center" width="10%">KODE</th>
                                        <th class="text-center" width="10%">NO. NOTIFIKASI</th>
                                        <th class="text-center">NAMA LENGKAP</th>
                                        <th class="text-center" width="10%">PLAFON</th>
                                        <th class="text-center">WILAYAH</th>
                                        <th class="text-center">SURVEYOR</th>
                                        <th class="text-center" width="5%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_notifikasi)->format('Y-m-d') }}</td>
                                        <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                        <td class="text-center">{{ $item->no_notifikasi }}</td>
                                        <td>{{ $item->nama_nasabah }}</td>
                                        <td class="text-right">{{ number_format($item->plafon, 0, ',', '.');}}</td>
                                        <td class="text-center">{{ $item->kode_kantor }}</td>
                                        <td>{{ $item->surveyor }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('cetak.notifikasi_kredit', ['pengajuan' => $item->kode_pengajuan]) }}" target="_blank"" class="btn-circle btn-sm btn-primary">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                    @empty
                                    <tr>
                                        <td class="text-center" colspan="9">TIDAK ADA DATA</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm pull-left"><i class="fa fa-download"></i>&nbsp; Export Data</button>

                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
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
                <form action="{{ route('export.realisasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <input type="text" name="alamat" id="alamat" hidden>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>MULAI DARI</label>
                                    <input type="date" class="form-control" name="tgl1" id="tgl1">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SAMPAI DENGAN</label>
                                    <input type="date" class="form-control" name="tgl2" id="tgl2">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-success">EXPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
