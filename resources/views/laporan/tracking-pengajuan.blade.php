@extends('theme.app')
@section('title', 'Laporan Tracking Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">LAPORAN TRACKING PENGAJUAN</h3>

                            <div class="box-tools">
                                <form action="{{ route('laporan.tracking.pengajuan') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <a data-toggle="modal" data-target="#modal-filter" class="btn btn-sm btn-default">
                                            <i class="fa fa-filter"></i> Short & Filter
                                        </a>

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
                                        <th class="text-center">JK</th>
                                        <th class="text-center">RATE</th>
                                        <th class="text-center">SURVEYOR</th>
                                        <th class="text-center">SURVEY</th>
                                        <th class="text-center">ANALISA</th>
                                        <th class="text-center">PUTUSAN</th>
                                        <th class="text-center">REALISASI</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">ESTIMASI</th>
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
                                            <td class="text-center">{{ $item->jangka_waktu }}</td>
                                            <td class="text-center">{{ $item->suku_bunga }}%</td>
                                            <td>{{ $item->nama_user }}</td>
                                            <td class="text-center">
                                                @if (is_null($item->tgl_survey))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_survey)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (is_null($item->tgl_analisa))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_analisa)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (is_null($item->tgl_persetujuan))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_persetujuan)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (is_null($item->tgl_realisasi))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_realisasi)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->status == "Disetujui" || $item->status == "Ditolak" || $item->status == "Dibatalkan")
                                                    {{ $item->status }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!is_null($item->tgl_realisasi))
                                                    @php
                                                        $hari = strtotime($item->tgl_realisasi) - strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24)); // Konversi detik ke hari
                                                    @endphp
                                                    <b class="text-green">{{ $hari }} hari</b>
                                                @else
                                                    @php
                                                        $hari = strtotime(now()) - strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24)); // Konversi detik ke hari
                                                    @endphp
                                                    <b class="text-red">{{ $hari }} hari</b>
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="17">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i>&nbsp; Export Data
                                </button>

                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>

                            </div>

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>

                    <p class="text-red" style="margin-top:-10px;">Jika tanggal realisasi tidak ada, estimasi dihitung sejak tanggal pendaftaran sampai dengan hari ini</p>

                </div>
            </div>
        </section>
    </div>
@endsection
@push('myscript')
    <script>
        $('.cgc').select2()
        $('.res').select2()
    </script>
@endpush
