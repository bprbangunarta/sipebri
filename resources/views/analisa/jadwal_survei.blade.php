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
                            <th class="text-center" width="8%">TGL SURVEI</th>
                            <th class="text-center" width="8%">KODE</th>
                            <th class="text-center" width="16%">NAMA NASABAH</th>
                            <th class="text-center" width="42%">ALAMAT</th>
                            <th class="text-center">PRODUK</th>
                            <th class="text-center">KANTOR</th>
                            <th class="text-center">PLAFON</th>
                            <th class="text-center">SURVEYOR</th>
                            @if (Auth::user()->roles[0]->name == 'Kasi Analis')
                                <th class="text-center">AKSI</th>
                            @endif
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
                                    {{ $item->tgl_jadwal_survei }}
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
                                @if (Auth::user()->roles[0]->name == 'Kasi Analis')
                                    <td class="text-center" style="vertical-align: middle;">
                                        @if (
                                            $item->tgl_survei != now()->format('d-m-Y') ||
                                                $item->tgl_jadul_1 != now()->format('d-m-Y') ||
                                                $item->tgl_jadul_2 != now()->format('d-m-Y'))
                                            <a data-toggle="modal" data-target="#ubahPenjadwalan"
                                                data-id="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-yellow"
                                                title="Ubah Penjadwalan" style="cursor: pointer">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @else
                                            <a class="btn-circle btn-sm bg-grey" title="Ubah Penjadwalan"
                                                style="cursor: pointer">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
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
                    <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        &nbsp;
                        Export Data
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

        <div class="modal fade" id="modal-export">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">EXPORT DATA</h4>
                    </div>
                    <form action="{{ route('export.jadwal.survei') }}" method="get">
                        @csrf
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>TANGGAL SURVEI</label>
                                        <input type="date" class="form-control" name="tgl_survei" id=""
                                            style="margin-top:-5px;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SAMPAI DENGAN</label>
                                        <input type="date" class="form-control" name="tgl_survei_sampai"
                                            id="" style="margin-top:-5px;">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="margin-top: -10px;">
                                <button type="button" class="btn btn-default pull-left"
                                    data-dismiss="modal">BATAL</button>
                                <button type="submit" class="btn btn-success">EXPORT</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ubahPenjadwalan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">FORM PERUBAHAN PETUGAS SURVEI</h4>
                </div>
                <form action="{{ route('update.petugas') }}" method="POST">
                    @method('POST')
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE PENGAJUAN</label>
                                    <input type="text" class="form-control" name="kode_pengajuan" id="kode_pengajuan"
                                        readonly>
                                </div>

                                <div class="form-group" style="margin-top: 10px;">
                                    <label>NAMA SURVEYOR</label>
                                    <select class="form-control petugas" style="width: 100%;" name="kode_petugas"
                                        id="kode_petugas">
                                        <option value="">--PILIH--</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NAMA NASABAH</label>
                                    <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah"
                                        readonly>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer" style="margin-top: 10px;">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                            <button type="submit" class="btn btn-warning">SIMPAN</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('myscript')
    <script>
        $(document).ready(function() {
            $("#ubahPenjadwalan").on("show.bs.modal", function(event) {

                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var id = button.data("id"); // Ambil data-id dari tombol

                $.ajax({
                    url: "/analisa/penjadwalan/" + id,
                    type: "GET",
                    dataType: "json",
                    cache: false,
                    success: function(response) {
                        // Isi modal dengan data yang diterima
                        var da = JSON.stringify(response[0]);
                        var data = JSON.parse(da);
                        var hasil = data[0];

                        var sr = JSON.stringify(response[1]);
                        var datas = JSON.parse(sr);

                        $("#kode_pengajuan").val(hasil.kode_pengajuan);
                        $("#nama_nasabah").val(hasil.nama_nasabah);

                        $("#kode_petugas").empty();

                        $("#kode_petugas").append(
                            $("<option>", {
                                value: hasil.surveyor_kode,
                                text: hasil.name,
                            }).prop("selected", true)
                        );

                        //Petugas
                        $.each(datas, function(index, item) {
                            if (item.code_user != hasil.surveyor_kode) {
                                $("#kode_petugas").append(
                                    $("<option>", {
                                        value: item.code_user,
                                        text: item.nama_user,
                                    })
                                ).select2();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                        console.error("Error:", xhr.responseText);
                    },
                });
            });
        });
    </script>
@endpush
