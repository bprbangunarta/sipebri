@extends('theme.app')
@section('title', 'Index Perjanjian Kredit RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">PERJANJIAN KREDIT RSC</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.perjanjian_kredit') }}" method="GET">
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
                                        <th class="text-center" width="10%">KODE PENGAJUAN</th>
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
                                            <td class="text-center" style="text-align: center;">

                                                <a data-toggle="modal" data-target="#add_spk_rsc"
                                                    data-rsc="{{ $item->rsc }}" data-kode="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-yellow" title="Add SPK RSC"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                &nbsp;
                                                <a data-toggle="modal" data-target="#generate-code"
                                                    data-id="{{ $item->rsc }}" data-kode="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-green" title="Generate"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-file-text"></i>
                                                </a>

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

    <div class="modal fade" id="generate-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">GENERATE PERJANJIAN KREDIT RSC</h4>
                </div>
                <form action="{{ route('rsc.perjanjian_kredit.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6" style="margin-bottom: 25px;">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">PLAFON RSC</span>
                                        <input class="form-control text-uppercase" type="text" name=""
                                            id="plafond_rsc" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">SPK RSC</span>
                                        <input class="form-control text-uppercase" type="text" name="spk_rsc"
                                            id="spk_rsc" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">KODE RSC</span>
                                        <input type="text" id="kode" hidden>
                                        <input type="text" name="nomor" id="nomor" hidden>
                                        <input class="form-control text-uppercase" type="text" name="kode_rsc"
                                            id="kode_rsc" readonly>
                                    </div>

                                    {{-- <div style="margin-top: 5px;">
                                        <span class="fw-bold">TUNGGAKAN BUNGA</span>
                                        <input class="form-control text-uppercase" name="tunggakan_bunga"
                                            id="tunggakan_bunga" type="text">
                                    </div> --}}

                                    {{-- <div style="margin-top: 5px;">
                                        <span class="fw-bold">TUNGGAKAN DENDA</span>
                                        <input type="text" class="form-control" name="tunggakan_denda"
                                            id="tunggakan_denda">
                                    </div> --}}

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">KODE PERJANJIAN KREDIT RSC</span>
                                        <input class="form-control text-uppercase" name="pk_rsc" id="generate"
                                            type="text" readonly>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">TANGGAL REALISASI</span>
                                        <input class="form-control text-uppercase" type="text" name="tg_realisasi"
                                            id="tg_realisasi" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TANGGAL JATUH TEMPO</span>
                                        <input class="form-control text-uppercase" type="text" name="tgL_jth_tmp"
                                            id="tgL_jth_tmp" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PLAFON USULAN / BAKI DEBET</span>
                                        <input type="text" class="form-control" name="plafon" id="plafon"
                                            readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS PERSETUJUAN</span>
                                        <input type="text" class="form-control" name="jenis_persetujuan"
                                            id="jenis_persetujuan" readonly>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <P>
                            <table>
                                <tr>
                                    <td colspan="3">Note : </td>
                                </tr>
                                <tr>
                                    <td width='3%'>*</td>
                                    <td>PLAFON RSC, SPK RSC, TANGGAL REALISASI, TANGGAL JATUH TEMPO</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>akan terisi jika sebelumnya telah di RSC.</td>
                                </tr>
                            </table>
                            </P>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-green">GENERATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_spk_rsc">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">ADD SPK RSC</h4>
                </div>
                <form action="{{ route('rsc.add_spk.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row" style="margin-bottom: 40px;">
                                <div class="col-md-12" style="margin-bottom: 20px;">
                                    <div style="margin-top: -15px; float:left; width: 47.5%;">
                                        <span class="fw-bold">PLAFON RSC</span>
                                        <input type="text" id="kode" name="pengajuan_kode" hidden>
                                        <input type="text" name="kode_rsc" id="kode_rsc" hidden>
                                        <input class="form-control text-uppercase" type="text" name="plafon_rsc"
                                            id="plafon_rsc">
                                    </div>
                                    <div style="margin-top: -15px; float:right; width: 47.5%;">
                                        <span class="fw-bold">SPK RSC</span>
                                        <input class="form-control text-uppercase" type="text" name="spk_rsc"
                                            id="spk_rsc">
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div style="margin-top: -15px; float:left; width: 47.5%;">
                                        <span class="fw-bold">TANGGAL REALISASI</span>
                                        <input class="form-control text-uppercase" type="text" id="tgl_realisasi"
                                            name="tgl_realisasi" placeholder="YYYY-MM-DD">
                                    </div>
                                    <div style="margin-top: -15px; float:right; width: 47.5%;">
                                        <span class="fw-bold">JANGKA WAKTU</span>
                                        <input class="form-control text-uppercase" id="jw" type="text"
                                            name="jw">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div style="margin-top: 5px; float:left; width: 47.5%;">
                                        <span class="fw-bold">TANGGAL JATUH TEMPO</span>
                                        <input class="form-control text-uppercase" id="tgl_jth_temp" type="text"
                                            name="tgl_jth_temp" placeholder="YYYY-MM-DD" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-green">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
    <script>
        $('#tgl_realisasi').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })
        $('#tgl_jth_temp').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })

        $(document).ready(function() {
            $("#add_spk_rsc").on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                const kode = button.data("kode");
                const rsc = button.data("rsc");

                var modal = $(this);
                modal.find('.modal-body #kode').val(kode);
                modal.find('.modal-body #kode_rsc').val(rsc);
            })
        })

        $("#generate-code").on("show.bs.modal", function(event) {
            var button = $(event.relatedTarget); // Tombol yang membuka modal
            var kode = button.data("id");
            var pengajuan = button.data("kode");

            // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
            $.ajax({
                url: "/themes/rsc/perjanjiankredit/get/",
                type: "GET",
                data: {
                    kode: kode,
                    pengajuan: pengajuan,
                },
                dataType: "json",
                cache: false,
                success: function(response) {

                    $('#plafond_rsc').val(response.plafon.toLocaleString("id-ID") ?? 0)
                    $('#spk_rsc').val(response.no_spk)
                    $('#tg_realisasi').val(response.tgL_realisasi)
                    $('#tgL_jth_tmp').val(response.tgl_jth_tempo)
                    $('#kode_rsc').val(response.kode_rsc)
                    $('#plafon').val(response.penentuan_plafon.toLocaleString("id-ID"))
                    $('#jenis_persetujuan').val(response.jenis_persetujuan)
                    $('#generate').val(response.no_spk_rsc)
                    $('#nomor').val(response.nomor)
                    $('#tunggakan_bunga').val(response.tunggakan_bunga.toLocaleString("id-ID") ?? 0)
                    $('#tunggakan_denda').val(response.tunggakan_denda.toLocaleString("id-ID") ?? 0)

                },
                error: function(xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });



        var tunggakan_bunga = document.getElementById('tunggakan_bunga')
        var tunggakan_denda = document.getElementById('tunggakan_denda')
        var plafon_rsc = document.getElementById('plafon_rsc')

        if (tunggakan_bunga) {
            tunggakan_bunga.addEventListener("keyup", function(e) {
                tunggakan_bunga.value = formatRupiah(this.value, "Rp. ");
            });
        }

        if (tunggakan_denda) {
            tunggakan_denda.addEventListener("keyup", function(e) {
                tunggakan_denda.value = formatRupiah(this.value, "Rp. ");
            });
        }

        if (plafon_rsc) {
            plafon_rsc.addEventListener("keyup", function(e) {
                plafon_rsc.value = formatRupiah(this.value, "Rp. ");
            });
        }


        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
        }


        // Generate tanggal jatuh tempo
        $(document).ready(function() {
            $('#tgl_realisasi, #jw').on('input', function() {
                const tgl_realisasi = $('#tgl_realisasi').val()
                const jw = $('#jw').val()

                if (tgl_realisasi && jw) {
                    // Menambahkan 1 bulan ke tgl_realisasi, lalu menambahkan jw bulan
                    const tgl_temp = moment(tgl_realisasi, 'YYYY-MM-DD')
                        .add(jw, 'months')
                        .format('YYYY-MM-DD');

                    $('#tgl_jth_temp').val(tgl_temp)
                }

            })
        })
    </script>
@endpush
