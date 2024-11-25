@extends('theme.app')
@section('title', 'Terima Berkas')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-inbox" aria-hidden="true"></i>
                            <h3 class="box-title">TERIMA BERKAS</h3>

                            <div class="box-tools">
                                <form action="{{ route('terima.berkas.index') }}" method="GET">
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
                                        <th class="text-center" width="8%">TGL KIRIM</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="42%">ALAMAT</th>
                                        <th class="text-center" width="8%">PRODUK</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="8%">PENGIRIM</th>
                                        <th class="text-center" width="8%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        @if (empty($item->tgl_terima))
                                            <tr>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    {{ $loop->iteration + $data->firstItem() - 1 }}
                                                </td>

                                                <td class="text-center" style="vertical-align: middle;">
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                                </td>

                                                <td class="text-center" style="vertical-align: middle;">
                                                    {{ $item->kode_pengajuan }}</td>

                                                <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                                <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    {{ $item->produk_kode }}
                                                </td>
                                                <td class="text-right" style="vertical-align: middle;">
                                                    {{ number_format($item->plafon, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    {{ $item->nama_user }}
                                                </td>
                                                <td>
                                                    <a data-toggle="modal" data-target="#terimaBerkas"
                                                        data-kode="{{ $item->kode_pengajuan }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Terima Berkas"
                                                        style="cursor: pointer;">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
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

    <div class="modal fade" id="terimaBerkas">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TERIMA BERKAS</h4>
                </div>
                <form action="{{ route('terima.berkas.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input class="form-control text-uppercase" type="text" name="kode_pengajuan"
                                        id="kode" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nama"
                                        type="text" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <p class="form-control" style="height: 55px;">
                                        Pastikan berkas sudah sesuai, baru lakukan terima berkas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">TERIMA</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            $('#terimaBerkas').on('show.bs.modal', function(e) {
                var button = $(e.relatedTarget);
                var kode = button.data('kode');

                $.ajax({
                    url: "/terima/berkas/get",
                    type: "get",
                    dataType: "json",
                    cache: false,
                    data: {
                        data: kode
                    },
                    success: function(response) {
                        $('#kode').val(response.kode_pengajuan)
                        $('#nama').val(response.nama_nasabah)

                    },
                    error: function(xhr, status, error) {
                        // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                        console.error("Error:", xhr.responseText);
                    },
                });
            })
        })
    </script>
@endpush
