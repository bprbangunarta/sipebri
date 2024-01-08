@extends('theme.app')
@section('title', 'Data Pendamping')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PENGAJUAN</h3>

                            <div class="box-tools">
                                <form action="{{ route('admin.pengajuan.index') }}" method="GET">
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
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">NO NIK</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center">PRODUK</th>
                                        <th class="text-center">METODE</th>
                                        <th class="text-center">JW</th>
                                        <th class="text-center">TRACKING</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">ON CURRENT</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->no_identitas }}</td>
                                            <td>Rp. {{ number_format($item->plafon, '0', '.', '') }}</td>
                                            <td class="text-center">{{ $item->produk_kode }}</td>
                                            <td>{{ $item->metode_rps }}</td>
                                            <td class="text-center">{{ $item->jangka_waktu }}</td>
                                            <td class="text-center">{{ $item->tracking }}</td>
                                            <td class="text-center">{{ $item->status }}</td>
                                            <td class="text-center">{{ $item->on_current }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.pengajuan.edit', ['kode' => $item->kode_pengajuan]) }}"
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
