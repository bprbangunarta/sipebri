@extends('theme.app')
@section('title', 'Pelaksanaan Survei')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">PELAKSANAAN SURVEI - {{ $tgl }}</h3>

                            <div class="box-tools">
                                <form action="{{ route('pelaksanaan.survei') }}" method="GET">
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
                                        <th class="text-center">SURVEYOR</th>
                                        <th class="text-center" width="25%">KETERANGAN</th>
                                        <th class="text-center" width="25%">JUMLAH SURVEI</th>
                                        <th class="text-center" width="45%">STATUS</th>
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
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->nama_user }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->catatan }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->jumlah_survei }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->ket == 'Progress')
                                                    <span class="badge bg-blue">Progress</span>
                                                @elseif ($item->ket == 'Pending')
                                                    <span class="badge bg-success"
                                                        style="background: rgb(245, 51, 51)">Pending</span>
                                                @elseif ($item->ket == 'Success')
                                                    <span class="badge bg-success"
                                                        style="background: rgb(3, 209, 3)">Success</span>
                                                @else
                                                    <span class="badge bg-success"
                                                        style="background: rgb(103, 103, 103)">Not
                                                        Status</span>
                                                @endif

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