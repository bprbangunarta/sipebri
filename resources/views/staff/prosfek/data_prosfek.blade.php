@extends('theme.app')
@section('title', 'Data Prosfek')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-inbox" aria-hidden="true"></i>
                            <h3 class="box-title">DATA PROSFEK</h3>

                            <div class="box-tools">
                                <form action="{{ route('data.prosfek.index') }}" method="GET">
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
                                        <th class="text-center" width="8%">TGL PROSFEK</th>
                                        <th class="text-center" width="16%">CALON NASABAH</th>
                                        <th class="text-center" width="42%">ALAMAT</th>
                                        <th class="text-center" width="8%">NO HP</th>
                                        <th class="text-center" width="8%">USER</th>
                                        <th class="text-center" width="8%">PROFEK 1 VIA</th>
                                        <th class="text-center" width="8%">PROFEK 2 VIA</th>
                                        <th class="text-center" width="8%">PROFEK 3 VIA</th>
                                        <th class="text-center" width="8%">TGL PROSFEK 1</th>
                                        <th class="text-center" width="8%">TGL PROSFEK 2</th>
                                        <th class="text-center" width="8%">TGL PROSFEK 3</th>
                                        <th class="text-center" width="8%">KET 1</th>
                                        <th class="text-center" width="8%">KET 2</th>
                                        <th class="text-center" width="8%">KET 3</th>
                                        <th class="text-center" width="8%">FHOTO PROSFEK 1</th>
                                        <th class="text-center" width="8%">FHOTO PROSFEK 2</th>
                                        <th class="text-center" width="8%">FHOTO PROSFEK 3</th>
                                        <th class="text-center" width="8%">FHOTO CLOSING</th>
                                        <th class="text-center" width="8%">TGL CLOSING</th>
                                        {{-- @if (!in_array($role, ['Kasi Analis', 'Kabag Analis', 'Direktur Bisnis', 'Direksi']))
                                            <th class="text-center" width="8%">AKSI</th>
                                        @endif --}}
                                        @can('edit prosfek')
                                            <th class="text-center" width="8%">AKSI</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tgl_prosfek1)->format('d-m-Y') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->calon_nasabah }}</td>

                                            <td style="vertical-align: middle;">
                                                {{ $item->alamat . ' ' . 'KEL. ' . $item->kelurahan . ' KEC. ' . $item->kecamatan . ' ' . $item->kabupaten }}
                                            </td>
                                            <td style="vertical-align: middle;">{{ $item->no_hp }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_user }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->prosfek1_via }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->prosfek2_via }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->prosfek3_via }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tgl_prosfek3)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (empty($item->tgl_prosfek2))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_prosfek2)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (empty($item->tgl_prosfek3))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_prosfek3)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->ket1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->ket2 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->ket3 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <img style="width: 50px; height: auto; max-height: 30px; cursor:pointer;"
                                                    src="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek1) }}"
                                                    data-toggle="modal" data-target="#imageModal"
                                                    @if (!empty($item->fhoto_prosfek1)) data-image="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek1) }}"
                                                    @else
                                                    data-image="" @endif
                                                    alt="">
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <img style="width: 50px; height: auto; max-height: 30px; cursor:pointer;"
                                                    src="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek2) }}"
                                                    data-toggle="modal" data-target="#imageModal"
                                                    @if (!empty($item->fhoto_prosfek2)) data-image="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek2) }}"
                                                    @else
                                                    data-image="" @endif
                                                    alt="">
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <img style="width: 50px; height: auto; max-height: 30px; cursor:pointer;"
                                                    src="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek3) }}"
                                                    data-toggle="modal" data-target="#imageModal"
                                                    @if (!empty($item->fhoto_prosfek3)) data-image="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_prosfek3) }}"
                                                    @else
                                                    data-image="" @endif
                                                    alt="">
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <img style="width: 50px; height: auto; max-height: 30px; cursor:pointer;"
                                                    src="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_closing) }}"
                                                    data-toggle="modal" data-target="#imageModal"
                                                    @if (!empty($item->fhoto_closing)) data-image="{{ asset('storage/image/photo_prosfek/' . $item->fhoto_closing) }}"
                                                    @else
                                                    data-image="" @endif
                                                    alt="">
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (!is_null($item->tgl_closing))
                                                    {{ \Carbon\Carbon::parse($item->tgl_closing)->format('d-m-Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            {{-- @if (!in_array($role, ['Kasi Analis', 'Kabag Analis', 'Direktur Bisnis', 'Direksi']))
                                                <td class="text-center" style="vertical-align: middle;">
                                                    @if (is_null($item->tgl_closing) && (is_null($item->tgl_prosfek2) || is_null($item->tgl_prosfek3)))
                                                        <a data-toggle="modal" data-target="#prosfek"
                                                            data-id="{{ $item->id }}"
                                                            class="btn-circle btn-sm bg-yellow" title="Add Prosfek"
                                                            style="cursor: pointer;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @else
                                                        <a class="btn-circle btn-sm"
                                                            style="background: grey; color:white; cursor: not-allowed;"
                                                            title="Tidak dapat menambahkan prosfek">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif


                                                    &nbsp;
                                                    @if (is_null($item->tgl_closing))
                                                        <a data-toggle="modal" data-target="#closing"
                                                            data-id="{{ $item->id }}"
                                                            class="btn-circle btn-sm bg-red" title="Closing"
                                                            style="cursor: pointer;">
                                                            <i class="fa fa-window-close" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                        <a class="btn-circle btn-sm" style="background: grey; color:white"
                                                            title="Closing" style="cursor: pointer;">
                                                            <i class="fa fa-window-close" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif --}}
                                            @can('edit prosfek')
                                                <td class="text-center" style="vertical-align: middle;">
                                                    @if (is_null($item->tgl_closing) && (is_null($item->tgl_prosfek2) || is_null($item->tgl_prosfek3)))
                                                        <a data-toggle="modal" data-target="#prosfek"
                                                            data-id="{{ $item->id }}" class="btn-circle btn-sm bg-yellow"
                                                            title="Add Prosfek" style="cursor: pointer;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @else
                                                        <a class="btn-circle btn-sm"
                                                            style="background: grey; color:white; cursor: not-allowed;"
                                                            title="Tidak dapat menambahkan prosfek">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif


                                                    &nbsp;
                                                    @if (is_null($item->tgl_closing))
                                                        <a data-toggle="modal" data-target="#closing"
                                                            data-id="{{ $item->id }}" class="btn-circle btn-sm bg-red"
                                                            title="Closing" style="cursor: pointer;">
                                                            <i class="fa fa-window-close" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                        <a class="btn-circle btn-sm" style="background: grey; color:white"
                                                            title="Closing" style="cursor: pointer;">
                                                            <i class="fa fa-window-close" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="21">TIDAK ADA DATA</td>
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

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" style="max-width: 100%; height: auto;" alt="Preview">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="prosfek">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PROSFEK ULANG</h4>
                </div>
                <form action="{{ route('data.prosfek.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">CALON NASABAH</span>
                                    <input type="hidden" value="" name="id" id="id">
                                    <input class="form-control text-uppercase" type="text" name="calon_nasabah"
                                        id="calon_nasabah" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">ALAMAT</span>
                                    <input class="form-control text-uppercase" name="alamat" id="alamat"
                                        type="text" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">FHOTO PROSFEK</span>
                                    <input type="file" class="form-control" class="photo_prosfek"
                                        name="photo_prosfek" id="photo_prosfek" required>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">PROSFEK VIA</span>
                                    <select class="form-control text-uppercase via" style="width: 100%;"
                                        name="prosfek_via" required>
                                        <option value="">--PILIH--</option>
                                        <option value="VIDEO CALL">VIDEO CALL</option>
                                        <option value="DATANG LANGSUNG">DATANG LANGSUNG</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <input class="form-control text-uppercase" name="keterangan" id=""
                                        type="text" value="{{ old('keterangan') }}" required>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">TAMBAH PROSFEK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="closing">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CLOSING</h4>
                </div>
                <form action="{{ route('data.prosfek.closing') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">CALON NASABAH</span>
                                    <input type="hidden" value="" name="id" id="idc">
                                    <input class="form-control text-uppercase" type="text" name="calon_nasabah"
                                        id="calon_nasabahc" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">ALAMAT</span>
                                    <input class="form-control text-uppercase" name="alamat" id="alamatc"
                                        type="text" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">FHOTO CLOSING</span>
                                    <input type="file" class="form-control" class="photo_prosfek"
                                        name="photo_closing" id="photo_prosfek" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">CLOSING</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(document).ready(function() {
            $('img[data-toggle="modal"]').on('click', function() {
                const imageSrc = $(this).data('image');
                $('#modalImage').attr('src', imageSrc);
            });


            $('#prosfek').on('show.bs.modal', function(e) {
                var button = $(e.relatedTarget)
                var id = button.data('id')


                $.ajax({
                    url: "{{ route('data.prosfek.get') }}",
                    type: "get",
                    dataType: "json",
                    cache: false,
                    data: {
                        data: id
                    },
                    success: function(response) {
                        $('#calon_nasabah').val(response.calon_nasabah)
                        $('#alamat').val(response.alamat_calon)
                        $('#id').val(response.id)
                    },
                    error: function() {

                    }
                })
            })

            $('#closing').on('show.bs.modal', function(e) {
                var button = $(e.relatedTarget)
                var id = button.data('id')


                $.ajax({
                    url: "{{ route('data.prosfek.get') }}",
                    type: "get",
                    dataType: "json",
                    cache: false,
                    data: {
                        data: id
                    },
                    success: function(response) {
                        $('#calon_nasabahc').val(response.calon_nasabah)
                        $('#alamatc').val(response.alamat_calon)
                        $('#idc').val(response.id)
                    },
                    error: function() {

                    }
                })
            })
        });
    </script>
@endpush
