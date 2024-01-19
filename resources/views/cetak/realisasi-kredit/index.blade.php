@extends('theme.app')
@section('title', 'Realisasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-flag"></i>
                            <h3 class="box-title">REALISASI KREDIT</h3>

                            <div class="box-tools">
                                <form href="{{ route('realisasi.kredit') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
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
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NO SPK</th>
                                        <th class="text-center">NAMA LENGKAP</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PDK</th>
                                        <th class="text-center">JW</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center">METODE</th>
                                        <th class="text-center">PINALTI</th>
                                        <th class="text-center">SB</th>
                                        <th class="text-center">PROVISI</th>
                                        <th class="text-center">BY ADM</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                            <td class="text-center">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->no_spk }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-center">{{ $item->kode_kantor }}</td>
                                            <td class="text-center">{{ $item->produk_kode }}</td>
                                            <td class="text-center">{{ $item->jangka_waktu }}</td>
                                            <td class="text-right">
                                                {{ number_format($item->plafon, 0, ',', '.') }}
                                            </td>
                                            <td>{{ $item->metode_rps }}</td>
                                            <td>{{ $item->b_penalti }}</td>
                                            <td>{{ $item->suku_bunga }}</td>
                                            <td>{{ $item->b_provisi }}</td>
                                            <td>{{ $item->b_admin }}</td>
                                            <td>
                                                <a data-toggle="modal" data-target="#bukti-realisasi"
                                                    data-id="{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-blue"
                                                    title="Upload Bukti">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#konfirmasi"
                                                    data-id="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm bg-green" title="Konfirmasi">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
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

    <div class="modal fade" id="bukti-realisasi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">BUKTI REALISASI</h4>
                </div>
                <form action="{{ route('simpan.realisasi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">FOTO PEMOHON</span>
                                    <input type="text" name="kode_pengajuan" id="kd" hidden>
                                    <a href="#" class="pull-right fw-bold" id="pemohon">PREVIEW</a>
                                    <input type="text" name="foto1" hidden>
                                    <input type="file" class="form-control" name="foto_pemohon" id="foto_pemohon"
                                        hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">FOTO PENDAMPING</span>
                                    <a href="#" class="pull-right fw-bold" id="pendamping">PREVIEW</a>
                                    <input type="text" name="foto2" hidden>
                                    <input type="file" class="form-control" name="foto_pendamping"
                                        id="foto_pendamping" hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN</span>
                                    <textarea class="form-control text-uppercase" name="catatan" id="catatan" rows="3"></textarea>
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

    <div class="modal fade" id="konfirmasi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">KONFIRMASI</h4>
                </div>
                <form action="{{ route('konfirmasi.realisasi') }}" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row text-center">
                                <p>APAKAH REALISASI SUDAH SELESAI DILAKUKAN? JIKA YA LAKUKAN KONFIRMASI</p>
                                <p>UNTUK MENYELESAIKAN PROSES PEMBERIAN KREDIT</p>
                                <input type="text" name="kode_pengajuan" id="kodes" hidden>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-success">KONFIRMASI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/generate_kode_realisasi.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/bukti_realisasi.js') }}"></script>
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });

        $("a[data-target='#bukti-realisasi']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kd').val(kode);
        });

        $("a[data-target='#konfirmasi']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kodes').val(kode);
        });
    </script>
@endpush
