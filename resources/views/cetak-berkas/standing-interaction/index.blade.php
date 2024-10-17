@extends('theme.app')
@section('title', 'Standing Interaction')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK STANDING INSTRUCTION</h3>

                            <div class="box-tools">
                                <form action="{{ route('cetak.lembar.konfirmasi') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
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
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="7%">NO. SPK</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="35%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        @if (Auth::user()->kantor_kode == 'KJT' || Auth::user()->kantor_kode == 'KJT')
                                            <th class="text-center" width="5%">PMK</th>
                                            <th class="text-center" width="5%">WNY</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }} <br>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->no_spk }}
                                            </td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_kantor }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            @if (Auth::user()->kantor_kode == 'KJT')
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="{{ route('cetak.data.standing.interaction', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        target="_blank" class="btn-circle btn-sm bg-blue">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="{{ route('cetak.data.standing.interaction.wanayasa', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        target="_blank" class="btn-circle btn-sm bg-blue">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            @elseif(Auth::user()->kantor_kode == 'PGD')
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="{{ route('cetak.data.standing.interaction', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        target="_blank" class="btn-circle btn-sm bg-blue">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="{{ route('cetak.data.standing.interaction.wanayasa', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        target="_blank" class="btn-circle btn-sm bg-blue">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            @endif
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
                            <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm">
                                <i class="fa fa-download"></i>&nbsp; Export Data
                            </button>

                            <div class="pull-left hidden xs">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>
                            </div>

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                    <p>
                        <b>*</b> Periksa kembali kelengkapan data sesuai kebutuhan. <br>
                        <b>*</b> Periksa kembali <b>PLAFON</b> apakah sudah sesuai.
                    </p>
                    <p style="color: red;">Jika ada kendala hubungi IT, agar ditindak lanjut. (o_o)</p>
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
                <form action="{{ route('export.standing.interaction') }}" method="POST">
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
                                    <select class="form-control produk" name="kode_produk" id=""
                                        style="width: 100%;margin-top:-5px;">
                                        <option value="">--PILIH--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->kode_produk }}">{{ $item->kode_produk }} -
                                                {{ $item->nama_produk }}</option>
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
                                    <select class="form-control kantor" name="nama_kantor" id=""
                                        style="width: 100%;margin-top:-5px;">
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
    <script src="{{ asset('assets/js/myscript/generate_kode_spk.js') }}"></script>
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });
    </script>
@endpush
