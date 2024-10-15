@extends('theme.app')
@section('title', 'Input Analisa')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-edit"></i>
                            <h3 class="box-title">INPUT ANALISA</h3>

                            <div class="box-tools">
                                <form action="{{ route('permohonan.analisa') }}" method="GET">
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
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="7%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="40%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="8%">STATUS</th>
                                        <th class="text-center" width="13%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tgl_daftar)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td style="text-align: center;">
                                                {{-- @if ($item->status_pengembalian == 'YA')
                                                    <label for="" style="color: red;">PERBAIKAN</label>
                                                @elseif ($item->status_pengembalian == 'DONE')
                                                    <label style="color: green">SELESAI</label>
                                                @elseif ($item->status_pengembalian == 'TIDAK')
                                                    <label for="">-</label>
                                                @endif --}}
                                                -
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{-- @if ($item->status_pengembalian == 'YA')
                                                    <a data-toggle="modal" data-target="#modal-perbaikan"
                                                        data-pengajuan="{{ $item->kode_pengajuan }}"
                                                        class="btn-circle btn-sm btn-info" title="Update Perbaikan"
                                                        style="cursor: pointer;">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    &nbsp;
                                                @else
                                                    <a href="#" class="btn-circle btn-sm" title="Update Perbaikan"
                                                        style="cursor: pointer; background: rgb(220, 220, 220);">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    &nbsp;
                                                @endif --}}

                                                @if ($item->tracking == 'Proses Survei')
                                                    <a data-toggle="modal" data-target="#modal-danger"
                                                        class="btn-circle btn-sm btn-default" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif($item->tracking == 'Proses Analisa')
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        class="btn-circle btn-sm bg-green" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#jadwal-ulang"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-blue" title="Jadwal Ulang"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-history"></i>
                                                </a>

                                                &nbsp;
                                                @if ($item->tracking == 'Proses Survei' || $item->tracking == 'Proses Analisa')
                                                    <a data-toggle="modal" data-target="#tolak-batal"
                                                        data-tolak="{{ $item->kode_pengajuan }}"
                                                        class="btn-circle btn-sm bg-red" title="Bypass"
                                                        style="cursor: pointer;">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @else
                                                    <a data-toggle="modal" data-target="#danger2"
                                                        class="btn-circle btn-sm bg-red" title="Bypass"
                                                        style="cursor: pointer;">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="9">Tidak ada data</td>
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

    <div class="modal fade" id="modal-perbaikan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PENGEMBALIAN BERKAS</h4>
                </div>
                <form action="{{ route('simpan.perbaikan.analisa') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">

                            <div class="row">
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN PENGEMBALIAN BERKAS</span>
                                    <input type="text" name="kode" id="kd_pengajuan" hidden>
                                    <textarea class="form-control text-uppercase" rows="8" name="catatan" id="catatans" readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <p style="font-size: 14px; color:red;">
                            * pilih <b>SIMPAN</b> jika telah melakukan perbaikan sesuai catatan.
                        </p>
                    </div>

                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="jadwal-ulang">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">JADWAL ULANG</h4>
                </div>
                <form action="{{ Route('permohonan.simpanjadul') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
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

    <div class="modal fade" id="tolak-batal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">BYPASS ANALISA</h4>
                </div>
                <form action="{{ route('tolak.simpan_penolakan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="kode_pengajuan" name="id" hidden>
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
                                        Fitur ini digunakan untuk proses <b class="text-danger">penolakan</b> atau <b
                                            class="text-danger">pembatalan</b> pengajuan kredit dengan kondisi tanpa proses
                                        input analisa. Terimakasih
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger">BYPASS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">INPUT ANALISA</h4>
                </div>

                <div class="modal-body">
                    <p>Mohon maaf input analisa kredit tidak bisa dilakukan karena belum melakukan survey, silahkan upload
                        foto survey melalui apliasi client sipebri. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn btn-default" style="width: 100%;"
                        data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="danger2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">BYPASS ANALISA</h4>
                </div>

                <div class="modal-body">
                    <p>Tracking pengajuan kredit sudah dalam proses persetujuan komite kredit, Anda jangan macam-macam
                        dengan fitur ini. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn bg-red" style="width: 100%;" data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/permintaan_jadul.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/tolak_permohonan.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("a[data-target='#tolak-batal']").click(function() {

                var dataId = $(this).data('tolak');

                $('#kode_pengajuan').val(dataId)
            });
        });

        $(document).ready(function() {
            $("#modal-perbaikan").on("show.bs.modal", function(event) {
                var button = $(event.relatedTarget);
                var pengajuan = button.data("pengajuan");

                $("#kd_pengajuan").val(pengajuan);
                $.ajax({
                    url: "/themes/perbaikan/analisa",
                    type: "get",
                    dataType: "json",
                    cache: false,
                    data: {
                        data: pengajuan
                    },
                    success: function(response) {
                        $('#catatans').empty()
                        $.each(response, function(index, value) {
                            let data = [
                                '**' + " " + value.role + " " + '**' + "\n" + value
                                .catatan +
                                "\n\n"
                            ]
                            $('#catatans').append(data.join(''));
                        })

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
