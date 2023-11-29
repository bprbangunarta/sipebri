@section('title', 'Survey dan Analisa')
@extends('theme.app')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">SURVEI DAN ANALISA</h3>

                            <div class="box-tools">
                                <form action="{{ route('survei.analisa') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">NASABAH</th>
                                        <th class="text-center" width="30%">ALAMAT</th>
                                        <th class="text-center" width="17%">PENGAJUAN</th>
                                        <th class="text-center">KOMITE</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">{{ $no }}</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>NAMA :</b> {{ $item->nama_nasabah }} <br>
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}
                                            </td>

                                            @if (is_null($item->alamat_ktp))
                                                <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                                <td class="text-uppercase" style="vertical-align: middle;">
                                                    {{ $item->alamat_ktp }} <br>
                                                    <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                    </b>{{ $item->kecamatan }}
                                                </td>
                                            @endif

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kode_kantor }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jk }} BULAN -
                                                {{ $item->suku_bunga }}% <br>
                                                <b>METODE :</b> {{ $item->metode_rps }}<br>
                                                <b>PLAFON :</b>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>USULAN K1 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan1, 0, ',', '.') }} <br>
                                                <b>USULAN K2 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan2, 0, ',', '.') }} <br>
                                                <b>USULAN K3 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan3, 0, ',', '.') }} <br>
                                                <b>USULAN K4 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan4, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{-- @if ($item->tracking == 'Selesai' && $item->status == 'Disetujui')
                                                    <span class="btn bg-green"
                                                        style="width: 120px;hight:100%;">{{ $item->tracking }}</span>
                                                @elseif($item->tracking == 'Naik Komite II')
                                                    <span class="btn bg-yellow"
                                                        style="width: 120px;hight:100%;">{{ $item->tracking }}</span>
                                                @endif --}}
                                                <span class="btn bg-yellow"
                                                    style="width: 120px;">{{ $item->tracking }}</span>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#modal-catatan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-blue" style="width: 120px;">Lihat Catatan</span>
                                                </a>

                                            </td>
                                            @php
                                                $no++;
                                            @endphp

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
                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="modal fade" id="modal-catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN KOMITE</h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">STAFF ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="staff_analis" readonly>Catatan dari staff analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KASI ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kasi_analiss" readonly>Catatan dari kasi analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KABAG ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kabag_analis" readonly>Catatan dari kabag analis</textarea>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">DIREKSI</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="direksi" readonly>Catatan dari direksi</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    {{-- <script src="{{ asset('assets/js/myscript/persetujuan_komite.js') }}"></script>
<script src="{{ asset('assets/js/myscript/catatan_komite.js') }}"></script> --}}
    <script src="{{ asset('assets/js/myscript/catatan_komite.js') }}"></script>
    {{-- <script>
        $(document).ready(function() {
            $("#modal-catatan").on("show.bs.modal", function(event) {
                $("#komite").empty();
                var button = $(event.relatedTarget);
                var pengajuan = button.data("pengajuan");

                $.ajax({
                    url: "/themes/komite/kredit/catatan/" + pengajuan,
                    type: "get",
                    dataType: "json",
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        $("#staff_analis").val(response.catatan1 ?? null);
                        $("#kasi_analis").val(response.catatan2 ?? null);
                        $("#kabag_analis").val(response.catatan3 ?? null);
                        $("#direksi").val(response.catatan4 ?? null);
                    },
                    error: function(xhr, status, error) {
                        // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                        console.error("Error:", xhr.responseText);
                    },
                });
            });
        });
    </script> --}}
@endpush
