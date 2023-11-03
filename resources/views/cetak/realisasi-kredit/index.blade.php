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
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" style="width: 10px">#</th>
                                        <th class="text-center" style="width: 150px">PERJANJIAN</th>
                                        <th class="text-center" style="width: 150px">PENGAJUAN</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center" style="width: 120px">ADMINISTRASI</th>
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
                                                [ {{ $item->kategori }} ]<br>
                                                <b>AN. </b>{{ $item->nama_nasabah }} <br>
                                                {{ $item->no_spk }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>{{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }}</b> <br>
                                                {{ $item->metode_rps }} <br>
                                                <b>{{ $item->produk_kode }}</b> - <b>{{ $item->jangka_waktu }} BULAN</b> -
                                                <b>{{ $item->suku_bunga }}%</b>
                                            </td>

                                            <td style="text-transform: uppercase;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <b>ADM: </b> {{ number_format($item->b_admin, 2) }}%<br>
                                                <b>PROVISI: </b> {{ number_format($item->b_provisi, 2) }}%<br>
                                                <b>KREDIT: </b> {{ number_format($item->b_admin + $item->b_provisi, 2) }}%
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#bukti-realisasi" data-id="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Bukti Realisasi</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#konfirmasi" data-id="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-green" style="width: 120px;hight:100%;">Konfirmasi</span>
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
                <form action="{{ route('simpan.realisasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">FOTO PEMOHON</span>
                                    <input type="file" class="form-control" name="foto_pemohon" id="foto_pemohon">
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">FOTO PENDAMPING</span>
                                    <input type="file" class="form-control" name="foto_pemohon" id="foto_pemohon">
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN</span>
                                    <textarea class="form-control text-uppercase" name="catatan" id="" rows="3"></textarea>
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
                <form action="{{ route('simpan.realisasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row text-center">
                                <p>APAKAH REALISASI SUDAH SELESAI DILAKUKAN? JIKA YA LAKUKAN KONFIRMASI</p>
                                <p>UNTUK MENYELESAIKAN PROSES PEMBERIAN KREDIT</p>                                
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
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });
    </script>
@endpush
