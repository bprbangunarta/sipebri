@extends('theme.app')
@section('title', 'Cetak Pengajuan Kredit')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">CETAK PENGAJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('cetak.pengajuan.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right" style="width: 170px;" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
                                                <i class="fa fa-search"></i>
                                            </button>
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
                                        <th class="text-center" width="7%">TANGGAL</th>
                                        <th class="text-center" width="7%">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="41%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="5%">INPUT</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="5%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center" style="vertical-align: middle;">{{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                               {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }} <br>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->input_user }}</td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('cetak.pengajuan', ['pengajuan' => $item->kd]) }}" target="_blank" class="btn-circle btn-sm bg-blue">
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
                            <div class="pull-left">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
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
