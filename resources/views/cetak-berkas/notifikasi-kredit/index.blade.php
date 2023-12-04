@extends('theme.app')
@section('title', 'Cetak Notifikasi')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK NOTIFIKASI KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('cetak.notifikasi.index') }}" method="GET">
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

                        <div class="box-body">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="7%">NOTIFIKASI</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="35%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="5%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }} <br>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (is_null($item->no_notifikasi))
                                                    <span class="label label-danger" style="font-size: 10px;">&nbsp; BELUM
                                                        DITURUNKAN &nbsp;</span>
                                                @else
                                                    {{ $item->no_notifikasi }}
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->wilayah }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (is_null($item->no_notifikasi))
                                                    <a data-toggle="modal" data-target="#modal-danger" class="btn-circle btn-sm bg-red">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('cetak.notifikasi_kredit', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        target="_blank" class="btn-circle btn-sm bg-blue">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>

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

    <div class="modal fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">NOTIFIKASI KREDIT</h4>
                </div>
                
                <div class="modal-body">
                    <p>Mohon maaf cetak notifikasi kredit tidak bisa dilakukan karena notifikasi belum diturunkan oleh komite kredit. Silahkan hubungi tim analis. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn btn-danger" style="width: 100%;" data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>
@endsection
