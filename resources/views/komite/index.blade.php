@extends('theme.app')
@section('title', 'Input Persetujuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">INPUT PERSETUJUAN</h3>

                            <div class="box-tools">
                                <form action="{{ route('komite.kredit') }}" method="GET">
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
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_kantor }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-persetujuan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-green" title="Persetujuan">
                                                    <i class="fa fa-check-circle"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#modal-catatan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-yellow" title="Catatan">
                                                    <i class="fa fa-file-text"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-blue" title="Jadwal Ulang">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                &nbsp;
                                                <a href="{{ route('cetak.analisa_kredit', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                    target="_blank" class="btn-circle btn-sm btn-info" title="Analisa">
                                                    <i class="fa fa-file-text"></i>
                                                </a>
                                            </td>

                                            {{-- MODAL INFO --}}
                                            <div class="modal fade" id="info-{{ $item->kode_pengajuan }}">
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
                                                                            value="{{ $item->nama_nasabah }} - {{ $item->kategori }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PRODUK KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->produk_kode }} - {{ $item->nama_produk }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PLAFON KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ number_format($item->plafon, 0, ',', '.') }}">
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

    <div class="modal fade" id="modal-persetujuan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PERSETUJUAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div class="div-left">
                                    <div style="margin-top: -20px;">
                                        <span class="fw-bold">MAX PLAFON</span>
                                        <input type="text" name="kode_pengajuan" id="pengajuan" hidden>
                                        <input class="form-control text-uppercase" type="text" name="max_plafon"
                                            value="" id="max" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">BIAYA PROVISI (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="b_provisi"
                                            value="" id="provisi" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">SUKU BUNGA (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="suku_bunga"
                                            id="bunga" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">KEPUTUSAN KOMITE</span>
                                        <select class="form-control text-uppercase" style="width:100%;"
                                            name="putusan_komite" id="komite" required>

                                        </select>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -20px;">
                                        <span class="fw-bold">METODE RPS</span>
                                        <select class="form-control" name="metode_rps" id="metode" required>
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">BIAYA ADMIN (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="b_admin"
                                            value="" id="admin" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">RC</span>
                                        <input class="form-control text-uppercase" type="text" name="rc"
                                            id="rc" readonly required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">USULAN PLAFON</span>
                                        <input class="form-control text-uppercase" type="text" name="usulan_plafon"
                                            placeholder="RP." id="usulan_plafon" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN KOMITE</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="catatan" id="catatan" required></textarea>
                                </div>
                            </div>
                        </div>

                        <p style="font-size: 14px; color:red;">*Untuk pengajuan <b>TOLAK/BATAL</b> isi <b>USULAN PLAFON</b>
                            sesuai dengan
                            nominal permohonan</p>
                    </div>

                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-green">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">STAFF ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="staff_analis" readonly>Catatan dari staff analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KASI ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kasi_analiss" readonly>Catatan dari kasi analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KABAG ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kabag_analis" readonly>Catatan dari kabag analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">DIREKSI</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="direksi" readonly>Catatan dari direksi</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn bg-yellow" style="width: 100%;"
                            data-dismiss="modal">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/persetujuan_komite.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/catatan_komite.js') }}"></script>
@endpush
