@extends('theme.app')
@section('title', 'Cetak Biaya RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">CETAK BIAYA RSC</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.cetakbiaya.index') }}" method="GET">
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
                                                <a href="{{ route('rsc.cetak.biaya', ['rsc' => $item->rsc]) }}"
                                                    class="btn-circle btn-sm bg-primary" title="Print Biaya RSC"
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
            $("#add_biaya_rsc").on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                const kode = button.data("kode");
                const rsc = button.data("rsc");

                var modal = $(this);
                modal.find('.modal-body #kode').val(kode);
                modal.find('.modal-body #kode_rsc').val(rsc);
            })
        })

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

        $('#add_biaya_rsc').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang membuka modal
            var pengajuan = button.data("kode");
            var kode = button.data("rsc");

            $.ajax({
                url: '{{ route('rsc.biaya.get') }}',
                dataType: 'json',
                type: 'get',
                data: {
                    data: kode
                },
                cache: false,
                success: function(response) {

                    $('#tunggakan_bunga').val(response.bunga_dibayar.toLocaleString("id-ID") ?? 0)
                    $('#tunggakan_denda').val(response.denda_dibayar.toLocaleString("id-ID") ?? 0)
                }
            })
        })
    </script>
@endpush
