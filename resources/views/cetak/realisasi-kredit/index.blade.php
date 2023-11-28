@extends('theme.app')
@section('title', 'Realisasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">REALISASI KREDIT</h3>

                            <div class="box-tools">
                                <form href="{{ route('realisasi.kredit') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">PERJANJIAN</th>
                                        <th class="text-center" width="33%">ALAMAT</th>
                                        <th class="text-center" width="18%">PENGAJUAN</th>
                                        <th class="text-center" width="13%">BIAYA</th>
                                        <th class="text-center" style="width: 100px">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">{{ $no }}</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>AN. </b>{{ $item->nama_nasabah }} <br>
                                                <span class="label label-success"
                                                    style="font-size: 12px;">{{ $item->no_spk }}</span>
                                            </td>

                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kode_kantor }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jangka_waktu }} BULAN <br>
                                                <b>PLAFON :</b>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }} <br>
                                                <b>METODE :</b> {{ $item->metode_rps }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                {{-- <b>KREDIT: </b> {{ number_format($item->b_admin + $item->b_provisi, 2) }} --}}

                                                <b>PENALTI &nbsp;&nbsp;&nbsp;: </b> {{ $item->b_penalti }} <br>
                                                <b>S. BUNGA&nbsp;: </b> {{ $item->suku_bunga }}% <br>
                                                <b>PROVISI &nbsp;&nbsp;&nbsp;: </b>
                                                {{ number_format($item->b_provisi, 2) }} <br>
                                                <b>BY ADMIN&nbsp;: </b> {{ number_format($item->b_admin, 2) }} <br>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#bukti-realisasi"
                                                    data-id="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Bukti
                                                        Realisasi</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#konfirmasi"
                                                    data-id="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-green"
                                                        style="width: 120px;hight:100%;">Konfirmasi</span>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="7">Tidak Ada Data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
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
                                    <input type="file" class="form-control" name="foto_pemohon" id="foto_pemohon" hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">FOTO PENDAMPING</span>
                                    <a href="#" class="pull-right fw-bold" id="pendamping">PREVIEW</a>
                                    <input type="text" name="foto2" hidden>
                                    <input type="file" class="form-control" name="foto_pendamping" id="foto_pendamping"
                                        hidden>
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
