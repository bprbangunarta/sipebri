@extends('theme.app')
@section('title', 'Add Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">ADD RESCHEDULLING</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.index.analisa') }}" method="GET">
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
                                        <th class="text-center">TANGGAL</th>
                                        <th class="text-center">KODE PENGAJUAN</th>
                                        <th class="text-center">KODE RSC</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center" width="7%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal_rsc)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td class="text-center">{{ $item->kode_rsc }}</td>
                                            <td>{{ strtoupper($item->nama_nasabah) }}</td>
                                            @if (is_null($item->alamat_ktp))
                                                <td class="text-center">-</td>
                                            @else
                                                <td class="text-uppercase">
                                                    {{ $item->alamat_ktp }}
                                                </td>
                                            @endif

                                            <td class="text-center">
                                                {{ $item->kantor_kode }}
                                            </td>

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right">
                                                {{ $item->plafon }}
                                            </td>
                                            <td class="text-center" style="text-align: right;">
                                                @if (
                                                    $item->status == 'Proses Analisa' ||
                                                        $item->status == 'Proses Persetujuan' ||
                                                        $item->status == 'Naik Kasi' ||
                                                        $item->status == 'Komite I' ||
                                                        $item->status == 'Komite II' ||
                                                        $item->status == 'Notifikasi')
                                                    <a href="{{ route('rsc.data.kredit', ['kode' => $item->kode, 'rsc' => $item->rsc, 'status_rsc' => $item->status_rsc]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Lengkapi RSC">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a data-toggle="modal" data-target="#jadwal-ulang"
                                                        data-pengajuan="{{ $item->kode_pengajuan }}"
                                                        data-rsc='{{ $item->rsc }}'
                                                        data-status='{{ $item->status_rsc }}'
                                                        class="btn-circle btn-sm bg-blue" title="Jadwal Ulang"
                                                        style="cursor: pointer;">
                                                        <i class="fa fa-history"></i>
                                                    </a>
                                                @endif

                                                &nbsp;

                                                <form action="{{ route('rsc.delete.rsc', ['rsc' => $item->rsc]) }}"
                                                    method="POST" style="display:inline;">
                                                    @method('delete')
                                                    @csrf
                                                    <a href="#" class="btn-circle btn-sm bg-red confirmdelete"
                                                        title="Hapus" style="cursor: pointer;">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="8">TIDAK ADA DATA</td>
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

    <div class="modal fade" id="jadwal-ulang">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">JADWAL ULANG</h4>
                </div>
                <form action="{{ route('rsc.simpan.jadul') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
                                    <input type="text" id="rsc" name="rsc" hidden>
                                    <input class="form-control text-uppercase" type="text" value="123456789S"
                                        name="kode_pengajuan" id="kd_pengajuan" readonly>
                                    <input type="text" value="" name="tgl_survei" id="tgl_survei" hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" value="ZULFADLI RIZAL" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <textarea class="form-control text-uppercase" name="keterangan" id=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-blue">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#jadwal-ulang").on("show.bs.modal", function(event) {
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var pengajuan = button.data("pengajuan"); // Ambil data-id dari tombol
                var status = button.data("status"); // Ambil data-id dari tombol

                if (status == 'EKS') {
                    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
                    $.ajax({
                        url: "/themes/rsc/permohonan/data_jadul/eks/" + pengajuan,
                        type: "GET",
                        dataType: "json",
                        cache: false,
                        success: function(response) {

                            $("#id").val(response.id);
                            $("#kd_pengajuan").val(response.pengajuan_kode);
                            $("#tgl_survei").val(response.tgl_survei);
                            $("#nm_nasabah").val(response.nama_nasabah);
                            $('#rsc').val(response.kode_rsc)
                        },
                        error: function(xhr, status, error) {
                            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                            console.error("Error:", xhr.responseText);
                        },
                    });
                } else {
                    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
                    $.ajax({
                        url: "/themes/permohonan/data_jadul/" + pengajuan,
                        type: "GET",
                        dataType: "json",
                        cache: false,
                        success: function(response) {

                            $("#id").val(response.id);
                            $("#kd_pengajuan").val(response.kode_pengajuan);
                            $("#tgl_survei").val(response.tgl_survei);
                            $("#nm_nasabah").val(response.nama_nasabah);
                            $('#rsc').val(response.kode_rsc)
                        },
                        error: function(xhr, status, error) {
                            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                            console.error("Error:", xhr.responseText);
                        },
                    });
                }

            });
        });
    </script>
@endpush
