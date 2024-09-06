@extends('theme.app')
@section('title', 'Laporan Pendaftaran RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">LAPORAN PENDAFTARAN RSC</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.pendaftaran') }}" method="GET">
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
                            <table class="table table-responsive table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center">TANGGAL</th>
                                        <th class="text-center">NO PENGAJUAN/LOAN</th>
                                        <th class="text-center">KODE RSC</th>
                                        <th class="text-center">NAMA DEBITUR</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">PDK</th>
                                        <th class="text-center">PLAFON RSC</th>
                                        <th class="text-center">JK</th>
                                        <th class="text-center">RATE</th>
                                        <th class="text-center">METODE RPS</th>
                                        <th class="text-center">INPUT USER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_rsc)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center">{{ $item->pengajuan_kode }}</td>
                                            <td class="text-center">{{ $item->kode_rsc }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td class="text-center">{{ $item->produk_kode }}</td>
                                            <td class="text-right">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->jangka_waktu }}</td>
                                            <td class="text-center">{{ $item->suku_bunga }}</td>
                                            <td class="text-center">{{ $item->metode_rps }}</td>
                                            <td>{{ $item->nama_user }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="17">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
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
@endsection
@push('myscript')
    <script>
        $('.resort').select2()
        $('.cgc').select2()
        $('.res').select2()
    </script>
@endpush
