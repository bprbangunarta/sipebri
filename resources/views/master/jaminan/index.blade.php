@extends('theme.app')
@section('title', 'Data Jaminan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                            <h3 class="box-title">DATA JAMINAN</h3>

                            <div class="box-tools">
                                <form action="{{ route('admin.jaminan.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ No Identitas">

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
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">ATAS NAMA</th>
                                        <th class="text-center">NO DOKUMEN</th>
                                        <th class="text-center">LOKASI</th>
                                        <th class="text-center">CATATAN</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $item->pengajuan_kode }}</td>
                                            <td>{{ $item->atas_nama }}</td>
                                            <td>{{ $item->no_dokumen }}</td>
                                            <td>{{ $item->lokasi }}</td>
                                            <td>{{ $item->catatan }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.jaminan.edit', ['id' => $item->id]) }}"
                                                    class="btn-circle btn-sm bg-yellow">
                                                    <i class="fa fa-edit"></i>
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
