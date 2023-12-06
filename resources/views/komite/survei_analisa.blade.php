@section('title', 'Kontrol Survey')
@extends('theme.app')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-hourglass-start"></i>
                            <h3 class="box-title">KONTROL SURVEY</h3>

                            <div class="box-tools">
                                <form action="{{ route('survei.analisa') }}" method="GET">
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
                                        {{-- <th class="text-center" width="40%">ALAMAT</th> --}}
                                        <th class="text-center">DESA</th>
                                        <th class="text-center">KECAMATAN</th>
                                        <th class="text-center" width="5%">PRODUK</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center">SURVEYOR</th>
                                        <th class="text-center" width="8%">SURVEY</th>
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
                                            
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_pengajuan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            
                                            {{-- <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td> --}}
                                            <td style="vertical-align: middle;">{{ $item->kelurahan }}</td>
                                            <td style="vertical-align: middle;">{{ $item->kecamatan }}</td>

                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_produk }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_kantor }}</td>
                                            <td class="text-center text-uppercase" style="vertical-align: middle;">{{ $item->surveyor }}</td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if(strtotime($item->tgl_survei) < strtotime(date('Y-m-d')))
                                                    <span class="label label-danger" style="font-size: 10px;">
                                                        &nbsp;&nbsp;&nbsp; JADUL &nbsp;&nbsp;&nbsp;
                                                    </span>
                                                @else
                                                    {{ \Carbon\Carbon::parse($item->tgl_survei)->format('Y-m-d') }}    
                                                @endif

                                            </td>

                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center"  style="vertical-align: middle;">
                                                <a @can('penjadwalan survey') data-toggle="modal" data-target="#modal-penjadwalan" data-id="{{ $item->kode_pengajuan }}" @endcan class="btn-circle btn-sm bg-yellow" title="Penjadwalan">
                                                    <i class="fa fa-calendar"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue" title="Informasi">
                                                    <i class="fa fa-eye"></i>
                                                </a>
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
                                                                </div>
                                        
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>JANGKA WAKTU</label>
                                                                        <input type="text" class="form-control" value="{{ $item->jk }} BULAN - {{ $item->metode_rps }}">
                                                                    </div>
                                        
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>SURVEYOR</label>
                                                                        <input type="text" class="form-control text-uppercase" value="{{ $item->full_name }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>TRACKING</label>
                                                                        <input type="text" class="form-control text-uppercase" value="{{ $item->tracking }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>ALAMAT LENGKAP</label>
                                                                        <textarea class="form-control" rows="2">{{ $item->alamat_ktp }}</textarea>
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
                                            <td class="text-center" colspan="12">TIDAK ADA DATA</td>
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

                    <p class="text-red" style="margin-top:-5px;">
                        <b>JADUL</b> = Jika tgl. survey kurang dari tgl hari ini.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-penjadwalan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PENJADWALAN SURVEY</h4>
                </div>
                <form action="{{ route('analisa.updatepenjadwalan') }}" method="POST">
                    @method('put')
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <input type="text" name="alamat" id="alamat" hidden>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KODE PENGAJUAN</label>
                                    <input type="text" class="form-control" name="kode_pengajuan" id="kode_pengajuan"
                                        readonly>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>NAMA NASABAH</label>
                                    <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah"
                                        readonly>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>NAMA SURVEYOR</label>
                                    <select class="form-control petugas" style="width: 100%;" name="kode_petugas"
                                        id="kode_petugas">
                                        <option value="">--PILIH--</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TGL. SURVEY</label>
                                    <input type="date" class="form-control" name="tgl_survei"
                                        id="datepicker-tanggal-survei" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>TGL. JADUL 1</label>
                                    <input type="date" class="form-control" name="tgl_jadul_1"
                                        id="datepicker-tanggal-survei1">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>TGL. JADUL 2</label>
                                    <input type="date" class="form-control" name="tgl_jadul_2"
                                        id="datepicker-tanggal-survei2">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:-10px;">
                                    <label>CATATAN</label>
                                    <div class="form-control" id="catatan" style="height: 75px;"></div>
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
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/penjadwalan.js') }}"></script>
    <script>
        //Initialize Select2 Elements
        $('.petugas').select2()
    </script>
@endpush
