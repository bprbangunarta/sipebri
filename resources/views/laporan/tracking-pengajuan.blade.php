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
                                        <th class="text-center">JK</th>
                                        <th class="text-center">RATE</th>
                                        <th class="text-center">SURVEYOR</th>
                                        <th class="text-center">SURVEY</th>
                                        <th class="text-center">ANALISA</th>
                                        <th class="text-center">PUTUSAN</th>
                                        <th class="text-center">TGL NOTIF</th>
                                        <th class="text-center">REALISASI</th>
                                        <th class="text-center">PEND - SURVEI</th>
                                        <th class="text-center">SURVEI - ANALISA</th>
                                        <th class="text-center">ANALISA - PUTUSAN</th>
                                        <th class="text-center">PUTUSAN - NOTIF</th>
                                        <th class="text-center">NOTIF - REALISASI</th>
                                        <th class="text-center">TRACKING</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">DEVIASI</th>
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
                                                @if (is_null($item->tgl_notif))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_notif)->format('d-m-Y') }}
                                                @endif

                                            </td>

                                            <td class="text-center">
                                                @if (is_null($item->tgl_realisasi))
                                                    -
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_realisasi)->format('d-m-Y') }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->deviasi_pend_survei }} Hari</td>
                                            <td class="text-center">{{ $item->deviasi_survei_analisa }} Hari</td>
                                            <td class="text-center">{{ $item->deviasi_analisa_persetujuan }} Hari</td>
                                            <td class="text-center">{{ $item->deviasi_persetujuan_notif }} Hari</td>
                                            <td class="text-center">{{ $item->deviasi_notif_realisasi }} Hari</td>
                                            <td class="text-center">
                                                @if (
                                                    $item->tracking == 'Penjadwalan' ||
                                                        $item->tracking == 'Proses Survei' ||
                                                        $item->tracking == 'Proses Analisa' ||
                                                        $item->tracking == 'Naik Kasi' ||
                                                        $item->tracking == 'Naik Komite I' ||
                                                        $item->tracking == 'Naik Komite II' ||
                                                        $item->tracking == 'Naik Komite III')
                                                    {{ $item->tracking }}
                                                @elseif($item->tracking == 'Selesai' || $item->tracking == 'Realisasi')
                                                    {{ $item->tracking }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->status == 'Disetujui' || $item->status == 'Ditolak' || $item->status == 'Dibatalkan')
                                                    {{ $item->status }}
                                                @elseif($item->tracking == 'Realisasi')
                                                    Disetujui
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($item->deviasi))
                                                    @if (in_array($item->deviasi, ['NOTIF EXPIRED', 'REGISTRATION EXPIRED']))
                                                        <b class="text-red">{{ $item->deviasi }}</b>
                                                    @else
                                                        {{ $item->deviasi }}
                                                    @endif
                                                @else
                                                    @php
                                                        $hari = strtotime(now()) - strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24));
                                                    @endphp
                                                    {{ $hari }} hari
                                                @endif
                                                {{-- @if ($item->status == 'Ditolak' || $item->status == 'Dibatalkan')
                                                    @php
                                                        $hari =
                                                            strtotime($item->tgl_persetujuan) -
                                                            strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24));
                                                    @endphp
                                                    <b class="text-red">{{ $hari }} hari</b>
                                                @elseif(!is_null($item->tgl_realisasi))
                                                    @php
                                                        $hari =
                                                            strtotime($item->tgl_realisasi) - strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24));
                                                    @endphp
                                                    <b class="text-green">{{ $hari }} hari</b>
                                                @else
                                                    @php
                                                        $hari = strtotime(now()) - strtotime($item->tanggal);
                                                        $hari = floor($hari / (60 * 60 * 24));
                                                    @endphp
                                                    <b class="text-red">{{ $hari }} hari</b>
                                                @endif --}}

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

                    <p class="text-red" style="margin-top:-10px;">Jika tanggal realisasi tidak ada, estimasi dihitung sejak
                        tanggal pendaftaran sampai dengan hari ini</p>

                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="modal-export">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">EXPORT DATA</h4>
                </div>
                <form action="{{ route('export.tracking') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TANGGAL SURVEI MULAI DARI</label>
                                    <input type="date" class="form-control" name="tgl1" id="tgl1"
                                        style="margin-top:-5px;">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>PRODUK</label>
                                    <select class="form-control produk" name="kode_produk" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->kode_produk }}">{{ $item->kode_produk }} -
                                                {{ $item->nama_produk }}</option>
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
                                    <select class="form-control resort" name="resort" id="resort"
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($resort as $item)
                                            <option value="{{ $item->kode_resort }}">{{ $item->nama_resort }}</option>
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
                                    <select class="form-control metode" name="metode" id=""
                                        style="width: 100%;margin-top:-5px;">
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
                                    <label>STATUS</label>
                                    <select class="form-control surveyor" name="status" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibatalkan">Dibatalkan</option>
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
        $('.resort').select2()
        $('.cgc').select2()
        $('.res').select2()
    </script>
@endpush
