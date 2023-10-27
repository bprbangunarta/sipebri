@extends('theme.app')
@section('title', 'Putusan Komite')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">SURVEI DAN ANALISA</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" style="width: 10px">#</th>
                                        <th class="text-center" style="width: 200px">NASABAH</th>
                                        <th class="text-center" style="width: 150px">PENGAJUAN</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" style="width: 50px;">K1</th>
                                        <th class="text-center" style="width: 50px;">K2</th>
                                        <th class="text-center" style="width: 50px;">K3</th>
                                        <th class="text-center" style="width: 50px;">K4</th>
                                        <th class="text-center">CATATAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">1</td>
                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->nama_nasabah }} <br>
                                                <b>Kategori:</b> {{ $item->kategori ?? 'KOSONG' }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE: </b>{{ $item->kode_pengajuan }} <br>
                                                <b>PLAFON</b> : <br>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="label label-warning">{{ $item->tracking }}</span>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 1000 && $item->plafon <= 10000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-success"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 10000001 && $item->plafon <= 35000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @elseif ($item->plafon >= 35000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 35000001 && $item->plafon <= 75000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @elseif ($item->plafon >= 75000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon > 75000001)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-catatan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <span class="label label-warning">BERIKAN CATATAN</span>
                                                </a>
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
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="modal fade" id="modal-catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
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
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kasi_analis" readonly>Catatan dari kasi analis</textarea>
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
    <script>
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
    </script>
@endpush
