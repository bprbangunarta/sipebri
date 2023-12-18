@extends('theme.app')
@section('title', 'List Pengajuan')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-bars"></i>
                            <h3 class="box-title">LIST PENGAJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('pengajuan.data') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <a data-toggle="modal" data-target="#modal-filter" class="btn btn-sm btn-default">
                                            <i class="fa fa-filter"></i> Short & Filter
                                        </a>

                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;font-size:11.4px;" name="keyword" id="keyword"
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
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="42%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="10%">AKSI</th>
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
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode }}</td>
                                            <td style="vertical-align: middle;">{{ strtoupper($item->nama) }}</td>
                                            @if (is_null($item->alamat))
                                                <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                                <td class="text-uppercase" style="vertical-align: middle;">
                                                    {{ $item->alamat }}
                                                </td>
                                            @endif

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kantor }}
                                            </td>

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ $item->plafon }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{-- <a href="{{ route('tracking', ['pengajuan' => $item->kd]) }}"
                                                    class="btn-circle btn-sm bg-yellow" title="Tracking Pengajuan">
                                                    <i class="fa fa-hourglass-start"></i>
                                                </a> --}}

                                                <a href="/laporan/tracking/pengajuan?keyword={{ $item->kode }}"
                                                    class="btn-circle btn-sm bg-yellow" title="Tracking Pengajuan" target="_blank">
                                                    <i class="fa fa-hourglass-start"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode }}"
                                                    class="btn-circle btn-sm bg-blue" title="Informasi">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>

                                            {{-- MODAL INFO --}}
                                            <div class="modal fade" id="info-{{ $item->kode }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-blue">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">INFORMASI PENGAJUAN</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>NAMA NASABAH</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->nama }} - {{ $item->kategori }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PRODUK KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->produk_kode }} - {{ $item->nama_produk }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PLAFON KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->plafon }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>JANGKA WAKTU</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->jk }} BULAN - {{ $item->metode_rps }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>DESA KECAMATAN</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->kelurahan }} - {{ $item->kecamatan }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>SURVEYOR</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->surveyor }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>TRACKING</label>
                                                                        <input type="text"
                                                                            class="form-control text-uppercase"
                                                                            value="{{ $item->tracking }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PERSETUJUAN</label>
                                                                        @if ($item->status == 'Disetujui' || $item->status == 'Ditolak' || $item->status == 'Dibatalkan')
                                                                            <input type="text"
                                                                                class="form-control text-uppercase"
                                                                                value="{{ $item->status }}">
                                                                        @else
                                                                            <input type="text"
                                                                                class="form-control text-uppercase"
                                                                                value="BELUM ADA PERSETUJUAN">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer" style="margin-top: -10px;">
                                                            <button type="submit" class="btn bg-blue"
                                                                data-dismiss="modal" style="width: 100%;">TUTUP</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL INFO --}}
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
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


    <div class="modal fade" id="modal-filter">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">SHORT & FILTER</h4>
                </div>
                <form action="{{ route('pengajuan.data') }}" method="GET">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>MULAI DARI</label>
                                    <input type="date" class="form-control" name="tgl1" id="tgl1"
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>PRODUK</label>
                                    <select class="form-control produk" name="kode_produk" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->kode_produk }}">{{ $item->kode_produk }} - {{ $item->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KANTOR</label>
                                    <select class="form-control kantor" name="nama_kantor" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $item)
                                            <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>RESORT</label>
                                    <select class="form-control resort" name="resort" id="" style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($resort as $item)
                                            <option value="{{ $item->kode_resort }}">{{ $item->nama_resort }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KECAMATAN</label>
                                    <select class="form-control kecamatan" name="kecamatan" id="" style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kecamatan as $item)
                                            <option value="{{ $item->kecamatan }}">{{ $item->kecamatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SAMPAI DENGAN</label>
                                    <input type="date" class="form-control" name="tgl2" id="tgl2"
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>METODE RPS</label>
                                    <select class="form-control metode" name="metode" id="" style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($metode as $item)
                                            <option value="{{ $item->nama_metode }}">{{ $item->nama_metode }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>SURVEYOR</label>
                                    <select class="form-control surveyor" name="surveyor" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($surveyor as $item)
                                            <option value="{{ $item->code_user }}">{{ $item->nama_user }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KABUPATEN</label>
                                    <select class="form-control kabupaten" name="kebupaten" id="" style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kabupaten as $item)
                                            <option value="{{ $item->kode_dati }}">{{ $item->kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>KELURAHAN</label>
                                    <select class="form-control kelurahan" name="kelurahan" id="" style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kelurahan as $item)
                                            <option value="{{ $item->kelurahan }}">{{ $item->kelurahan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">FILTER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // Select2
        $('.produk').select2()
        $('.metode').select2()
        $('.kantor').select2()
        $('.surveyor').select2()
        $('.resort').select2()
        $('.kabupaten').select2()
        $('.kecamatan').select2()
        $('.kelurahan').select2()
    </script>
@endpush