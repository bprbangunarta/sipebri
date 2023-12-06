@php
use Illuminate\Support\Facades\DB;
@endphp
@extends('theme.app')
@section('title', 'Surat Penolakan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">SURAT PENOLAKAN</h3>

                            <div class="box-tools">
                                <form action="{{ route('penolakan.pengajuan') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right" style="width: 180px;font-size:11.4px;" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah/ Produk">

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
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="8%">NO. ST</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="35%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="5%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_pengajuan }}</td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (is_null($item->no_penolakan))
                                                    -
                                                @else
                                                    {{ $item->no_penolakan }}
                                                @endif
                                            </td>

                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}</td>

                                            @php
                                            $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right" style="vertical-align: middle;">{{ $item->plafon }}</td>

                                            <td class="text-center"  style="vertical-align: middle;">
                                                @if (is_null($item->no_penolakan))
                                                    <a data-toggle="modal" data-target="#tolak-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-yellow" title="Penjadwalan">
                                                        <i class="fa fa-file-text"></i>
                                                    </a>
                                                @else
                                                    <a data-toggle="modal" data-target="#edit-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-yellow" title="Penjadwalan">
                                                        <i class="fa fa-file-text"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue" title="Jadwal Ulang">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>

                                            {{-- <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('penolakan.edit', ['pengajuan' => $item->kd_pengajuan]) }}">
                                                    <span class="btn bg-red" style="width: 120px;hight:100%;">Input Penolakan</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#jadwal-ulang" data-pengajuan="{{ $item->kode_pengajuan }}" title="Jadwal Ulang">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Lihat Analisa</span>
                                                </a>
                                            </td> --}}

                                            {{-- MODAL TOLAK --}}
                                            <div class="modal fade" id="tolak-{{ $item->kode_pengajuan }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-yellow">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">SURAT PENOLAKAN</h4>
                                                        </div>

                                                        <form action="{{ route('penolakan.tambah') }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        {{-- Hiden --}}
                                                                        <input type="text" name="kode_pengajuan" id="kode_pengajuan" value="{{ $item->kode_pengajuan }}" hidden>
    
                                                                        <div class="form-group">
                                                                            <label>NOMOR SURAT</label>
                                                                            @php
                                                                                $lastNomor = DB::table('data_penolakan')->max('nomor');
                                                                                $nextNomor = $lastNomor + 1;
                                                                                $nomorBaru = str_pad($nextNomor, 4, '0', STR_PAD_LEFT);
                                                                            @endphp

                                                                            <input type="text" name="nomor" value="{{ $nomorBaru }}" hidden>

                                                                            <input type="text" class="form-control" style="margin-top: -5px;" name="no_penolakan" value="{{ $nomorBaru }}/03/KABAG.ANALIS/PBA/XII/2023" readonly>
                                                                        </div>
    
                                                                        <div class="form-group" style="margin-top: -10px;">
                                                                            <label>NAMA NASABAH</label>
                                                                            <input type="text" class="form-control" style="margin-top: -5px;" value="{{ $item->nama_nasabah }}" readonly>
                                                                        </div>
    
                                                                        <div class="form-group" style="margin-top: -10px;">
                                                                            <label>ALASAN PENOLAKAN</label>
                                                                            <select class="form-control text-uppercase" style="margin-top: -5px;" name="alasan" id="alasan" required>
                                                                                <option value="">--PILIH--</option>
                                                                                @foreach ($alasan as $item)
                                                                                <option value="{{ $item->id }}">{{ $item->alasan }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
    
                                                                        <div class="form-group" style="margin-top: -10px;">
                                                                            <label>KETERANGAN INTERNAL</label>
                                                                            <textarea class="form-control" name="keterangan" id="keterangan"rows="3" style="margin-top:-5px;" required>{{ old('keterangan') }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                            
                                                            <div class="modal-footer" style="margin-top: -10px;">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                                                                <button type="submit" class="btn btn-warning">SIMPAN</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL TOLAK --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="9">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
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