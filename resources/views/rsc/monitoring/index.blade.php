@extends('theme.app')
@section('title', 'Monitoring RSC Staff Analis')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-cogs"></i>
                            <h3 class="box-title">MONITORING RSC STAFF ANALIS</h3>

                            <div class="box-tools">
                                <form action="{{ route('monitoring.rsc.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama">

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
                            <table class="table table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center" width="15%">PETUGAS SURVEI</th>
                                        <th class="text-center" width="7%">BELUM SURVEI</th>
                                        <th class="text-center" width="7%">PROSES ANALISA</th>
                                        <th class="text-center" width="7%">NAIK KASI</th>
                                        <th class="text-center" width="7%">BATAL </th>
                                        <th class="text-center" width="7%">TOLAK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $item->nama_user }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">

                                                <a
                                                    href="{{ route('monitoring.rsc.detail', ['name' => $item->nama_user, 'user' => $item->code_user, 'status' => 'Proses Survei']) }}">{{ $item->total_survei }}</a>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a
                                                    href="{{ route('monitoring.rsc.detail', ['name' => $item->nama_user, 'user' => $item->code_user, 'status' => 'Proses Analisa']) }}">{{ $item->total_analisa }}</a>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a
                                                    href="{{ route('monitoring.rsc.detail', ['name' => $item->nama_user, 'user' => $item->code_user, 'status' => 'Naik Kasi']) }}">{{ $item->total_naik_kasi }}</a>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;"><a
                                                    href="{{ route('monitoring.rsc.detail.status', ['name' => $item->nama_user, 'user' => $item->code_user, 'trc' => 'Dibatalkan']) }}">{{ $item->total_batal }}</a>
                                            <td class="text-center" style="vertical-align: middle;"><a
                                                    href="{{ route('monitoring.rsc.detail.status', ['name' => $item->nama_user, 'user' => $item->code_user, 'trc' => 'Ditolak']) }}">{{ $item->total_tolak }}</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="9">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
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
@endsection
