@extends('theme.app')
@section('title', 'Data Siap Realisasi')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>LAPORAN</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-laptop"></i> Dashboard</a></li>
                <li>Laporan</li>
                <li class="active">Siap Realisasi</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">REKAP SIAP REALISASI</h3>

                            <div class="box-tools">
                                <form action="{{ route('laporan.post.siap_realisasi') }}" method="POST">
                                    @csrf
                                    <div class="input-group input-group-sm hidden-xs pull-right" style="width: 335px;">
                                        <input type="date" class="form-control pull-left" style="width: 150px;"
                                            name="tgl1" id="tgl1" value="">

                                        <input type="date" class="form-control pull-right" style="width: 150px;"
                                            name="tgl2" id="tgl2" value="">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-primary"><i
                                                    class="fa fa-filter"></i></button>
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
                                        <th class="text-center" width="15%">INFORMASI</th>
                                        <th class="text-center" width="13%">WIL</th>
                                        <th class="text-center" width="40%">RENCANA</th>
                                        <th class="text-center" width="20%">KETERANGAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center" style="vertical-align: middle;">{{ $no }}</td>
                                            <td style="vertical-align: middle;">
                                                <b>Tgl  : </b> {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }} <br>
                                                <b>Kode : </b> {{ $item->kode_pengajuan }} <br>
                                                <b>Nama : </b> {{ $item->nama_nasabah }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <b>Wilayah : </b> {{ $item->wilayah }} <br>
                                                <b>Plafon : </b> {{ number_format($item->plafon, 0, ',', '.') }} <br>
                                                <b>Surveyor : </b> {{ $item->surveyor }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $item->keterangan }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $item->rencana_realisasi }}
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="8">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm pull-left"><i class="fa fa-download"></i>&nbsp; Export Data</button>

                            {{ $data->links('vendor.pagination.adminlte') }}
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
                <form action="{{ route('export.siap-realisasi') }}" method="POST">
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
