@extends('theme.app')
@section('title', 'Input Analisa')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-edit"></i>
                            <h3 class="box-title">INPUT ANALISA</h3>

                            <div class="box-tools">
                                <form action="{{ route('permohonan.analisa') }}" method="GET">
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
                            <table class="table table-bordered" style="font-size:12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="7%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="40%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="13%">AKSI</th>
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
                                                {{ \Carbon\Carbon::parse($item->tgl_daftar)->format('Y-m-d') }}    
                                            </td>
                                            <td class="text-center"  style="vertical-align: middle;">{{ $item->kode_pengajuan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}</td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->tracking == 'Proses Survei')
                                                    <a data-toggle="modal" data-target="#modal-danger" class="btn-circle btn-sm btn-default" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif($item->tracking == 'Proses Analisa')
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}" class="btn-circle btn-sm bg-yellow" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}" class="btn-circle btn-sm bg-green" title="Input">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#jadwal-ulang"
                                                data-pengajuan="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue" title="Jadwal Ulang">
                                                    <i class="fa fa-history"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#tolak-batal" class="btn-circle btn-sm bg-red" title="Tolak dan Batal">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="9">Tidak ada data</td>
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

    <div class="modal fade" id="jadwal-ulang">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">JADWAL ULANG</h4>
                </div>
                <form action="{{ Route('permohonan.simpanjadul') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
                                    <input class="form-control text-uppercase" type="text" value="123456789S"
                                        name="kode_pengajuan" id="kd_pengajuan" readonly>
                                    <input type="text" value="" name="tgl_survei" id="tgl_survei" hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" value="ZULFADLI RIZAL" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <textarea class="form-control text-uppercase" name="keterangan" id=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-blue">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tolak-batal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TOLAK / BATAL PENGAJUAN</h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
                                    <input class="form-control text-uppercase" type="text" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" value="" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">BATAL / TOLAK</span>
                                    <select class="form-control text-uppercase" name="status" id="status" required>
                                        <option value="">--PILIH--</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibatalakan">Dibatalkan</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <textarea class="form-control text-uppercase" name="keterangan" id="" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">INPUT ANALISA</h4>
                </div>
                
                <div class="modal-body">
                    <p>Mohon maaf input analisa kredit tidak bisa dilakukan karena belum melakukan survey, silahkan upload foto survey melalui apliasi client sipebri. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn btn-default" style="width: 100%;" data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/permintaan_jadul.js') }}"></script>
@endpush
