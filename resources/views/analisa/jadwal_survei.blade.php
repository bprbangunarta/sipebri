@extends('analisa.menu_penjadwalan')
@section('title', 'Jadwal Survei')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-header with-border">
                <i class="fa fa-calendar"></i>
                <h3 class="box-title">JADWAL SURVEI - {{ $tgl }}</h3>

                <div class="box-tools">
                    <form action="{{ route('jadwal.survei') }}" method="GET">
                        <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                            <input type="text" class="form-control text-uppercase pull-right"
                                style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                                value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah/ Produk">

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
                <table class="table table-bordered" style="font-size:12px;">
                    <thead>
                        <tr class="bg-blue">
                            <th class="text-center" width="3%">NO</th>
                            <th class="text-center" width="8%">TGL DAFTAR</th>
                            <th class="text-center" width="8%">KODE</th>
                            <th class="text-center" width="16%">NAMA NASABAH</th>
                            <th class="text-center" width="42%">ALAMAT</th>
                            <th class="text-center">PRODUK</th>
                            <th class="text-center">KANTOR</th>
                            <th class="text-center">PLAFON</th>
                            <th class="text-center">SURVEYOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $loop->iteration + $data->firstItem() - 1 }}
                                </td>

                                <td class="text-center" style="vertical-align: middle;">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                </td>

                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $item->kode_pengajuan }}</td>

                                <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $item->produk_kode }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ number_format($item->plafon, '0', ',', '.') }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">{{ $item->nama_user }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="11">TIDAK ADA DATA</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <div class="box-footer clearfix">
                <div class="pull-left hidden-xs">
                    <button type="button" class="btn-circle btn-sm bg-green" title="Informasi" data-toggle="modal"
                        data-target="#modalInfo" style="cursor: pointer; border:none;">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        &nbsp;
                        Export
                    </button>

                    &nbsp;
                    <button class="btn btn-default btn-sm">
                        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                        entries
                    </button>
                </div>

                {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
            </div>

        </div>

    @endsection
