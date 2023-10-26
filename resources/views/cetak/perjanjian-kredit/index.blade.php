@extends('theme.app')
@section('title', 'Permohonan Analisa')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PERJANJIAN KREDIT</h3>
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
                                                <b>KREDIT: </b> 5.00%
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">

                                                {{-- <a data-toggle="modal" data-target="#generate-code"
                                                    data-id="{{ $item->kode_pengajuan }}"
                                                    class="btn-circle btn-sm btn-warning" title="Input Analisa">
                                                    <i class="fa fa-file-text-o"></i>
                                                </a> --}}
                                                <button data-toggle="modal" data-target="#generate-code"
                                                    class="btn btn-sm btn-warning" data-id="{{ $item->kode_pengajuan }}">
                                                    <i class="fa fa-file-text-o"></i>
                                                </button>

                                                &nbsp;
                                                @if (is_null($item->no_spk))
                                                    <a href="#" class="btn btn-sm btn-white" title="Cetak Analisa">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('analisa5c.analisa', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        class="btn btn-sm btn-primary" title="Cetak Analisa">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @endif
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

    <div class="modal fade" id="generate-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">GENERATE CODE</h4>
                </div>
                <form action="{{ Route('simpan.spk') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="kode" hidden>
                                    <input type="text" id="nomor" name="nomor" hidden>
                                    <input class="form-control text-uppercase" type="text" name="kode_pengajuan"
                                        id="kd_pengajuan" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KODE PERJANJIAN KREDIT</span>
                                    <input class="form-control text-uppercase" name="kode_spk" id="generate" type="text"
                                        readonly>
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
