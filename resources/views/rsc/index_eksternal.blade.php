@extends('theme.app')
@section('title', 'Add Pengajuan RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">ADD RESCHEDULLING EKSTERNAL</h3>

                            <div class="box-tools">
                                <form action="{{ route('rsc.index') }}" method="GET">
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
                                        <th class="text-center">KODE NASABAH</th>
                                        <th class="text-center">KODE RSC</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center" width="7%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @forelse ($data as $item)
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
                                            <td class="text-center" style="text-align: right;">

                                                <a href="" class="btn-circle btn-sm bg-yellow" title="Info RSC"
                                                    disabled>
                                                    <i class="fa fa-info"></i>
                                                </a>

                                                &nbsp;

                                                <form action="{{ route('rsc.delete.rsc', ['rsc' => $item->rsc]) }}"
                                                    method="POST" style="display:inline;">
                                                    @method('delete')
                                                    @csrf
                                                    <a href="#" class="btn-circle btn-sm bg-red confirmdelete"
                                                        title="Hapus" style="cursor: pointer;">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="8">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse --}}

                                    <tr>
                                        <td class="text-center">

                                        </td>
                                        <td class="text-center">

                                        </td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td></td>
                                        <td class="text-uppercase">

                                        </td>

                                        <td class="text-center">

                                        </td>
                                        <td class="text-right">
                                        </td>
                                        <td class="text-center" style="text-align: right;">

                                            <a href="" class="btn-circle btn-sm bg-yellow" title="Info RSC" disabled>
                                                <i class="fa fa-info"></i>
                                            </a>

                                            &nbsp;

                                            <form action="{{ route('rsc.delete.rsc') }}" method="POST"
                                                style="display:inline;">
                                                @method('delete')
                                                @csrf
                                                <a href="#" class="btn-circle btn-sm bg-red confirmdelete"
                                                    title="Hapus" style="cursor: pointer;">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modal-tambah" class="btn bg-blue btn-sm">
                                    <i class="fa fa-plus"></i>&nbsp; TAMBAH
                                </button>
                                {{-- <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button> --}}
                            </div>

                            {{-- {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }} --}}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAMBAH RESCHEDULLING</h4>
                </div>
                <form action="{{ route('rsc.tambah.rsc') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>KODE NASABAH</label>
                                    <input class="form-control" type="text" name="pengajuan_kode" id="pengajuan_kode"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>NAMA KASI</label>
                                    <select class="form-control" name="kasi_kode" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        @foreach ($kasi as $item)
                                            <option value="{{ $item->code_user }}">{{ $item->nama_user }} -
                                                {{ $item->kantor_kode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>JENIS PERSETUJUAN</label>
                                    <select class="form-control" name="jenis_persetujuan" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        <option value="RESCHEDULLING"
                                            {{ old('RESCHEDULLING') == 'RESCHEDULLING' ? 'selected' : '' }}>
                                            RESCHEDULLING
                                        </option>
                                        <option value="RECONDITIONING"
                                            {{ old('RECONDITIONING') == 'RECONDITIONING' ? 'selected' : '' }}>
                                            RECONDITIONING
                                        </option>
                                        <option value="RESTRUKTURING"
                                            {{ old('RESTRUKTURING') == 'RESTRUKTURING' ? 'selected' : '' }}>
                                            RESTRUKTURING
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>NAMA SURVEYOR</label>
                                    <select class="form-control" name="surveyor_kode" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        @foreach ($surveyor as $item)
                                            <option value="{{ $item->code_user }}">{{ $item->kantor_kode }} -
                                                {{ $item->nama_user }}
                                            </option>
                                        @endforeach
                                    </select>
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
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
@endpush
