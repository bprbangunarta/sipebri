@extends('theme.app')
@section('title', 'Laporan Pencairan Kredit')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">LAPORAN PENCAIRAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('laporan.pencairan') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        {{-- <a data-toggle="modal" data-target="#modal-filter" class="btn btn-sm btn-default">
                                            <i class="fa fa-filter"></i> Short & Filter
                                        </a> --}}

                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

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
                            <table class="table table-responsive table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center">TANGGAL</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NAMA DEBITUR</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PDK</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center">USER</th>
                                        <th class="text-center">NO. SPK</th>
                                        <th class="text-center">JK</th>
                                        <th class="text-center">RATE</th>
                                        <th class="text-center">METODE RPS</th>
                                        <th class="text-center">JTH. TEMPO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-center">{{ $item->kantor_kode }}</td>
                                            <td class="text-center">{{ $item->produk_kode }}</td>
                                            <td class="text-right">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td>{{ $item->nama_user }}</td>
                                            <td class="text-center">{{ $item->no_spk }}</td>
                                            <td class="text-center">{{ $item->jangka_waktu }}</td>
                                            <td class="text-center">{{ $item->suku_bunga }}%</td>
                                            <td class="text-center">{{ $item->metode_rps }}</td>

                                            @php
                                                $tanggal = \Carbon\Carbon::parse($item->tanggal);
                                                $jk = $item->jangka_waktu;
                                                $tempo = $tanggal->addMonths($jk);
                                                $jth_tempo = $tempo->format('d-m-Y');
                                            @endphp
                                            <td class="text-center">{{ $jth_tempo }}</td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="15">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i>&nbsp; Export Data
                                </button>

                                {{-- <button data-toggle="modal" data-target="#modal-export-filter"
                                    class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i>&nbsp; Export By Filter
                                </button> --}}

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
                <form action="{{ route('filter.laporan.fasilitas') }}" method="GET">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>MULAI DARI</label>
                                    <input type="date" class="form-control" name="tgl1" id="tgl1"
                                        style="margin-top:-5px;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SAMPAI DENGAN</label>
                                    <input type="date" class="form-control" name="tgl2" id="tgl2"
                                        style="margin-top:-5px;">
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

    <div class="modal fade" id="modal-export">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">EXPORT DATA</h4>
                </div>
                <form action="{{ route('export.fasilitas') }}" method="POST">
                    @csrf
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
                                    <select class="form-control" name="kode_produk" id=""
                                        style="margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->kode_produk }}">{{ $item->nama_produk }}</option>
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
                                    <label>KANTOR</label>
                                    <select class="form-control" name="nama_kantor" id=""
                                        style="margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($kantor as $item)
                                            <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-success">EXPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('myscript')
    <script>
        $('.cgc').select2()
        $('.res').select2()
    </script>
@endpush
