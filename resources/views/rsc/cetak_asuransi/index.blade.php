@extends('theme.app')
@section('title', 'Cetak Asuransi RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">CETAK ASURANSI RSC</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.cetakpk.index') }}" method="GET">
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
                                        <th class="text-center">No SPK</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center" width="7%">DILANJUT</th>
                                        <th class="text-center" width="7%">TDK IKUT</th>
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
                                            <td class="text-center">{{ $item->no_spk }}</td>
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
                                                <a data-toggle="modal" data-target="#add_data_asuransi"
                                                    data-rsc="{{ $item->rsc }}" class="btn-circle btn-sm bg-yellow"
                                                    title="Add Data TDK DILANJUT" style="cursor: pointer;">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                &nbsp;
                                                <a href="{{ route('rsc.cetak.asuransi.cover', ['rsc' => $item->rsc, 'status_rsc' => $item->status_rsc]) }}"
                                                    class="btn-circle btn-sm bg-primary" title="Cetak Asuransi RSC"
                                                    target="_blank">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                            <td class="text-center" style="text-align: center;">
                                                <a href="{{ route('rsc.cetak.tidak.ikut', ['rsc' => $item->rsc, 'status_rsc' => $item->status_rsc]) }}"
                                                    class="btn-circle btn-sm bg-primary" title="Cetak Asuransi RSC"
                                                    target="_blank">
                                                    <i class="fa fa-print"></i>
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

    <div class="modal fade" id="add_data_asuransi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">ADD DATA ASURANSI RSC</h4>
                </div>
                <form action="{{ route('rsc.simpan.asuransi') }}" method="POST">
                    @method('POST')
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row" style="margin-bottom: 40px;">
                                <div class="col-md-12" style="margin-bottom: 20px;">
                                    <div style="margin-top: -15px; float:left; width: 47.5%;">
                                        <span class="fw-bold">NOMINAL ASURANSI</span>
                                        <input type="text" name="kode_rsc" id="kode_rsc" hidden>
                                        <input class="form-control text-uppercase" type="text" name="pertanggungan"
                                            id="pertanggungan">
                                    </div>
                                    <div style="margin-top: -15px; float:right; width: 47.5%;">
                                        <span class="fw-bold">TANGGAL MULAI</span>
                                        <input class="form-control text-uppercase" type="text" id="tgl_mulai"
                                            name="tgl_mulai" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div style="margin-top: -15px; float:left; width: 47.5%;">
                                        <span class="fw-bold">TANGGAL AKHIR</span>
                                        <input class="form-control text-uppercase" id="tgl_akhir" type="text"
                                            name="tgl_akhir" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $('#tgl_mulai').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })
        $('#tgl_akhir').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })

        var pertanggungan = document.getElementById('pertanggungan')

        if (pertanggungan) {
            pertanggungan.addEventListener("keyup", function(e) {
                pertanggungan.value = formatRupiah(this.value, "Rp. ");
            });
        }


        $(document).ready(function() {
            $("#add_data_asuransi").on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                const rsc = button.data("rsc");

                var modal = $(this);
                modal.find('.modal-body #kode_rsc').val(rsc);
            })
        })


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
    </script>
@endpush
