@extends('theme.app')
@section('title', 'Penolakan RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">PENOLAKAN RSC</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.penolakan') }}" method="GET">
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
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (is_null($item->no_penolakan))
                                                    <a href="" class="btn-circle btn-sm btn-warning"
                                                        data-toggle="modal" data-kode="{{ $item->kode_rsc }}"
                                                        data-target="#modal-penolakan" title="Penolakan RSC">
                                                        <i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <a href="" class="btn-circle btn-sm btn-success"
                                                        data-toggle="modal" data-kode="{{ $item->kode_rsc }}"
                                                        data-target="#modal-penolakan-update" title="Penolakan RSC">
                                                        <i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                @if (is_null($item->no_penolakan))
                                                    <a href="#" class="btn-circle btn-sm bg-gray"
                                                        style="pointer-events: none;" title="Informasi">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('rsc.cetak.penolakan', ['kode_rsc' => $item->rsc_kode]) }}"
                                                        target="__blank" class="btn-circle btn-sm bg-blue"
                                                        title="Informasi">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @endif
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

    <div class="modal fade" id="modal-penolakan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">SURAT PENOLAKAN</h4>
                </div>

                <form action="{{ route('rsc.simpan_penolakan') }}" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- Hiden --}}
                                <input type="text" name="kode_rsc" id="kode_rsc" value="" hidden>
                                <div class="form-group">
                                    <label>NOMOR SURAT</label>
                                    {{-- Hiden --}}
                                    <input type="text" name="nomor" id="nomor" hidden value="">
                                    <input type="text" class="form-control" style="margin-top: -5px;"
                                        name="no_penolakan" value="" id="no_penolakan" readonly>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>NAMA NASABAH</label>
                                    <input type="text" class="form-control" name="nama_nasabah"
                                        style="margin-top: -5px;" value="" id="nm_nasabah" readonly>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>ALASAN PENOLAKAN</label>
                                    <select class="form-control text-uppercase" style="margin-top: -5px;" name="alasan"
                                        id="alasan_id" required>
                                        <option value="">--PILIH--</option>
                                        @foreach ($alasan as $item)
                                            <option value="{{ $item->id }}">{{ $item->alasan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>KETERANGAN INTERNAL</label>
                                    <textarea class="form-control text-upper" name="keterangan" id="keterangan"rows="3" style="margin-top:-5px;"
                                        required>{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-penolakan-update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">UPDATE SURAT PENOLAKAN</h4>
                </div>

                <form action="{{ route('rsc.update_penolakan') }}" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- Hiden --}}
                                <input type="text" name="kode_rsc" id="kode_rscup" value="" hidden>
                                <div class="form-group">
                                    <label>NOMOR SURAT</label>
                                    {{-- Hiden --}}
                                    <input type="text" name="nomor" id="nomorup" hidden value="">
                                    <input type="text" class="form-control" style="margin-top: -5px;"
                                        name="no_penolakan" value="" id="no_penolakanup" readonly>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>NAMA NASABAH</label>
                                    <input type="text" class="form-control" name="nama_nasabah"
                                        style="margin-top: -5px;" value="" id="nm_nasabahup" readonly>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>ALASAN PENOLAKAN</label>
                                    <select class="form-control text-uppercase" style="margin-top: -5px;" name="alasan"
                                        id="alasan_idup" required>
                                        <option value="">--PILIH--</option>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top: -10px;">
                                    <label>KETERANGAN INTERNAL</label>
                                    <textarea class="form-control text-upper" name="keterangan" id="keteranganup"rows="3" style="margin-top:-5px;"
                                        required>{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            $('#modal-penolakan').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var kode = button.data('kode')

                $.ajax({
                    url: '/themes/rsc/add/penolakan',
                    type: 'GET',
                    data: {
                        kode: kode,
                    },
                    dataType: 'json',
                    cache: false,
                    success: function(response) {

                        $('#kode_rsc').val(response.kode_rsc)
                        $('#nomor').val(response.nomor)
                        $('#nm_nasabah').val(response.nama_nasabah)
                        $('#no_penolakan').val(response.no_penolakan)
                        $('#keterangan').val(response.ket)

                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {

                    console.log('Error:', jqXHR.responseJSON.error);

                    Swal.fire({
                        title: jqXHR.responseJSON.error,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
            })
        })

        $(document).ready(function() {
            $('#modal-penolakan-update').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var kode = button.data('kode')

                $.ajax({
                    url: '/themes/rsc/add/penolakan',
                    type: 'GET',
                    data: {
                        kode: kode,
                    },
                    dataType: 'json',
                    cache: false,
                    success: function(response) {

                        $('#kode_rscup').val(response[0].kode_rsc)
                        $('#nomorup').val(response[0].nomor)
                        $('#nm_nasabahup').val(response[0].nama_nasabah)
                        $('#no_penolakanup').val(response[0].no_penolakan)
                        $('#keteranganup').val(response[0].ket)

                        var select = $('#alasan_idup');
                        var selectedCategoryId = response[0].alasan_id;

                        $.each(response[1], function(index, category) {
                            var selected = '';
                            if (category.id == selectedCategoryId) {
                                selected = 'selected';
                            }
                            select.append('<option value="' + category.id + '" ' +
                                selected + '>' + category.alasan + '</option>');
                        });
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {

                    console.log('Error:', jqXHR.responseJSON.error);

                    Swal.fire({
                        title: jqXHR.responseJSON.error,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
            })
        })
    </script>
@endpush
