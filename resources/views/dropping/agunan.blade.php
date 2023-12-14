@extends('theme.app')
@section('title', 'Dropping Agunan Kredit')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-download"></i>
                            <h3 class="box-title">DROPPING AGUNAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('dropping.agunan') }}" method="GET">
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
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center">TANGGAL</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">NO. SPK</th>
                                        <th class="text-center">JENIS AGUNAN</th>
                                        <th class="text-center">NO. DOKUMEN</th>
                                        <th class="text-center">LOKASI</th>
                                        
                                        @can('dropping agunan')
                                        <th class="text-center">AKSI</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_daftar)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td class="text-center">{{ $item->no_spk }}</td>
                                            <td>{{ $item->jenis_agunan }}</td>
                                            <td class="text-center">{{ $item->no_dokumen }}</td>
                                            <td>{{ $item->lokasi }}</td>

                                            @can('dropping agunan')
                                            <td class="text-center">
                                                <a data-toggle="modal" data-target="#hapus"
                                                    class="btn-circle btn-sm bg-red" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                            @endcan
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="15">TIDAK ADA DATA</td>
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

    <div class="modal fade" id="hapus">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">HAPUS DATA AGUNAN</h4>
                </div>

                <div class="modal-body">
                    <p>Fitur hapus data agunan yang sudah siap untuk dropping sedang dalam pengembangan, mohon tunggu sebentar. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn bg-red" style="width: 100%;"
                        data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>
@endsection
