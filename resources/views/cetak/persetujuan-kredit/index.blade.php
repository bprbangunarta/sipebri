@extends('theme.app')
@section('title', 'Cetak Persetujuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK PERSETUJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('persetujuan.kredit') }}" method="GET">
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
                        
                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="41%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="5%">SRV</th>
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
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                               {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }} <br>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_pengajuan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->wilayah }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->surveyor }}</td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('cetak.persetujuan_kredit', ['pengajuan' => $item->kd_pengajuan]) }}" target="_blank" class="btn-circle btn-sm bg-blue">
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
                            <div class="pull-left hidden-xs">
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
