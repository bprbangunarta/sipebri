@extends('theme.app')
@section('title', 'Notifikasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-bell"></i>
                            <h3 class="box-title">NOTIFIKASI KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('notifikasi_kredit') }}" method="GET">
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
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="42%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="10%">AKSI</th>
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

                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_pengajuan }} </td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }} </td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}</td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#generate-code"
                                                data-id="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-green" title="Generate">
                                                    <i class="fa fa-file-text"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue" title="Catatan">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                {{-- <a data-toggle="modal" data-target="#catatan"
                                                data-id="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue" title="Catatan">
                                                    <i class="fa fa-comment"></i>
                                                </a> --}}
                                            </td>

                                            {{-- MODAL INFO --}}
                                            <div class="modal fade" id="info-{{ $item->kode_pengajuan }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-blue">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">INFORMASI PENGAJUAN</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>NAMA NASABAH</label>
                                                                        <input type="text" class="form-control"
                                                                        value="{{ $item->nama_nasabah }} - {{ $item->kategori }}">
                                                                    </div>
                                                                    
                                                                    <div class="form-group"  style="margin-top:-10px;">
                                                                        <label>PRODUK KREDIT</label>
                                                                        <input type="text" class="form-control" value="{{ $item->produk_kode }} - {{ $item->nama_produk }}">
                                                                    </div>
                                        
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PLAFON KREDIT</label>
                                                                        <input type="text" class="form-control" value="{{ number_format($item->plafon, 0, ',', '.') }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>JANGKA WAKTU</label>
                                                                        <input type="text" class="form-control" value="{{ $item->jangka_waktu }} BULAN - {{ $item->metode_rps }}">
                                                                    </div>
                                                                </div>
                                        
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>SUKU BUNGA</label>
                                                                        <input type="text" class="form-control" value="{{ $item->suku_bunga }}%">
                                                                    </div>
                                        
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>BIAYA ADMIN (%)</label>
                                                                        <input type="text" class="form-control" value="{{ number_format($item->b_admin, 2) }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>BIAYA PROVISI (%)</label>
                                                                        <input type="text" class="form-control text-uppercase" value="{{ number_format($item->b_provisi, 2) }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>BIAYA PENALTI (%)</label>
                                                                        <input type="text" class="form-control text-uppercase" value="{{ $item->b_penalti }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                        
                                                        <div class="modal-footer" style="margin-top: -10px;">
                                                            <button type="submit" class="btn bg-blue" data-dismiss="modal" style="width: 100%;">TUTUP</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL INFO --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="8">Tidak Ada Data.</td>
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

    <div class="modal fade" id="generate-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">GENERATE NOMOR</h4>
                </div>
                <form action="{{ route('simpan.notifikasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="kode" hidden>
                                    <input type="text" name="nomor" id="nomor" hidden>
                                    <input class="form-control text-uppercase" type="text" name="kode_pengajuan"
                                        id="kd_pengajuan" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KODE NOTIFIKASI</span>
                                    <input class="form-control text-uppercase" name="kode_notifikasi" id="generate"
                                        type="text" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-green">GENERATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN</h4>
                </div>
                <form action="{{ Route('simpan.catatan_notifikasi_kredit') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KETERANGAN BY PHONE</span>
                                    <input type="text" id="kodes" name="kode_pengajuan" hidden>

                                    <textarea class="form-control" name="keterangan" id="keterangan" rows="5"></textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">RENCANA REALISASI</span>
                                    <textarea class="form-control" name="rencana_realisasi" id="rencana_realisasi" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" id="smb" class="btn btn-warning">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/generate_kode_notifikasi.js') }}"></script>
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });

        $("a[data-target='#catatan']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');

            $('#kodes').val(kode);
        });
    </script>
@endpush
